
<?php

    /**
     * Check a $_GET variable and return its value if it has one. Else returns false.
     *
     * @param   string  $variable   The variable name to be checked   
     *
     */
    function get_if_set($variable) {
        if(isset($_GET[$variable])) {
            return $_GET[$variable];
        } else {
            return false;
        }
    }

    /**
     * Give a JSON error message and exit the program
     *
     * @param   string  $version    The program version
     * @param   string  $msg        Error message
     *
     */
    function error_message($version,$msg) {
        $result = ["version"=>$version, "status"=>"ERROR", "data"=>$msg];
        die(json_encode($result));
    }


    /**
     * Check if a user has end-user permissions for a service
     *
     * @param   int     $user_id        ID of the user
     * @param   int     $service_id     ID of the service
     * @return  bool
     *
     */
    function check_end_user($user_id,$service_id) {
        $stmt = $GLOBALS['conn']->prepare("SELECT * FROM end_user WHERE userID = ? AND serviceID = ?");
        $stmt->bind_param("ii",$user_id,$service_id);
        $stmt->execute();

        if($stmt->get_result()->num_rows == 0) {
            return false;
        } else {
            return true;
        }
    }
?>

