<?php



//==================================================
// Calls the databas
//==================================================

require_once("db.php");

$version = "0.0.1";


//==================================================
// Array with all the users IDs
//==================================================
$list = [];



//==================================================
// Looks if all users tokens is up to date
//==================================================
$stmt = $conn->prepare("SELECT id,token, end_date FROM user");
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $list[] = $row;
    }
}


foreach($list as $x) {
    if($time >= $x['end_date']) {
        $new_time = ((intval(date('H', time())+1)));
        $time = date("Y-m-d $new_time:00:00", time());
        $new_token = bin2hex(random_bytes(32));

        $stmt = $conn->prepare("UPDATE user SET token=?, end_date=? WHERE id=?");
        $stmt->bind_param("ssi", $new_token, $time, $x['id']);
        $stmt->execute();
    }
}



?>