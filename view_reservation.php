<?php 
if(isset($_GET['id']) && $_GET['id'] > 0){
   
    $sql = "SELECT *, (SELECT `room_no` FROM `room_list` where `room_list`.`room_id` = `reservation_list`.`room_id`) as room FROM `reservation_list` where `reservation_id` = '{$_GET['id']}' ";
    $query = $conn->query($sql);
    $data = $query->fetchArray();
    $date_created = new DateTime($data['date_created'], new DateTimeZone('UTC'));
    $date_created->setTimezone(new DateTimeZone('Asia/Manila'));
    $date_created = $date_created->format("M d, Y g:i A");
    $extra_fee = $conn->querySingle("SELECT SUM(`price` * `quantity`) FROM `reservation_fee_list` where `reservation_id` = '{$data['reservation_id']}'");

}else{
    throw new ErrorException("This page requires a valid ID.");
}
$_SESSION['formToken']['reservations'] = password_hash(uniqid(), PASSWORD_DEFAULT);
$_SESSION['formToken']['reservationDetails'] = password_hash(uniqid(), PASSWORD_DEFAULT);
$_SESSION['formToken']['extra-form'] = password_hash(uniqid(), PASSWORD_DEFAULT);
?>
<h1 class="text-center fw-bolder">Reservation Details</h1>
<hr class="mx-auto opacity-100" style="width:50px;height:3px">
<div class="col-lg-12 col-md-12 col-sm-12 mx-auto py-3">
    <div class="card rounded-0 shadow">
        <div class="card-body rounded-0">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                        <table class="table table-sm table-bordered">
                            <colgroup>
                                <col width="50%">
                                <col width="50%">
                            </colgroup>
                            <tbody>
                                <tr>
                                    <td><b>Name</b></td>
                                    <td><?= $data['fullname'] ?? "" ?></td>
                                </tr>
                                <tr>
                                    <td><b>Contact #</b></td>
                                    <td><?= $data['contact'] ?? "" ?></td>
                                </tr>
                                <tr>
                                    <td><b>Check-In Date</b></td>
                                    <td><?= isset($data['from_date']) ? date("M d, Y", strtotime($data['from_date'])) : "" ?></td>
                                </tr>
                                <tr>
                                    <td><b>Check-Out Date</b></td>
                                    <td><?= isset($data['to_date']) ? date("M d, Y", strtotime($data['to_date'])) : "" ?></td>
                                </tr>
                                <tr>
                                    <td><b>Room/Cottage</b></td>
                                    <td><?= $data['room'] ?? "N/A" ?></td>
                                </tr>
                                <tr>
                                    <td><b>Room/Cottage Price</b></td>
                                    <td class="text-end"><?= number_format(($data['room_price'] ?? 0), 2) ?></td>
                                </tr>
                                <tr>
                                    <td><b>Total Extra Fee</b></td>
                                    <td class="text-end"><?= number_format($extra_fee ?? 0.00) ?></td>
                                </tr>
                                <tr>
                                    <td><b>Total Amount</b></td>
                                    <td class="text-end"><?= number_format($data['total'] ?? 0.00) ?></td>
                                </tr>
                                <tr>
                                    <td><b>Payment</b></td>
                                    <td class="text-end"><?= number_format($data['payment'] ?? 0.00) ?></td>
                                </tr>
                                <tr>
                                    <td><b>Balance</b></td>
                                    <td class="text-end"><?= number_format((($data['total'] ?? 0) + ($extra_fee ?? 0)) - ($data['payment'] ?? 0) ,2) ?></td>
                                </tr>
                                <tr>
                                    <td><b>Status</b></td>
                                    <td>
                                    <?php 
                                        if(isset($data['status'])){
                                            switch($data['status']){
                                                case 0:
                                                    echo "<span class='badge bg-primary border rounded-pill px-3'>Pending</span>";
                                                    break;
                                                case 1:
                                                    echo "<span class='badge bg-warning border rounded-pill px-3'>Checked-in</span>";
                                                    break;
                                                case 2:
                                                    echo "<span class='badge bg-success border rounded-pill px-3'>Checked-out</span>";
                                                    break;
                                                default:
                                                    echo "<span class='badge bg-light border rounded-pill px-3'>N/A</span>";
                                                    break;
                                            }
                                        }
                                    ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-12">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Extra Fees</th>
                                    <th>Amount</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $total  = 0; ?>
                                <?php 
                                if(isset($data['reservation_id'])):
                                $rf_qry = $conn->query("SELECT *, (SELECT `name` FROM `fee_list` where `fee_list`.`fee_id` = `reservation_fee_list`.`fee_id` ) as fee_name FROM `reservation_fee_list` where `reservation_id` = '{$data['reservation_id']}'");
                                while($row = $rf_qry->fetchArray(SQLITE3_ASSOC)):
                                    $total += $row['price'] * $row['quantity'];
                                ?>
                                <tr>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-sm btn-outline-danger rounded-0 delete_extra" type="button" data-id='<?= $row['reservation_fee_id'] ?>' title="Delete Extra Fee"><span class="material-symbols-outlined">delete</span></button>
                                        </div>
                                    </td>
                                    <td class=""><?= $row['fee_name'] ?></td>
                                    <td class="text-end"><?= number_format($row['price'], 2) ?></td>
                                    <td class="text-end"><?= number_format($row['quantity'], 2) ?></td>
                                    <td class="text-end"><?= number_format(($row['price'] * $row['quantity']), 2) ?></td>
                                </tr>
                                <?php endwhile; ?>
                                <?php if(!$rf_qry->fetchArray()): ?>
                                    <tr>
                                        <th colspan="5" class="text-center">No Extra Charges</th>
                                    </tr>
                                <?php endif; ?>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-center">Total</th>
                                    <th class="text-end"><?= number_format($total, 2) ?></th>
                                </tr>
                            </tfoot>
                        </table>
                        <div class="col-lg-4 col-md-6 col-sm-12 col-12 mx-auto">
                            <button class="btn btn-outline-primary rounded-pill w-100 d-flex justify-content-center" type="button" id="add_extra_btn">
                                <span class="material-symbols-outlined">add</span>
                                Add Extra
                            </button>
                        </div>
                    </div>
                </div>
                
                <hr>
                <div class="text-center">
                    <a href="./?page=reservations" class="btn btn-sm btn-secondary rounded-0">Back to List</a>
                    <a href="./?page=manage_reservation&id=<?= $data['reservation_id'] ?>&toview=true" class="btn btn-sm btn-primary rounded-0">Edit</a>
                    <button type="button" data-id="<?= $data['reservation_id'] ?>" class="btn btn-sm btn-danger rounded-0 delete_data">Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="extraModal" tabindex="-1" aria-labelledby="extraModalLabel" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-md-down">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="extraModalLabel">Add Extra Fee</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
            <form action="" id="add-extra">
                <input type="hidden" name="formToken" value="<?= $_SESSION['formToken']['extra-form'] ?>">
                <input type="hidden" name="reservation_fee_id">
                <input type="hidden" name="reservation_id" value="<?= $data['reservation_id'] ?? '' ?>">
                <div class="mb-3">
                    <label for="fee_id" class="text-body-tertiary">Extra Charges:</label>
                    <select class="form-select rounded-0" id="fee_id" name="fee_id" required="required">
                        <option value="" disabled <?= !isset($data['fee_id']) ? "selected" : "" ?>>Please Select here.</option>
                        <?php 
                            $fee_qry = $conn->query("SELECT * FROM `fee_list` where `status` = 1");
                            while($row = $fee_qry->fetchArray(SQLITE3_ASSOC)):
                        ?>
                        <option value="<?= $row['fee_id'] ?>" data-price="<?= $row['price'] ?>" <?= isset($data['fee_id']) && $data['fee_id'] == $row['fee_id'] ? "selected" : "" ?>><?= $row['name'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="price" class="text-body-tertiary">Price</label>
                    <input type="number" step="any" class="form-control rounded-0" id="price" name="price" value="" readonly>
                </div>
                <div class="mb-3">
                    <label for="quantity" class="text-body-tertiary">Quantity</label>
                    <input type="number" step="any" class="form-control rounded-0" id="quantity" name="quantity" value="">
                </div>
                <div class="mb-3">
                    <label for="total" class="text-body-tertiary">Total</label>
                    <input type="text" class="form-control rounded-0" id="total" name="total" value="">
                </div>
            </form>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" form="add-extra" class="btn btn-primary">Save</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<script>
    function calc_extra_charge(){
        var fee_price = $('#price').val() || 0
        var quantity = $('#quantity').val() || 0
            fee_price = fee_price > 0 ? fee_price : 0;
            quantity = quantity > 0 ? quantity : 0;
        var total = parseFloat(fee_price) * parseFloat(quantity);
            $('#total').val(total)
    }
    $(function(){
        $('#add_extra_btn').click(function(){
            $('#extraModal').modal('show')
        })
        $('#extraModal').on('bs.hide.modal', e=>{
            $('#add-extra')[0].reset()
        })
        $('#fee_id').change(function(){
            var fee_id = $(this).val()
            var price = $(`#fee_id option[value='${fee_id}']`).attr('data-price')
            $('#price').val(price)
            calc_extra_charge()
        })
        $('#quantity').on('input change', function(){
            calc_extra_charge()
        })
        $('.delete_data').on('click', function(e){
            e.preventDefault()
            var id = $(this).attr('data-id');
            start_loader()
            var _conf = confirm(`Are you sure to delete this reservation data? This action cannot be undone`);
            if(_conf === true){
                $.ajax({
                    url:'Master.php?a=delete_reservation',
                    method:'POST',
                    data: {
                        token: '<?= $_SESSION['formToken']['reservations'] ?>',
                        id: id
                    },
                    dataType:'json',
                    error: err=>{
                        console.error(err)
                        alert("An error occurred.")
                        end_loader()
                    },
                    success:function(resp){
                        if(resp.status == 'success'){
                            location.replace("./?page=reservation")
                        }else{
                            console.error(resp)
                            alert(resp.msg)
                        }
                        end_loader()
                    }
                })
            }else{
                end_loader()
            }
        })
        
        $('.delete_extra').on('click', function(e){
            e.preventDefault()
            var id = $(this).attr('data-id');
            start_loader()
            var _conf = confirm(`Are you sure to delete this reservation extra fee data? This action cannot be undone`);
            if(_conf === true){
                $.ajax({
                    url:'Master.php?a=delete_reservation_fee',
                    method:'POST',
                    data: {
                        token: '<?= $_SESSION['formToken']['reservations'] ?>',
                        id: id
                    },
                    dataType:'json',
                    error: err=>{
                        console.error(err)
                        alert("An error occurred.")
                        end_loader()
                    },
                    success:function(resp){
                        if(resp.status == 'success'){
                            location.reload()
                        }else{
                            console.error(resp)
                            alert(resp.msg)
                        }
                        end_loader()
                    }
                })
            }else{
                end_loader()
            }
        })
        $('#add-extra').submit(function(e){
            e.preventDefault();
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>')
                _el.addClass('pop_msg')
            start_loader()
            $.ajax({
                url:'./Master.php?a=save_reservation_fee',
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
                    $('html, body').scrollTop(0)
                },
                success:function(resp){
                    if(resp.status == 'success'){
                        location.reload();
                    }else{
                        _el.addClass('alert alert-danger')
                    }
                    _el.text(resp.msg)

                    _el.hide()
                    _this.prepend(_el)
                    _el.show('slow')
                    $('html, body').scrollTop(0)
                    end_loader()
                }
            })
        })
    })
</script>
