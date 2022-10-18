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


    $sql = "SELECT id,username,password FROM user";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            if(password_verify($password, $row['password']) && $name == $row['username']) {
                if($row['id'] == 1) {
                    echo "admin";
                } else {
                    echo "normal";
                }
            }
        }
    } 
    else {
        echo "0 results";
    }
}



//==================================================
// Shows the result
//==================================================
if(!empty($_GET['type'])) {
  $result = $stmt->get_result();

  while($row = $result->fetch_assoc()) {
    $search = array("ID"=>$row["ID"],"Title"=>$row["title"],"Type"=>$row["type"]);
    echo json_encode($search);
  }
}



?>