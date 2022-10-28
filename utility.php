
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

    // Keeping this here for now to not break everyone's code.
    function error_message($msg) {
        die(json_encode("!! error_message() IS NO LONGER IN USE, PLEASE SWITCH TO output_error() !!"));
    }

    /**
     * Give a JSON error message and exit the program
     *
     * @param   string  $msg        Error message
     *
     */
    function output_error($msg) {
        $result = ["Version"=>$GLOBALS['version'], "Status"=>"Error", "Data"=>$msg];
        die(json_encode($result));
    }

    /**
     * Echo json data
     *
     * @param   string  $msg        Data
     *
     */
    function output_ok($msg) {
        $result = ["Version"=>$GLOBALS['version'], "Status"=>"OK", "Data"=>$msg];
        echo json_encode($result);
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

    /**
     * If the given user is an admin, return true. Else return false.
     *
     * @param   int     $user_id      ID of the user
     * @return  boolean
     */
    function check_admin($user_id) {
        // Prepared statement
        $sql = "SELECT admin FROM user WHERE ID = ?";
        $stmt = $GLOBALS['conn']->prepare($sql);
        $stmt->bind_param("i",$user_id);

        // Get admin status
        $stmt->execute();
        $admin = mysqli_fetch_assoc($stmt->get_result())['admin'];

        if($admin) {
            return true;
        } 

        return false;
    }

    /**
     * If the account exists, return true. Else return false.
     *
     * @param   int     $user_id      ID of the user
     * @return  boolean
     */
    function verify_account_existance($user_id) {
        // Prepared statement
        $sql = "SELECT * FROM user WHERE ID = ?";
        $stmt = $GLOBALS['conn']->prepare($sql);
        $stmt->bind_param("i",$user_id);

        // Get user
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows == 0) {
            return false;
        }
        return true;
    }

    /**
     * If the account is banned, return true. Else returns false.
     *
     * @param   int     $user_id      ID of the user
     * @return  boolean
     */
    function check_ban_status($user_id) {
        // Prepared statement
        $sql = "SELECT ban FROM user WHERE ID = ?";
        $stmt = $GLOBALS['conn']->prepare($sql);
        $stmt->bind_param("i",$user_id);

        // Get ban status
        $stmt->execute();
        $ban = mysqli_fetch_assoc($stmt->get_result())['ban'];

        if($ban == 1) {
            return true;
        }
        return false;
    }

    /**
     * If the user is the manager of the given service, return true. Else returns false.
     *
     * @param   int     $wiki_id      ID of the wiki
     * @param   int     $user_id      ID of the user
     * @return  boolean
     */
    function check_manager($wiki_id,$user_id) {
        // Prepared statement
        $sql = "SELECT * FROM service WHERE ID = ? AND managerID = ? AND type = 'wiki'";
        $stmt = $GLOBALS['conn']->prepare($sql);
        $stmt->bind_param("ii",$wiki_id,$user_id);

        // Get result
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows == 0) {
            return false;
        }
        return true;
    }
?>

