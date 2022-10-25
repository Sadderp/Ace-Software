<?php



//==================================================
// Calls the databas
//==================================================
require_once("./db.php");
require_once("./token.php");
$version = "0.0.1";



//==================================================
// Looks what you have filled in
//==================================================
if(!empty($_GET['display_name']) && !empty($_GET['name']) && !empty($_GET['password']) && !empty($_GET['cpassword'])) {
    $display_name = $_GET['display_name'];
    $name = $_GET['name'];
    $password = $_GET['password'];
    $cpassword = $_GET['cpassword'];

    $sql = "SELECT * FROM user";
    $result = $conn->query($sql);
        
    $list = [];
    while ($row = $result->fetch_assoc()) {
        $list[] = $row;
    }
        
    foreach($list as $x) {
        if($name == $x["username"] && $password == $x["password"]) {
            // this account already exist
        }
        else if($list[count($list)-1] == $x ) {
            if($password === $cpassword) {
                $stmt = $conn->prepare("INSERT INTO user(displayname, username, password) VALUE(?, ?, ?)");
                $stmt->bind_param("sss", $display_name, $name, password_hash($password, PASSWORD_DEFAULT));
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($conn->query($sql) == TRUE) {
                    header("Location: login.php"); // created account
                }
                else {
                    // i do not know how they will come to here???
                }
            }
            else {
                // cpassword and password is not same
            }
        }
    }
}



?>