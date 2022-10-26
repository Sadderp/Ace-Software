<?php



//==================================================
// Calls the databas
//==================================================
require_once("./db.php");
require_once("./token.php");



//==================================================
// Wiki or Blog
//==================================================
if(!empty($_GET['type']) && !empty($_GET['title'])) {
  $type = $_GET['type'];
  $title = "%".$_GET['title']."%";

  $stmt = $conn->prepare("SELECT * FROM service WHERE type=? AND title LIKE ?");
  $stmt->bind_param("ss", $type, $title);
  $stmt->execute();
}
else if(!empty($_GET['type'])) {
  $type = $_GET['type'];

  $stmt = $conn->prepare("SELECT * FROM service WHERE type=?");
  $stmt->bind_param("s", $type);
  $stmt->execute();
}
//==================================================
// Wiki Page
//==================================================
else if(!empty($_GET['page_title'])) {
  $page_title = "%".$_GET['page_title']."%";

  $stmt = $conn->prepare("SELECT * FROM service INNER JOIN wiki_page ON service.ID=wiki_page.serviceID WHERE wiki_page.title LIKE ?");
  $stmt->bind_param("s", $page_title);
  $stmt->execute();
  $result = $stmt->get_result();

  while($row = $result->fetch_assoc()) {
      $search = array("ID"=>$row["ID"],"ServiceID"=>$row["serviceID"],"Title"=>$row["title"]);
      echo json_encode($search);
  }
}



//==================================================
// Shows the result
//==================================================
if(!empty($_GET['type'])) {
  $stmt->execute();
  $result = $stmt->get_result();

  while($row = $result->fetch_assoc()) {
    $search = array("ID"=>$row["ID"],"Title"=>$row["title"],"Type"=>$row["type"]);
    echo json_encode($search);
  }
}



?>