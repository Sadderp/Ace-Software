<?php

//==================================================
// Calls the databas
//==================================================
require_once("db.php");
require_once("utility.php");
require_once("verify_token.php");
require_once("token.php");
$version = "0.0.2";
$ok = "OK";
$error = "Error";

//==================================================
//      Get variables
//==================================================
$name = get_if_set('name');
$password = get_if_set('password');

if(!$name or !$password) {
    error_message("Missing input(s) - expected: 'name' and 'password'");
}

// Prepared statement
$stmt = $conn->prepare("SELECT ID,admin,ban,username,password FROM user WHERE BINARY username=?");
$stmt->bind_param("s", $name);
$stmt->execute();
$result = $stmt->get_result();

// Check if account exists
if($result->num_rows == 0) {
    error_message("This account does not exist in the database");
}

$user = mysqli_fetch_assoc($result);

// Check if password input matches stored password
if(!password_verify($password, $user['password'])) {
    $login = ["Version"=>$version,"Status"=>$ok,"Data"=>"Incorrect password"];
    die(json_encode($login));
}

// User is banned
if($user['ban'] == 1) {
    $login = ["Version"=>$version,"Status"=>$ok,"Data"=>"This account is banned"];
    die(json_encode($login));
}

if($user['admin'] == 1) {
    $msg = "You logged in as an admin";
} else {
    $msg = "You logged in as a user";
}
    
$token = generate_token();
replace_token($user['ID'],$token);
$login = ["Version"=>$version,"Status"=>$ok,"Data"=>["msg"=>$msg,"token"=>$token]];
echo json_encode($login);
?>