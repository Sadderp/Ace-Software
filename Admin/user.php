<?php



//==================================================
// Calls the databas
//==================================================
require_once("../db.php");
require_once("../verify_token.php");
require_once("../utility.php");


$del = get_if_set('delete_user_id');
$display_name = get_if_set('display_name');

$user_id = get_if_set('user_id');
$token = get_if_set('token');



//==================================================
// Looks what you have filled in
//==================================================
if(!$user_id || !$token) {
    output_error('You need to fill in user_id & token. If you want to edit someones display name fill in delete_user_id and display_name, if you want to delete a user just fill in delete_user_id');
}

if(!verify_token($user_id,$token)) {
    output_error('The token or user_id is wrong');
}

if(!check_admin($user_id)) {
    output_error('This account is not admin');
}



//==================================================
// If you have not selected a user, show all users
//==================================================
if(!$del) {
    $stmt = $conn->prepare("SELECT * FROM user");
    $stmt->execute();
    $result = $stmt->get_result();

    while($row = $result->fetch_assoc()) {
        $data[] = ['ID'=>$row['ID'], 'Display name'=>$row['displayname'], 'Username'=>$row['username']];
    }
}



//==================================================
// Edit the user if it exists in database
//==================================================
else if($del && $display_name) {
    $stmt = $conn->prepare("UPDATE user set displayname = ? WHERE ID=?");
    $stmt->bind_param("si", $display_name, $del);
    $stmt->execute();
    $result = $stmt->get_result();

    if($stmt->affected_rows == 0) {
        output_error('This user do not exist');
    }
    $data[] = ['You successfully edited user '.$del.'s display name to '.$display_name];
}



//==================================================
// Delete the user if it exists in database
//==================================================
else {
    $stmt = $conn->prepare("DELETE FROM user WHERE ID=?");
    $stmt->bind_param("i", $display_name);
    $stmt->execute();
    $result = $stmt->get_result();

    if($stmt->affected_rows == 0) {
        output_error('This user do not exist');
    }
    $data[] = ['You successfully deleted user '.$del];
}

output_ok($data)

?>
