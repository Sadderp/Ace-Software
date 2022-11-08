<?php



//==================================================
// Calls the databas
//==================================================
require_once("../db.php");
require_once("../verify_token.php");
require_once("../utility.php");


$edit_id = get_if_set('edit_id');
$new_name = get_if_set('new_display_name');

$user_id = get_if_set('user_id');
$token = get_if_set('token');



//==================================================
// Looks what you have filled in
//==================================================
if(!$edit_id or !$new_name or !$user_id or !$token) {
    output_error("Missing input(s) - expected: 'edit_id', 'new_name', 'user_id' and 'token'");
}

if(!is_numeric($edit_id) or !is_numeric($user_id)) {
    output_error($num_error);
}

if(!verify_token($user_id,$token)) {
    output_error($token_error);
}

if(!check_admin($user_id)) {
    output_error($permission_error);
}

//==================================================
// Edit display name
//==================================================

$stmt = $conn->prepare("UPDATE user set displayname = ? WHERE ID=?");
$stmt->bind_param("si", $new_name, $edit_id);
$stmt->execute();
$result = $stmt->get_result();

if($stmt->affected_rows == 0) {
    output_error('This user do not exist');
}

// Output
$output = ['text'=>"User's name was successfully edited","id"=>$edit_id,"new_display_name"=>$new_name];
output_ok($output)

?>