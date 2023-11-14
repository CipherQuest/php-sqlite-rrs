<h1 class="text-center fw-bolder">Welcome to Resort Reservation System</h1>
<hr class="mx-auto opacity-100" style="width:50px;height:3px">
<?php 
include_once("./Master.php");
?>
<div class="row justify-content-center">
    <div class="col-lg-4 col-md-6 col-sm-12 col-12">
        <div class="card rounded-0 shadow dash-box">
            <div class="card-body">
                <div class="dash-box-icon">
                    <span class="material-symbols-outlined">pending</span>
                </div>
                <div class="dash-box-title">Pending Reservations</div>
                <div class="dash-box-text"><?= $master->pending_reservations() ?></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-12 col-12">
        <div class="card rounded-0 shadow dash-box">
            <div class="card-body">
                <div class="dash-box-icon">
                    <span class="material-symbols-outlined">login</span>
                </div>
                <div class="dash-box-title">Total Checked-In</div>
                <div class="dash-box-text"><?= $master->checkedin_reservations() ?></div>
            </div>
        </div>
    </div>
</div>