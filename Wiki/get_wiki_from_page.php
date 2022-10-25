<?php
    require_once("../db.php");
    require_once("../utility.php");

    /**
     * get_wiki_from_page
     * 
     * Get the ID of the wiki service hosting a specific page
     *
     * @param    int  $page_id      The ID of the page
     * @return   int 
     *
     */
    function get_wiki_from_page($page_id) {
        $sql = "SELECT s.ID AS 'wiki_id', s.type FROM wiki_page 
        LEFT JOIN service s ON s.ID = wiki_page.serviceID 
        WHERE wiki_page.ID = ?";
        $stmt = $GLOBALS['conn']->prepare($sql);
        $stmt->bind_param("i",$page_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $r = mysqli_fetch_assoc($result);

        if($result->num_rows == 0 or $r['type'] != "wiki") {
            return 0;
        }

        return $r['wiki_id'];
    }
    

