<?php

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
            'version'=>$v, 'user_id'=>null, 'username'=>null, 'date'=>null
        ];

        //==============================
        //    Prepared statements
        //==============================

        // Get information about a version
        $sql = "SELECT v.userID,v.date,u.username FROM wiki_page_version v
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