<?php 
if(isset($_GET['id']) && $_GET['id'] > 0){
   
    $sql = "SELECT * FROM `room_list` where `room_id` = '{$_GET['id']}' ";
    $query = $conn->query($sql);
    $data = $query->fetchArray();
    $date_created = new DateTime($data['date_created'], new DateTimeZone('UTC'));
    $date_created->setTimezone(new DateTimeZone('Asia/Manila'));
    $date_created = $date_created->format("M d, Y g:i A");

}else{
    throw new ErrorException("This page requires a valid ID.");
}
$_SESSION['formToken']['rooms'] = password_hash(uniqid(), PASSWORD_DEFAULT);
$_SESSION['formToken']['roomDetails'] = password_hash(uniqid(), PASSWORD_DEFAULT);
$_SESSION['formToken']['comment-form'] = password_hash(uniqid(), PASSWORD_DEFAULT);
?>
<h1 class="text-center fw-bolder">Room Details</h1>
<hr class="mx-auto opacity-100" style="width:50px;height:3px">
<div class="col-lg-8 col-md-10 col-sm-12 mx-auto py-3">
    <div class="card rounded-0 shadow">
        <div class="card-body rounded-0">
            <div class="container-fluid">
                
                <table class="table table-sm table-bordered">
                    <colgroup>
                        <col width="50%">
                        <col width="50%">
                    </colgroup>
                    <tbody>
                        <tr>
                            <td><b>Room #</b></td>
                            <td><?= $data['room_no'] ?? "" ?></td>
                        </tr>
                        <tr>
                            <td><b>Name</b></td>
                            <td><?= $data['name'] ?? "" ?></td>
                        </tr>
                        <tr>
                            <td><b>Description</b></td>
                            <td><?= $data['description'] ?? "N/A" ?></td>
                        </tr>
                        <tr>
                            <td><b>Price</b></td>
                            <td><?= number_format($data['price'] ?? 0.00) ?></td>
                        </tr>
                        <tr>
                            <td><b>Status</b></td>
                            <td>
                            <?php 
                                if(isset($data['status'])){
                                    switch($data['status']){
                                        case 1:
                                            echo "<span class='badge bg-primary border rounded-pill px-3 text-light'>Active</span>";
                                            break;
                                        default:
                                            echo "<span class='badge bg-danger border rounded-pill px-3'>Inactive</span>";
                                            break;
                                    }
                                }
                            ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
                
                <hr>
                <div class="text-center">
                    <a href="./?page=rooms" class="btn btn-sm btn-secondary rounded-0">Back to List</a>
                    <a href="./?page=manage_room&id=<?= $data['room_id'] ?>&toview=true" class="btn btn-sm btn-primary rounded-0">Edit</a>
                    <button type="button" data-id="<?= $data['room_id'] ?>" class="btn btn-sm btn-danger rounded-0 delete_data">Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        $('.delete_data').on('click', function(e){
            e.preventDefault()
            var id = $(this).attr('data-id');
            start_loader()
            var _conf = confirm(`Are you sure to delete this room data? This action cannot be undone`);
            if(_conf === true){
                $.ajax({
                    url:'Master.php?a=delete_room',
                    method:'POST',
                    data: {
                        token: '<?= $_SESSION['formToken']['rooms'] ?>',
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
                            location.replace("./?page=room")
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
    })
</script>
