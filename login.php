<?php



//==================================================
// Calls the databas
//==================================================
require_once "./db.php";
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

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $_SESSION['username'] = $row['username'];
            $_SESSION['password'] = $row['password'];
            if(password_verify($password, $row['password'])) {
                if($row['admin'] == 1) {
                    // admin
                } else if($row['ban'] == 0) {
                    // normal user
                    echo $_SESSION['username'];
                    echo $_SESSION['password'];
                } else {
                    // is banned and is not an admin
                }
            } else {
                // not a user
            }
        }
    } 
    else {
        // not a user
    }
}



?>