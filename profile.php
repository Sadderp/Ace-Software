<?php



//==================================================
// Calls the databas
//==================================================
require_once("./db.php");
require_once("./verify_token.php");
require_once("./utility.php");


$display_name = get_if_set('display_name');
$old_password = get_if_set('old_password');
$password = get_if_set('password');
$cpassword = get_if_set('cpassword');
$del = get_if_set('delete');

$user_id = get_if_set('user_id');
$token = get_if_set('token');



//==================================================
// Looks what you have filled in
//==================================================
if((!$display_name OR !$old_password) AND (!$old_password OR !$password OR !$cpassword) AND (!$del)) {
    output_error('You need to fill in display_name and old_password or old_password, password and cpassword or delete');
}

if(!verify_token($user_id,$token)) {
    output_error('The token or user_id is wrong');
}

if($del == 'true' || $del == 1) { // 1/true = 1
    $del = 1;
} else if($del == 'false' || $del == 0) { // 0/false = 0
    $del = 0;
} else if(empty($del)){ // empty = 0
    $del = 0;
} else if($del != 1 && $del != 0 && $del != 'true' && $del != 'false') { // everything else will be error
    output_error('delete needs to be inputed as a 1/true or 0/false');
}

if($del) {
    $stmt = $conn->prepare("DELETE FROM user WHERE ID=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if($stmt->affected_rows == 1) {
        die(output_ok('You successfully deleted your account'));
    }
    else {
        output_error('Something went wrong');
    }
}





//==================================================
// Look if password is wrong
//==================================================
$stmt = $conn->prepare("SELECT * FROM user WHERE ID=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
    
if(!password_verify($old_password, $row["password"])) {
    output_error('Your login information is wrong please try again');
}

if($password !== $cpassword) {
    output_error('Password and cpassword did not match');
}



//==================================================
// If not set
//==================================================
if(!$password && !$cpassword) {
    $password = $old_password;
}
if(!$display_name) {
    $display_name = $row["displayname"];
}



//==================================================
// Makes the changes
//==================================================
$hashed = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("UPDATE user SET displayname=?, password=? WHERE ID=?");
$stmt->bind_param("ssi", $display_name, $hashed, $user_id);
$stmt->execute();

if($stmt->affected_rows == 1) {
    output_ok('You successfully changed your information');
}
else {
    output_error('Something went wrong');
}



?>