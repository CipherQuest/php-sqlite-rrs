<?php 
if(isset($_GET['id']) && $_GET['id'] > 0){
   
    $sql = "SELECT * FROM `reservation_list` where `reservation_id` = '{$_GET['id']}' ";
    $query = $conn->query($sql);
    $data = $query->fetchArray();
    $extra_fee = $conn->querySingle("SELECT SUM(`price` * `quantity`) FROM `reservation_fee_list` where `reservation_id` = '{$data['reservation_id']}'");

}

// Generate Manage reservation Form Token
$_SESSION['formToken']['reservation-form'] = password_hash(uniqid(),PASSWORD_DEFAULT);
?>
<h1 class="text-center fw-bolder"><?= isset($data['reservation_id']) ? "Update Reservation Details" : "Add New Reservation" ?></h1>
<hr class="mx-auto opacity-100" style="width:50px;height:3px">
<div class="col-lg-6 col-md-8 col-sm-12 col-12 mx-auto">
    <div class="card rounded-0">
        <div class="card-body">
            <div class="container-fluid">
                <form action="" id="reservation-form">
                    <input type="hidden" name="formToken" value="<?= $_SESSION['formToken']['reservation-form'] ?>">
                    <input type="hidden" name="reservation_id" value="<?= $data['reservation_id'] ?? '' ?>">
                    <div class="mb-3">
                        <label for="from_date" class="text-body-tertiary">Check-In Date</label>
                        <input type="date" class="form-control rounded-0" id="from_date" name="from_date" required="required" autofocus value="<?= $data['from_date'] ?? "" ?>" >
                    </div>
                    <div class="mb-3">
                        <label for="to_date" class="text-body-tertiary">Check-Out Date</label>
                        <input type="date" class="form-control rounded-0" id="to_date" name="to_date" required="required" value="<?= $data['to_date'] ?? "" ?>" >
                    </div>
                    <div class="mb-3">
                        <label for="fullname" class="text-body-tertiary">Fullname</label>
                        <input type="text" class="form-control rounded-0" id="fullname" name="fullname" required="required" value="<?= $data['fullname'] ?? "" ?>">
                    </div>
                    <div class="mb-3">
                        <label for="contact" class="text-body-tertiary">Contact #</label>
                        <input type="text" class="form-control rounded-0" id="contact" name="contact" required="required" value="<?= $data['contact'] ?? "" ?>">
                    </div>
                    <div class="mb-3">
                        <label for="room_id" class="text-body-tertiary">Room/Cottage:</label>
                        <select class="form-select rounded-0" id="room_id" name="room_id" required="required">
                            <option value="" disabled <?= !isset($data['room_id']) ? "selected" : "" ?>>Please Select here.</option>
                            <?php 
                                $room_qry = $conn->query("SELECT * FROM `room_list` where `status` = 1");
                                while($row = $room_qry->fetchArray(SQLITE3_ASSOC)):
                            ?>
                            <option value="<?= $row['room_id'] ?>" data-price="<?= $row['price'] ?>" <?= isset($data['room_id']) && $data['room_id'] == $row['room_id'] ? "selected" : "" ?>><?= $row['room_no'] . " - " . $row['name'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="total" class="text-body-tertiary">Price</label>
                        <input type="hidden" id="room_price" name="room_price" value="<?= $data['room_price'] ?? 0 ?>">
                        <input type="number" step="any" class="form-control rounded-0" id="total" name="total" value="<?= $data['total'] ?? "" ?>" readonly>
                    </div>
                    <?php if(isset($data['reservation_id'])): ?>
                        <div class="mb-3">
                            <label for="extra_fee" class="text-body-tertiary">Total Extra Fee</label>
                            <input type="number" step="any" class="form-control rounded-0" id="extra_fee" name="extra_fee" value="<?= $extra_fee ?? "" ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="" class="text-body-tertiary">Total Payable Amount</label>
                            <input type="number" step="any" class="form-control rounded-0" name="" value="<?= ($data['total'] + $extra_fee) ?? "" ?>" readonly>
                        </div>
                    <?php endif; ?>
                    <div class="mb-3">
                        <label for="payment" class="text-body-tertiary">Payment Amount</label>
                        <input type="number" step="any" class="form-control rounded-0" id="payment" name="payment" value="<?= $data['payment'] ?? "" ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="remarks" class="text-body-tertiary">Remarks</label>
                        <textarea rows="5" class="form-control rounded-0" id="remarks" name="remarks" required="required" ><?= $data['remarks'] ?? "" ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="text-body-tertiary">Status</label>
                        <select class="form-select rounded-0" id="status" name="status">
                            <option value="0" <?= isset($data['status']) && $data['status'] == 0 ? "selected" : "" ?>>Pending</option>
                            <option value="1" <?= isset($data['status']) && $data['status'] == 1 ? "selected" : "" ?>>Checked-In</option>
                            <option value="2" <?= isset($data['status']) && $data['status'] == 2 ? "selected" : "" ?>>Checked-Out</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
        <div class="card-footer">
            <div class="row justify-content-evenly">
                <button class="btn col-lg-4 col-md-5 col-sm-12 col-12 btn-primary rounded-0" form="reservation-form">Save</button>
                <a class="btn col-lg-4 col-md-5 col-sm-12 col-12 btn-secondary rounded-0" href='./?page=reservations'>Cancel</a>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        $('#from_date').change(function(){
            $('#to_date').attr("min", $(this).val())
        })
        
        $('#to_date').change(function(){
            $('#from_date').attr("max", $(this).val())
        })
        $('#room_id').change(function(){
            var id = $(this).val()
            var price = $(`#room_id option[value="${id}"]`).attr('data-price');

            price = price > 0 ? price : 0;
            $('#total').val(price)
            $('#room_price').val(price)
        })
        $('#reservation-form').submit(function(e){
            e.preventDefault();
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>')
                _el.addClass('pop_msg')
            start_loader()
            $.ajax({
                url:'./Master.php?a=save_reservation',
                method:'POST',
                data:$(this).serialize(),
                dataType:'JSON',
                error:err=>{
                    console.log(err)
                    _el.addClass('alert alert-danger')
                    _el.text("An error occurred.")
                    _this.prepend(_el)
                    _el.show('slow')
                    end_loader();
                    $('.modal').scrollTop(0)
                },
                success:function(resp){
                    if(resp.status == 'success'){
                        if('<?= $_GET['toview'] ?? "" ?>' == ""){
                            location.replace("./?page=reservations");
                        }else{
                            location.replace("./?page=view_reservation&id=<?= $data['reservation_id'] ?? "" ?>");
                        }
                    }else{
                        _el.addClass('alert alert-danger')
                    }
                    _el.text(resp.msg)

                    _el.hide()
                    _this.prepend(_el)
                    _el.show('slow')
                    $('.modal').scrollTop(0)
                    end_loader()
                }
            })
        })
    })
</script>