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



//==================================================
//      Check if account exists
//==================================================
$stmt = $conn->prepare("SELECT ID,admin,ban,username,password FROM user WHERE BINARY username=?");
$stmt->bind_param("s", $name);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0) {
    output_error("This account does not exist");
}

$user_info = mysqli_fetch_assoc($result);



//==================================================
//      Check if password is correct
//==================================================
if(!password_verify($password, $user_info['password'])) {
    die(output_ok("Incorrect password"));
}



//==================================================
//      Check if banned
//==================================================
if($user_info['ban'] == 1) {
    die(output_ok("This account is banned"));
}



//==================================================
//      Login
//==================================================
if($user_info['admin'] == 1) {
    $msg = "You logged in as an admin";
} else {
    $msg = "You logged in as a user";
}

$token = generate_token();
replace_token($user_info['ID'],$token);

output_ok(['message'=>$msg, 'ID'=>$user_info['ID'], 'token'=>$token]);
?>