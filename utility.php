
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
    function error_message($msg) {
        $result = ["Version"=>$GLOBALS['version'], "Status"=>"Error", "Data"=>$msg];
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

    /**
     * Get the account ID from its username
     *
     * @param   int     $username        name of the user
     * @return  int
     *
     */
    function id_from_username($username) {
        $sql = "SELECT ID FROM user WHERE username = ?";
        $stmt = $GLOBALS['conn']->prepare($sql);
        $stmt->bind_param("s",$username);
        $stmt->execute();

        $user_id = mysqli_fetch_assoc($stmt->get_result())['ID'];

        if(!$user_id) {
            return 0;
        }

        return $userID;
    }

    /**
     * Get the account username from its ID
     *
     * @param   int     $user_id        ID of the user
     *
     */
    function username_from_id($user_id) {
        $sql = "SELECT username FROM user WHERE ID = ?";
        $stmt = $GLOBALS['conn']->prepare($sql);
        $stmt->bind_param("i",$user_id);
        $stmt->execute();

        $username = mysqli_fetch_assoc($stmt->get_result())['username'];

        if(!$username) {
            return 0;
        }

        return $username;
    }

    /**
     * If the given service is of the given type, return true. Else return false.
     *
     * @param   int     $service_id     ID of the service
     * @param   string  $type           Desired service type ('wiki', 'blog', or 'calendar')
     * @return  boolean
     */
    function verify_service_type($service_id,$type) {
        // Prepared statement
        $sql = "SELECT type FROM service WHERE ID = ?";
        $stmt = $GLOBALS['conn']->prepare($sql);
        $stmt->bind_param("i",$service_id);

        // Get service type
        $stmt->execute();
        $service_type = mysqli_fetch_assoc($stmt->get_result())['type'];

        // Compare service type to desired type. Return true if match
        if($service_type == $type) {
            return true;
        }

        return false;
    }
?>

