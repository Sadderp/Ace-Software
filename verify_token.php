<?php
    require_once("../db.php");
    require_once("../utility.php");

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

        // Generate a new token if the previous one has expired
        $time = date('Y-m-d H:i:s', time());
        $end_time = $result['end_date'];
        if($time >= $end_time) {
            generate_token($user_id);
            return false;
        }

        // Check if token matches
        if($result['token'] != $token) {
            return false;
        }

        return true;   
    }

    /**
     * Generate a new token for the user and replace the new one
     * 
     * @param   int     $user_id    ID of the user
     */
    function generate_token($user_id) {
        // Generate end time
        $new_hour = ((intval(date('H', time())+1)));
        $end_time = date("Y-m-d $new_hour:00:00", time());

        // Generate token
        $token = bin2hex(random_bytes(32));

        // Update existing token
        $sql = "UPDATE user SET token=?, end_date=? WHERE id=?";
        $stmt = $GLOBALS['conn']->prepare($sql);
        $stmt->bind_param("ssi", $token, $end_time, $user_id);
        $stmt->execute();
    }
?>