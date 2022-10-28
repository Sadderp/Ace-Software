<?php




    function check_page_deletion($page_id) {
        // Prepared statement
        $sql = "SELECT deleted FROM wiki_page WHERE ID = ?";
        $stmt = $GLOBALS['conn']->prepare($sql);
        $stmt->bind_param("i",$page_id);

        
        $stmt->execute();
        $result = mysqli_fetch_assoc($stmt->get_result())['deleted'];
        if($result == 1) {
            return true;
        }
        return false;
    }



?>