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

$user_id = get_if_set('user_id');
$token = get_if_set('token');

//==================================================
// Looks what you have filled in
//==================================================
if(!$display_name OR !$old_password OR !$password OR !$cpassword) {
    output_error('You need to fill all the colums. Fill in display_name, old_password, password, cpassword');
}

if(!verify_token($user_id,$token)) {
    output_error('The token or user_id is wrong');
}

$stmt = $conn->prepare("SELECT * FROM user WHERE ID=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
    
if($stmt->affected_rows == 0) {
    output_error('User was not found');
}
    
if(!password_verify($old_password, $row["password"])) {
    output_error('Your login information is wrong please try again');
}

if($password !== $cpassword) {
    output_error('Password and cpassword did not match');
}

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