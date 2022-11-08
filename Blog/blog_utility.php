<?php

    /**
     * get_blog_from_content
     * 
     * Get the ID of the blog service that the given content belongs to
     *
     * @param    int  $content_id      The ID of the content
     * @return   int 
     *
     */
function get_blog_from_content($content_id) {
    // Prepared statement
    $sql = "SELECT s.ID AS 'blog_id', s.type FROM content 
    LEFT JOIN service s ON s.ID = content.serviceID 
    WHERE content.ID = ?";
    $stmt = $GLOBALS['conn']->prepare($sql);
    $stmt->bind_param("i",$content_id);

    // Get blog ID
    $stmt->execute();
    $result = $stmt->get_result();
    $r = mysqli_fetch_assoc($result);

    if($result->num_rows == 0 or $r['type'] != "blog") {
        return 0;
    }

    return $r['blog_id'];
}

?>