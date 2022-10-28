<?php

    /**
     * get_recent_version
     * 
     * Get the most recent version number for a page
     *
     * @param    int  $page_id      The ID of the page
     * @return   int 
     *
     */
    function get_recent_version($page_id) {

        //==============================
        //    Prepared Statement
        //==============================
        $sql = "SELECT MAX(num) AS 'version_num' FROM wiki_page_version WHERE pageID = ?";
        $stmt_get_version = $GLOBALS['conn']->prepare($sql);
        $stmt_get_version->bind_param("i",$page_id);

        //==============================
        //    Get the most recent version
        //==============================
        $stmt_get_version->execute();
        $v_num = mysqli_fetch_assoc($stmt_get_version->get_result())['version_num'];

        // Default to 0 if no version has been made yet.
        if(!$v_num) {
            return 0;
        }
        return $v_num;
    }
?>