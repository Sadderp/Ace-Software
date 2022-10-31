<?php



//==================================================
// Calls the databas
//==================================================
require_once("./db.php");
require_once("./verify_token.php");
require_once("./utility.php");
$version = "0.0.1";
$ok = "OK";
$error = "Error";



$display_name = get_if_set('display_name');
$username = get_if_set('username');
$password = get_if_set('password');
$cpassword = get_if_set('cpassword');

$user_id = get_if_set('userID');
$token = get_if_set('token');

//==================================================
// Looks what you have filled in
//==================================================
if(!$display_name OR !$username OR !$password OR !$cpassword) {
    error_message('You need to fill all the colums. Fill in display_name, username, password, cpassword');
}

if(!verify_token($user_id,$token)) {
    error_message('You need to login as an admin');
}

$stmt = $conn->prepare("SELECT admin FROM user WHERE ID=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$row = $result->fetch_assoc();

if($row[admin] == 1) {
    $stmt = $conn->prepare("SELECT * FROM user");
    $stmt->execute();
    $result = $stmt->get_result();
        
    $list = [];
    while ($row = $result->fetch_assoc()) {
        $list[] = $row;
    }
        
    foreach($list as $x) {
        if($username == $x["username"] && $password == password_verify($password, $x["password"])) {
            // this account already exist
            $sign_up = ["Version"=>$version,"Status"=>$error,"Date"=>"This account already exists"];
            echo json_encode($sign_up);
        }
        else if($list[count($list)-1] == $x ) {
            if($password === $cpassword) {
                $stmt = $conn->prepare("INSERT INTO user(displayname, username, password) VALUE(?, ?, ?)");
                $stmt->bind_param("sss", $display_name, $username, password_hash($password, PASSWORD_DEFAULT));
                $stmt->execute();
    
                if ($stmt->affected_rows == 1) {
                    // created account
                    header("Location: login.php");
                }
                else {
                    // i do not know how they will come to here???
                }
            }
            else {
                // cpassword and password is not same
                $sign_up = ["Version"=>$version,"Status"=>$error,"Date"=>"Password and cpassword did not match"];
                echo json_encode($sign_up);
            }
        }
    }
}



?>