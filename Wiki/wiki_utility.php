<?php

    /**
     * check_page_deletion
     * 
     * If page is deleted, return true. Else return false.
     *
     * @param    int  $page_id      The ID of the page
     * @return   int 
     *
     */
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

    /**
     * Get and return the properties of a wiki page version and the content assigned to it.
     *
     * @param   int     $page       Page ID
     * @param   int     $version    Page version
     * @return  object
     * 
     */
    function get_version_content($page_id,$v) {
        $data = [
            'version'=>$v, 'user_id'=>null, 'username'=>null, 'date'=>null, 'deletion'=>FALSE
        ];

        //==============================
        //    Prepared statements
        //==============================

        // Get information about a version
        $sql = "SELECT v.userID,v.date,u.username,v.deletion FROM wiki_page_version v
        LEFT JOIN user u ON v.userID = u.ID
        WHERE v.pageID = ? and v.num = ?";
        $stmt_version_info = $GLOBALS['conn']->prepare($sql);
        $stmt_version_info->bind_param("ii",$page_id,$v);

        // Get all content from current version
        $sql = "SELECT contents FROM content WHERE pageID = ? AND versionID = ?";
        $stmt_get_content = $GLOBALS['conn']->prepare($sql);
        $stmt_get_content->bind_param("ii",$page_id,$v);

        //==============================
        //    Get version info
        //==============================
        
        $stmt_version_info->execute();
        
        $result = mysqli_fetch_assoc($stmt_version_info->get_result());
        if($result) {
            $data['user_id'] = $result['userID'];
            $data['username'] = $result['username'];
            $data['date'] = $result['date'];
        } 

        // Check if version is a page deletion
        if($result['deletion'] == 1) {
            $data['deletion'] = TRUE;
            return $data;
        } 

        //==============================
        //    Get content assigned to version
        //==============================
        
        $stmt_get_content->execute();
        $result = $stmt_get_content->get_result();

        $content = [];
        while ($row = mysqli_fetch_assoc($result)) {
            array_push($content,$row['contents']);
        }
        $data['page_content'] = $content;

        $stmt_version_info->close();
        $stmt_get_content->close();

        return $data;
    }

?>