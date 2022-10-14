<?php
require_once('../db.php');

$post = $_GET['title'];
$sql = "INSERT INTO service(title) VALUES ('$post')";
$result = $conn->query($sql);

if($result){
    $select = "SELECT * FROM service WHERE title = 1";
    $blog = '{"$select":1}';
    $JSON = json_decode($blog, true);

    foreach($JSON as $value => $string){
        echo $value."<br>";
    }
}

?>

