<?php

//==================================================
// Calls the databas
//==================================================
require_once("db.php");
require_once("utility.php");
require_once("verify_token.php");



//==================================================
//      Get variables
//==================================================

$name = get_if_set('name');
$password = get_if_set('password');

if(!$name or !$password) {
    output_error("Missing input(s) - expected: 'name' and 'password'");
}

// Prepared statement
$stmt = $conn->prepare("SELECT ID,admin,ban,username,password FROM user WHERE BINARY username=?");
$stmt->bind_param("s", $name);
$stmt->execute();
$result = $stmt->get_result();

// Check if account exists
if($result->num_rows == 0) {
    output_error("This account does not exist in th
    e database");
}

$user = mysqli_fetch_assoc($result);

// Check if password input matches stored password
if(!password_verify($password, $user['password'])) {
    die(output_ok("Incorrect password"));
}

// Check if user is banned
if($user['ban'] == 1) {
    die(output_ok("This account is banned"));
}

// Different login message depending on if you're admin
if($user['admin'] == 1) {
    $msg = "You logged in as an admin";
} else {
    $msg = "You logged in as a user";
}
    
// Request token
$token = generate_token();
replace_token($user['ID'],$token);

$login[] = "message"=>$msg,"token"=>$token;
output_ok($login);
?>