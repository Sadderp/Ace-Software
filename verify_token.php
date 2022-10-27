<?php
    require_once("db.php");
    require_once("utility.php");

    /**
     * Check if token matches with the user token stored in the database.
     * 
     * @param   int     $user_id    ID of the user
     * @param   string  $token      The token to be compared
     * @return  boolean
     */
    function verify_token($user_id,$token) {
        // Prepared statement for getting the user info
        $sql = "SELECT token,end_date FROM user WHERE ID = ?";
        $stmt = $GLOBALS['conn']->prepare($sql);
        $stmt->bind_param("i",$user_id);

        // Get result
        $stmt->execute();
        $result = mysqli_fetch_assoc($stmt->get_result());

        // Give an error if the return for user is blank.
        if(!$result) {
            error_message("User not found");
        }

        // If the token is expired or the token doesn't match, deny access.
        $time = date('Y-m-d H:i:s', time());
        $end_time = $result['end_date'];
        if($time >= $end_time or $result['token'] != $token) {
            return false;
        }

        return true;   
    }

    /**
     * Replace the existing token and create an end time for it
     * 
     * @param   int     $user_id    ID of the user
     */
    function replace_token($user_id,$token) {
        // Generate end time
        $new_hour = ((intval(date('H', time())+1)));
        $end_date = date("Y-m-d $new_hour:00:00", time());

        // Update existing token
        $sql = "UPDATE user SET token=?, end_date=? WHERE id=?";
        $stmt = $GLOBALS['conn']->prepare($sql);
        $stmt->bind_param("ssi", $token, $end_date, $user_id);
        $stmt->execute();
    }

    /**
     * Generate a new token
     * 
     * @return  string
     */
    function generate_token() {
        $token = bin2hex(random_bytes(32));
        return $token;
    }
?>