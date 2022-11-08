<?php



//==================================================
// Calls the databas
//==================================================
require_once("./db.php");
require_once("utility.php");



$type = get_if_set('type');
$title = get_if_set('title');
$page_title = get_if_set('page_title');



//==================================================
// Looks what you have filled in
//==================================================
if(!$type AND !$title AND !$page_title) {
  output_error('You need to fill all the colums. Blog/wiki: type and title, wiki page: page_title');
}



//==================================================
// Wiki or Blog
//==================================================
if($type) {
  $stmt = $conn->prepare("SELECT * FROM service WHERE type=? AND title LIKE ?");
  $title = "%".$title."%";
  $stmt->bind_param("ss", $type, $title);
  $stmt->execute();
  $result = $stmt->get_result();

  
  if($stmt->affected_rows == 0) {
    output_ok('Found no pages');
    die();
  }

  while($row = $result->fetch_assoc()) {
    $search[] = ['ID'=>$row['ID'], 'Type'=>$row['type'], 'Title'=>$row['title']];
  }
  output_ok($search);
}



//==================================================
// Wiki Page
//==================================================
else if($page_title) {
  $stmt = $conn->prepare("SELECT * FROM service INNER JOIN wiki_page ON service.ID=wiki_page.serviceID WHERE wiki_page.title LIKE ?");
  $page_title = "%".$page_title."%";
  $stmt->bind_param("s", $page_title);
  $stmt->execute();
  $result = $stmt->get_result();

  if($stmt->affected_rows == 0) {
    output_ok('Found no pages');
  }

  while($row = $result->fetch_assoc()) {
    $search[] = ['ID'=>$row['ID'], 'serviceID'=>$row['serviceID'], 'Title'=>$row['title']];
  }
  output_ok($search);
}



?>