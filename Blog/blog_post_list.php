<?php
require_once('../db.php');
require_once('../token.php');
$version = "0.0.1"; // Variable for version (hardcoded)
$error = "Error";

    if (!empty($_GET['blog'])){   
        $blog = $_GET['blog'];    // retrieves data from url and shows which blog you selected.
        
        $stmt = $conn->prepare("SELECT * FROM content INNER JOIN service ON content.serviceID = service.ID INNER JOIN img ON content.imgID = img.ID WHERE content.serviceID = ?");
        $stmt->bind_param("i", $blog);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data = "blog name:{$row['title']} content:{$row['contents']} img url:{$row['img_url']}";   // assosiative array with blog title, content and img url.
                $json_result = ["Version: "=>$version, "Status: "=>"OK", "Data: "=>$data];
                echo json_encode($json_result);     // Prints associative array above in json.
            }
        }
        else {
            $json_array = ["Version: "=>$version,"Type: "=>$error,"Data: "=>'Access denied!'];
            echo json_encode($json_array);
        }
    }

?>