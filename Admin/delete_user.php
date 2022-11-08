<?php



//==================================================
// Calls the databas
//==================================================
require_once("../db.php");
require_once("../verify_token.php");
require_once("../utility.php");


$del = get_if_set('delete_user_id');

$user_id = get_if_set('user_id');
$token = get_if_set('token');



//==================================================
// Looks what you have filled in
//==================================================
if(!$user_id or !$token or !$del) {
    output_error('You need to fill all the colums. Fill in user_id & token');
}

if(!is_numeric($del) or !is_numeric($user_id)) {
    output_error($num_error);
}

if(!verify_token($user_id,$token)) {
    output_error($token_error);
}

if(!check_admin($user_id)) {
    output_error($permission_error);
}

//==================================================
// Delete the user if it exists in database
//==================================================

$stmt = $conn->prepare("DELETE FROM user WHERE ID=?");
$stmt->bind_param("i", $del);
$stmt->execute();
$result = $stmt->get_result();

if($stmt->affected_rows == 0) {
    output_error('This user do not exist');
}

// Output
$output = ['text'=>"User was successfully deleted","id"=>$del];
output_ok($output)

?>