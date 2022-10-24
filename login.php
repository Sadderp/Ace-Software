<?php



//==================================================
// Calls the databas
//==================================================
require_once "./db.php";
require_once "./token.php";
$version = "0.0.1";


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

    $_SESSION['user_username'] = $name;
    $_SESSION['password'] = $password;

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            if(password_verify($password, $row['password'])) {
                if($row['admin'] == 1) {
                    // admin
                } else if($row['ban'] == 0) {
                    // normal user
                    echo $_SESSION['user_username'];
                    echo $_SESSION['password'];
                } else {
                    // is banned and is not an admin
                }
            }
        }
    } 
    else {
        // not a user
        echo ("NOT A USER!");
    }
}



?>