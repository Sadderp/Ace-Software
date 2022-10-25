
<?php

    /**
    * Check a $_GET variable and return its value if it has one. Else returns false.
    *
    * @param    string  $variable   The variable name to be checked   
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
    * @param    string  $version    The program version
    * @param    string  $msg        Error message
    *
    */
    function error_message($version,$msg) {
        $result = ["version"=>$version, "status"=>"ERROR", "data"=>$msg];
        die(json_encode($result));
    }
?>