<?php



//==================================================
// Calls the databas
//==================================================
require_once("./db.php");
require_once("./verify_token.php");
require_once("./utility.php");
error_reporting(E_ERROR | E_PARSE);



//==================================================
// Variables
//==================================================
$display_name = get_if_set('display_name');
$username = get_if_set('username');
$password = get_if_set('password');
$cpassword = get_if_set('cpassword');
$admin = get_if_set('admin');

$user_id = get_if_set('user_id');
$token = get_if_set('token');



//==================================================
// Looks what you have filled in
//==================================================
if(!$display_name || !$username || !$password || !$cpassword) {
    output_error('You need to fill in display_name, username, password, cpassword');
}

if($admin == 'true' || $admin == 1) { // 1/true = 1
    $admin = 1;
} else if($admin == 'false' || $admin == 0) { // 0/false = 0
    $admin = 0;
} else if(empty($admin)){ // empty = 0
    $admin = 0;
} else if($admin != 1 && $admin != 0 && $admin != 'true' && $admin != 'false') { // everything else will be error
    output_error('Admin needs to be inputed as a 1/true or 0/false');
}

if(!is_numeric($user_id)) {
    output_error('User ID must be an int');
}

if(!verify_token($user_id,$token)) {
    output_error('Your user_id is invalid or your token has expired');
}



//==================================================
// Looks if admin
//==================================================
$stmt = $conn->prepare("SELECT admin FROM user WHERE ID=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if($row['admin'] != 1) {
    output_error('Only admins can create accounts');
}



//==================================================
// Looks if user already exist
//==================================================
$stmt = $conn->prepare("SELECT * FROM user WHERE username=?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if($result->num_rows == 1) {
    output_error('A user with this username already exists');
}



//==================================================
// Looks if password and cpassword is correct
//==================================================
if($password !== $cpassword) {
    output_error('Password and cpassword did not match');
}



//==================================================
// Makes the account
//==================================================
$stmt = $conn->prepare("INSERT INTO user(admin, displayname, username, password) VALUE(?, ?, ?, ?)");
$stmt->bind_param("isss", $admin, $display_name, $username, password_hash($password, PASSWORD_DEFAULT));
$stmt->execute();

if ($stmt->affected_rows == 0) {
    // i do not know how they will come to here???
    output_error('EMMMMMMMMMM WHAT DID YOU DO!!!!!!');
}

$data = ['ID'=>$conn->insert_id, 'Name'=>$username];
output_ok($data);
?>