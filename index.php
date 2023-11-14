<?php 
require_once('auth.php');
require_once('DBConnection.php');
$page = $_GET['page'] ?? 'home';
$title = ucwords(str_replace("_", " ", $page));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= ucwords($title) ?> | RRS</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/custom.css">
    <script src="./js/jquery-3.6.0.min.js"></script>
    <script src="./js/popper.min.js"></script>
    <script src="./js/bootstrap.min.js"></script>
    <script src="./js/script.js"></script>
</head>
<body>
    <main>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary bg-gradient fixed-top mb-5" id="topNavBar">
        <div class="container">
            <a class="navbar-brand" href="./">
            RRS
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($page == 'home')? 'active' : '' ?>" aria-current="page" href="./">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($page == 'rooms')? 'active' : '' ?>" aria-current="page" href="./?page=rooms">Room List</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($page == 'fees')? 'active' : '' ?>" aria-current="page" href="./?page=fees">Fee List</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($page == 'reservations')? 'active' : '' ?>" aria-current="page" href="./?page=reservations">Reservation List</a>
                    </li>
                    <?php if($_SESSION['type'] == 1): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($page == 'users')? 'active' : '' ?>" aria-current="page" href="./?page=users">Users</a>
                    </li>
                    <?php endif; ?>
                    
                </ul>
            </div>
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle bg-transparent  text-light border-0" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                    Hello <?php echo $_SESSION['fullname'] ?>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                    <li><a class="dropdown-item" href="./?page=update_account">Change Password</a></li>
                    <li><a class="dropdown-item" href="./LoginRegistration.php?a=logout">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container-md pt-5 pb-3" id="page-container">
        <div class="my-4">
            <?php if(isset($_SESSION['message']['success'])): ?>
                <div class="alert alert-success py-3 rounded-0">
                    <?= $_SESSION['message']['success'] ?>
                </div>
                <?php unset($_SESSION['message']['success']) ?>
            <?php endif; ?>
            <?php if(isset($_SESSION['message']['error'])): ?>
                <div class="alert alert-danger py-3 rounded-0">
                    <?= $_SESSION['message']['error'] ?>
                </div>
                <?php unset($_SESSION['message']['error']) ?>
            <?php endif; ?>
            <?php include($page.".php");  ?>
        </div>
    </div>
    <footer class="position-fixed bottom-0 w-100 bg-gradient bg-light">
        <div class="lh-1 container py-4">
            <div class="text-center">All rights reserved &copy; <?= date("Y") ?> - RRS(php)</div>
            <div class="text-center">Developed by:<a href="mailto:oretnom23@gmail.com" class='text-body-tertiary'>oretnom23</a></div>
        </div>
    </footer>
    <script>
        $(function(){
        })
    </script>
</body>
</html>