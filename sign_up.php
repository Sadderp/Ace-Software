<?php



//==================================================
// Calls the databas
//==================================================
require_once "./db.php";
$version = "0.0.1";

$password = "root";
$r = password_hash($password, PASSWORD_DEFAULT);
echo $r;

//==================================================
// Looks what you have filled in
//==================================================
if(!empty($_GET['display_name']) && !empty($_GET['name']) && !empty($_GET['password']) && !empty($_GET['cpassword'])) {
    $name = $_GET['display_name'];
    $password = $_GET['name'];
    $name = $_GET['password'];
    $password = $_GET['cpassword'];

    $stmt = $conn->prepare("SELECT admin,ban,username,password FROM user WHERE BINARY username=?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();
    
        $list = [];
        if(!empty($_POST)){
            $name = $_POST["name"];
            $password = $_POST["password"];
            $cPassword = $_POST["cPassword"];
            $dob = $_POST["dob"];
            $adress = $_POST["adress"];

            $sql = "SELECT * FROM user";
            $result = $conn->query($sql);
            
            while ($row = $result->fetch_assoc()) {
                
                array_push($list,$row);
            }
            

            foreach($list as $x) {
                if($name == $x["name"] && $password == $x["password"]) {
                    ?>
                        <script>
                            location.replace("sign-up/")
                        </script>
                    <?php
                }
                else if($list[count($list)-1] == $x ) {
                    if($password === $cPassword) {
                        $sql = "INSERT INTO user(name, password, dob, adress) VALUE('$name', '$password', '$dob', '$adress')";
                        
                        if ($conn->query($sql) === TRUE) {
                            ?>
                                <script>
                                    location.replace("./")
                                </script>
                            <?php
                        }
                        else {
                            echo "Error: " . $sql . "<br>" . $conn->error;
                        }
                    }
                    else {
                        ?>
                            <script>
                                location.replace("sign-up/")
                            </script>
                        <?php
                    }
                }
            }
        }
}



?>