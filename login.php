<?php



//==================================================
// Calls the databas
//==================================================
require_once "./db.php";



//==================================================
// Looks what you have filled in
//==================================================
if(!empty($_GET['name']) && !empty($_GET['password'])) {
    $name = $_GET['name'];
    $password = $_GET['password'];


    $stmt = $conn->prepare("SELECT admin,ban,username,password FROM user WHERE BINARY username=?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            if(password_verify($password, $row['password'])) {
                if($row['admin'] == 1) {
                    echo 'hej';// admin
                } else if($row['ban'] == 0) {
                    echo 'hej2';// normal user
                } else {
                    echo 'hej3';// is banned and is not an admin
                }
            } else {
                echo 'hej4';// not a user
            }
        }
    } 
    else {
        echo 'hej5';// not a user
    }
}



?>