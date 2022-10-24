<?php



//==================================================
// Calls the databas
//==================================================
require_once "./db.php";
$version = "0.0.1";



//==================================================
// Looks what you have filled in
//==================================================
$time = date('h:i:s', time());
$list = [];



$sql = "SELECT id,token, end_date FROM user";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $list[] = $row;
    }
} else {
    echo "0 results";
}



foreach($list as $x) {
    if($time >= $x['end_date']) {
        $new_time = (intval($time)+1)*10000;
        $new_token = bin2hex(random_bytes(32));
        $sql = "UPDATE user SET token = '$new_token', end_date = '$new_time' WHERE id=$x[id]";
        $result = $conn->query($sql);
    }
}



?>