<?php 
if(session_id() ==="")
session_start();
require_once('DBConnection.php');
/**
 * Login Registration Class
 */
Class Master extends DBConnection{
    function __construct(){
        parent::__construct();
    }
    function __destruct(){
        parent::__destruct();
    }
    function save_room(){
        if(!isset($_POST['user_id']))
        $_POST['user_id'] = $_SESSION['user_id'];
        foreach($_POST as $k => $v){
            if(!in_array($k, ['formToken']) && !is_array($_POST[$k]) && !is_numeric($v)){
                $_POST[$k] = $this->escapeString($v);
            }
        }
        extract($_POST);
        $allowed_field = ['room_no', 'name', 'description', 'price', 'status'];
        $allowedToken = $_SESSION['formToken']['room-form'];
        if(!isset($formToken) || (isset($formToken) && $formToken != $allowedToken)){
            $resp['status'] = 'failed';
            $resp['msg'] = "Security Check: Form Token is invalid.";
        }else{
            $check = $this->querySingle("SELECT count(`room_id`) FROM `room_list` where `room_no` = '{$room_no}'");
            if($check > 0){
                $resp['status'] = 'failed';
                $resp['msg'] = "Room Number Already Exists.";
            }else{
                $cols=[];
                $vals = [];
                foreach( $_POST as $k => $v ){
                    if(in_array($k, $allowed_field)){
                        $cols[] = $k;
                        $vals[] = $v;
                    }
                }
                if(empty($room_id)){
                    $cols = "`". implode("`, `", $cols) . "`";
                    $vals = "'". implode("', '", $vals) . "'";
                    $sql = "INSERT INTO `room_list` ({$cols}) VALUES ({$vals})";
                }else{
                    $data = '';
                    foreach($cols as $k => $v){
                        if(!empty($data)) $data .= ", ";
                        $data .= " `{$v}` = '{$vals[$k]}' ";
                    }
                    $sql = "UPDATE `room_list` set {$data} where `room_id` = '{$room_id}'";
                }
                $qry = $this->query($sql);
                if($qry){
                    $resp['status'] = 'success';
                    if(empty($room_id)){
                        $resp['msg'] = 'New Room has been addedd successfully';
                    }else{
                        $resp['msg'] = 'Room Details has been updated successfully';
                    }
                    $_SESSION['message']['success'] = $resp['msg'];
                }else{
                    $resp['status'] = 'failed';
                    $resp['msg'] = 'Error:'. $this->lastErrorMsg(). ", SQL: {$sql}";
                }
            }
        }
        return json_encode($resp);
    }
    function delete_room(){
        extract($_POST);
        $allowedToken = $_SESSION['formToken']['rooms'];
        if(!isset($token) || (isset($token) && $token != $allowedToken)){
            $resp['status'] = 'failed';
            $resp['msg'] = "Security Check: Token is invalid.";
        }else{
            $sql = "DELETE FROM `room_list` where `room_id` = '{$id}'";
            $delete = $this->query($sql);
            if($delete){
                $resp['status'] = 'success';
                $resp['msg'] = 'The room data has been deleted successfully';
                $_SESSION['message']['success'] = $resp['msg'];
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = $this->lastErrorMsg();
            }
        }
        return json_encode($resp);
    }
    function save_fee(){
        if(!isset($_POST['user_id']))
        $_POST['user_id'] = $_SESSION['user_id'];
        foreach($_POST as $k => $v){
            if(!in_array($k, ['formToken']) && !is_array($_POST[$k]) && !is_numeric($v)){
                $_POST[$k] = $this->escapeString($v);
            }
        }
        extract($_POST);
        $allowed_field = ['name', 'description', 'price', 'status'];
        $allowedToken = $_SESSION['formToken']['fee-form'];
        if(!isset($formToken) || (isset($formToken) && $formToken != $allowedToken)){
            $resp['status'] = 'failed';
            $resp['msg'] = "Security Check: Form Token is invalid.";
        }else{
            $cols=[];
            $vals = [];
            foreach( $_POST as $k => $v ){
                if(in_array($k, $allowed_field)){
                    $cols[] = $k;
                    $vals[] = $v;
                }
            }
            if(empty($fee_id)){
                $cols = "`". implode("`, `", $cols) . "`";
                $vals = "'". implode("', '", $vals) . "'";
                $sql = "INSERT INTO `fee_list` ({$cols}) VALUES ({$vals})";
            }else{
                $data = '';
                foreach($cols as $k => $v){
                    if(!empty($data)) $data .= ", ";
                    $data .= " `{$v}` = '{$vals[$k]}' ";
                }
                $sql = "UPDATE `fee_list` set {$data} where `fee_id` = '{$fee_id}'";
            }
            $qry = $this->query($sql);
            if($qry){
                $resp['status'] = 'success';
                if(empty($fee_id)){
                    $resp['msg'] = 'New Fee has been addedd successfully';
                }else{
                    $resp['msg'] = 'Fee Details has been updated successfully';
                }
                $_SESSION['message']['success'] = $resp['msg'];
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = 'Error:'. $this->lastErrorMsg(). ", SQL: {$sql}";
            }
        }
        return json_encode($resp);
    }
    function delete_fee(){
        extract($_POST);
        $allowedToken = $_SESSION['formToken']['fees'];
        if(!isset($token) || (isset($token) && $token != $allowedToken)){
            $resp['status'] = 'failed';
            $resp['msg'] = "Security Check: Token is invalid.";
        }else{
            $sql = "DELETE FROM `fee_list` where `fee_id` = '{$id}'";
            $delete = $this->query($sql);
            if($delete){
                $resp['status'] = 'success';
                $resp['msg'] = 'The fee data has been deleted successfully';
                $_SESSION['message']['success'] = $resp['msg'];
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = $this->lastErrorMsg();
            }
        }
        return json_encode($resp);
    }
    function save_reservation(){
        if(!isset($_POST['user_id']))
        $_POST['user_id'] = $_SESSION['user_id'];
        foreach($_POST as $k => $v){
            if(!in_array($k, ['formToken']) && !is_array($_POST[$k]) && !is_numeric($v)){
                $_POST[$k] = $this->escapeString($v);
            }
        }
        extract($_POST);
        $allowed_field = ['from_date', 'to_date', 'room_id', 'room_price', 'fullname', 'contact', 'total', 'payment', 'remarks', 'status'];
        $allowedToken = $_SESSION['formToken']['reservation-form'];
        if(!isset($formToken) || (isset($formToken) && $formToken != $allowedToken)){
            $resp['status'] = 'failed';
            $resp['msg'] = "Security Check: Form Token is invalid.";
        }else{
            $cols=[];
            $vals = [];
            foreach( $_POST as $k => $v ){
                if(in_array($k, $allowed_field)){
                    $cols[] = $k;
                    $vals[] = $v;
                }
            }
            if(empty($reservation_id)){
                $cols = "`". implode("`, `", $cols) . "`";
                $vals = "'". implode("', '", $vals) . "'";
                $sql = "INSERT INTO `reservation_list` ({$cols}) VALUES ({$vals})";
            }else{
                $data = '';
                foreach($cols as $k => $v){
                    if(!empty($data)) $data .= ", ";
                    $data .= " `{$v}` = '{$vals[$k]}' ";
                }
                $sql = "UPDATE `reservation_list` set {$data} where `reservation_id` = '{$reservation_id}'";
            }
            $qry = $this->query($sql);
            if($qry){
                $resp['status'] = 'success';
                if(empty($reservation_id)){
                    $resp['msg'] = 'New reservation has been addedd successfully';
                }else{
                    $resp['msg'] = 'Reservation Details has been updated successfully';
                }
                $_SESSION['message']['success'] = $resp['msg'];
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = 'Error:'. $this->lastErrorMsg(). ", SQL: {$sql}";
            }
        }
        return json_encode($resp);
    }
    function delete_reservation(){
        extract($_POST);
        $allowedToken = $_SESSION['formToken']['reservations'];
        if(!isset($token) || (isset($token) && $token != $allowedToken)){
            $resp['status'] = 'failed';
            $resp['msg'] = "Security Check: Token is invalid.";
        }else{
            $sql = "DELETE FROM `reservation_list` where `reservation_id` = '{$id}'";
            $delete = $this->query($sql);
            if($delete){
                $resp['status'] = 'success';
                $resp['msg'] = 'The reservation data has been deleted successfully';
                $_SESSION['message']['success'] = $resp['msg'];
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = $this->lastErrorMsg();
            }
        }
        return json_encode($resp);
    }
    function save_reservation_fee(){
        if(!isset($_POST['user_id']))
        $_POST['user_id'] = $_SESSION['user_id'];
        foreach($_POST as $k => $v){
            if(!in_array($k, ['formToken']) && !is_array($_POST[$k]) && !is_numeric($v)){
                $_POST[$k] = $this->escapeString($v);
            }
        }
        extract($_POST);
        $allowed_field = ['reservation_id', 'fee_id', 'quantity', 'price'];
        $allowedToken = $_SESSION['formToken']['extra-form'];
        if(!isset($formToken) || (isset($formToken) && $formToken != $allowedToken)){
            $resp['status'] = 'failed';
            $resp['msg'] = "Security Check: Form Token is invalid.";
        }else{
            $cols=[];
            $vals = [];
            foreach( $_POST as $k => $v ){
                if(in_array($k, $allowed_field)){
                    $cols[] = $k;
                    $vals[] = $v;
                }
            }
            if(empty($reservation_fee_id)){
                $cols = "`". implode("`, `", $cols) . "`";
                $vals = "'". implode("', '", $vals) . "'";
                $sql = "INSERT INTO `reservation_fee_list` ({$cols}) VALUES ({$vals})";
            }else{
                $data = '';
                foreach($cols as $k => $v){
                    if(!empty($data)) $data .= ", ";
                    $data .= " `{$v}` = '{$vals[$k]}' ";
                }
                $sql = "UPDATE `reservation_fee_list` set {$data} where `reservation_fee_id` = '{$reservation_fee_id}'";
            }
            $qry = $this->query($sql);
            if($qry){
                $resp['status'] = 'success';
                if(empty($reservation_fee_id)){
                    $resp['msg'] = 'New Reservation Fee has been addedd successfully';
                }else{
                    $resp['msg'] = 'Reservation Fee Details has been updated successfully';
                }
                $_SESSION['message']['success'] = $resp['msg'];
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = 'Error:'. $this->lastErrorMsg(). ", SQL: {$sql}";
            }
        }
        return json_encode($resp);
    }
    function delete_reservation_fee(){
        extract($_POST);
        $allowedToken = $_SESSION['formToken']['reservations'];
        if(!isset($token) || (isset($token) && $token != $allowedToken)){
            $resp['status'] = 'failed';
            $resp['msg'] = "Security Check: Token is invalid.";
        }else{
            $sql = "DELETE FROM `reservation_fee_list` where `reservation_fee_id` = '{$id}'";
            $delete = $this->query($sql);
            if($delete){
                $resp['status'] = 'success';
                $resp['msg'] = 'The Reservation Fee data has been deleted successfully';
                $_SESSION['message']['success'] = $resp['msg'];
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = $this->lastErrorMsg();
            }
        }
        return json_encode($resp);
    }
    function pending_reservations(){
        $total = $this->querySingle("SELECT COUNT(`reservation_id`) FROM `reservation_list` where  `status` = 0");
        return number_format($total);
    }
    function checkedin_reservations(){
        $total = $this->querySingle("SELECT COUNT(`reservation_id`) FROM `reservation_list` where  `status` =1");
        return number_format($total);
    }

}
$a = isset($_GET['a']) ?$_GET['a'] : '';
$master = new Master();
switch($a){
    case 'save_settings':
        echo $master->save_settings();
    break;
    case 'save_room':
        echo $master->save_room();
    break;
    case 'delete_room':
        echo $master->delete_room();
    break;
    case 'save_fee':
        echo $master->save_fee();
    break;
    case 'delete_fee':
        echo $master->delete_fee();
    break;
    case 'save_reservation':
        echo $master->save_reservation();
    break;
    case 'delete_reservation':
        echo $master->delete_reservation();
    break;
    case 'save_reservation_fee':
        echo $master->save_reservation_fee();
    break;
    case 'delete_reservation_fee':
        echo $master->delete_reservation_fee();
    break;
    default:
    // default action here
    break;
}