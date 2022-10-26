<?php



//==================================================
// Calls the databas
//==================================================
require_once("./db.php");
require_once("./token.php");
$version = "0.0.1";
$ok = "OK";
$error = "Error";

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
                    // admin
                    $login = ["Version"=>$version,"Status"=>$ok,"Date"=>"You logged in as an admin"];
                    echo json_encode($login);
                } else if($row['ban'] == 0) {
                    // normal user
                    $login = ["Version"=>$version,"Status"=>$ok,"Date"=>"You logged in as a user"];
                    echo json_encode($login);
                } else {
                    // is banned and is not an admin
                    $login = ["Version"=>$version,"Status"=>$ok,"Date"=>"This account is banned"];
                    echo json_encode($login);
                }
            }
        }
    } 
    else {
        $login = ["Version"=>$version,"Status"=>$error,"Date"=>"This account does not exist in the database"];
        echo json_encode($login);
    }
} else {
    $login = ["Version"=>$version,"Status"=>$error,"Date"=>"You have not filled in name and password"];
    echo json_encode($login);
}



?>