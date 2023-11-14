<?php 
if(isset($_GET['id']) && $_GET['id'] > 0){
   
    $sql = "SELECT * FROM `room_list` where `room_id` = '{$_GET['id']}' ";
    $query = $conn->query($sql);
    $data = $query->fetchArray();

}

// Generate Manage room Form Token
$_SESSION['formToken']['room-form'] = password_hash(uniqid(),PASSWORD_DEFAULT);
?>
<h1 class="text-center fw-bolder"><?= isset($data['room_id']) ? "Update Room Details" : "Add New Room" ?></h1>
<hr class="mx-auto opacity-100" style="width:50px;height:3px">
<div class="col-lg-6 col-md-8 col-sm-12 col-12 mx-auto">
    <div class="card rounded-0">
        <div class="card-body">
            <div class="container-fluid">
                <form action="" id="room-form">
                    <input type="hidden" name="formToken" value="<?= $_SESSION['formToken']['room-form'] ?>">
                    <input type="hidden" name="room_id" value="<?= $data['room_id'] ?? '' ?>">
                    <div class="mb-3">
                        <label for="room_no" class="text-body-tertiary">Room/Cottage Number</label>
                        <input type="text" class="form-control rounded-0" id="room_no" name="room_no" required="required" autofocus value="<?= $data['room_no'] ?? "" ?>" >
                    </div>
                    <div class="mb-3">
                        <label for="name" class="text-body-tertiary">Name</label>
                        <input type="text" class="form-control rounded-0" id="name" name="name" required="required" value="<?= $data['name'] ?? "" ?>">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="text-body-tertiary">Description</label>
                        <textarea rows="5" class="form-control rounded-0" id="description" name="description" required="required" ><?= $data['description'] ?? "" ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="text-body-tertiary">Price</label>
                        <input type="number" step="any" class="form-control rounded-0" id="price" name="price" value="<?= $data['price'] ?? "" ?>">
                    </div>
                    <div class="mb-3">
                        <label for="status" class="text-body-tertiary">Status</label>
                        <select class="form-select rounded-0" id="status" name="status">
                            <option value="0" <?= isset($data['status']) && $data['status'] == 0 ? "selected" : "" ?>>Inactive</option>
                            <option value="1" <?= isset($data['status']) && $data['status'] == 1 ? "selected" : "" ?>>Active</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
        <div class="card-footer">
            <div class="row justify-content-evenly">
                <button class="btn col-lg-4 col-md-5 col-sm-12 col-12 btn-primary rounded-0" form="room-form">Save</button>
                <a class="btn col-lg-4 col-md-5 col-sm-12 col-12 btn-secondary rounded-0" href='./?page=rooms'>Cancel</a>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        $('#room-form').submit(function(e){
            e.preventDefault();
            $('.pop_msg').remove()
            var _this = $(this)
            var _el = $('<div>')
                _el.addClass('pop_msg')
            start_loader()
            $.ajax({
                url:'./Master.php?a=save_room',
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
                        if('<?= $_GET['toview'] ?? "" ?>' == ""){
                            location.replace("./?page=rooms");
                        }else{
                            location.replace("./?page=view_room&id=<?= $data['room_id'] ?? "" ?>");
                        }
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