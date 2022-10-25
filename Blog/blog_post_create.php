<?php
require_once('../db.php');
require_once('../token.php');
$version = "0.0.1";
$ok = "OK";
$error = "Error";


//==================================================
// content table
//==================================================
    if ((!empty($_GET['contents'])) && (!empty($_GET['imgID'])) && (!empty($_GET['serviceID'])) && (!empty($_GET['userID'])))  {    //checks if the if is empty if so "dies". 
                                                                                                                                    
        $contents = $_GET['contents'];
        $imgID = $_GET['imgID'];
        $serviceID = $_GET['serviceID'];
        $userID = $_GET['userID'];

        $username = $_SESSION['user_username'];
        $password = $_SESSION['password'];
        $sql = "SELECT username,password FROM user WHERE BINARY username='".$username."'";
        $result = $conn->query($sql);

        

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                if(password_verify($password, $row['password'])) {
                $stmt = $conn->prepare("INSERT INTO content (contents, imgID, serviceID, userID) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("siii",$contents, $imgID, $serviceID, $userID);
                $stmt->execute(); 
                
                $data = "contents:$contents imgID:$imgID serviceID:$serviceID userID:$userID";
                $json_result = ["Version: "=>$version, "Status: "=>"OK", "Data: "=>$data];
                echo json_encode($json_result);
                }
                else {
                    $json_array = ["Version: "=>$version,"Type: "=>$error,"Data: "=>'Access denied!'];
                    echo json_encode($json_array);
                }
            }
        }

        else {
            $json_array = ["Version: "=>$version,"Type: "=>$error,"Data: "=>'Access denied!'];
            echo json_encode($json_array);

        }
    }
    else {
        $data = "";
        $json_result = ["Version: "=>$version, "Status: "=>"error", "Data: "=>$data];
        echo json_encode($json_result);
    }


//==================================================
// img table
//==================================================
    if (!empty($_GET['contentID']) && (!empty($_GET['img_url']))) {
        $contentID = $_GET['contentID'];
        $img_url = $_GET['img_url'];

        $username = $_SESSION['user_username'];
        $password = $_SESSION['password'];
        $sql = "SELECT username,password FROM user WHERE BINARY username='".$username."'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                if(password_verify($password, $row['password'])) {
                    $stmt = $conn->prepare("INSERT INTO img (contentID, img_url) VALUES (?, ?)");
                    $stmt->bind_param("is", $contentID, $img_url);
                    $stmt->execute();

                    $data = "contentID:$contentID img url:$img_url";
                    $json_result = ["Version: "=>$version, "Status: "=>"OK", "Data: "=>$data];
                    echo json_encode($json_result);
                
                }
                else {
                    $json_array = ["Version: "=>$version,"Type: "=>$error,"Data: "=>'Access denied!'];
                    echo json_encode($json_array);
                }
            } 
        }

        else {
            $json_array = ["Version: "=>$version,"Type: "=>$error,"Data: "=>'Access denied!'];
            echo json_encode($json_array);
        }
    }
    else {
        $data = ""; // If the if fails, it will make $data empty which will prevent it from printing
        $json_result = ["Version: "=>$version, "Status: "=>"Error", "Data: "=>$data];
        echo json_encode($json_result); // prints the json encode and will display the version, status and data.  
    }

?>