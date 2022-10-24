
<?php

    // If the given $_GET-variable exists, return it. Else return false.
    // Usage: $user_id = get_if_set('user_id');
    function get_if_set($data) {
        if(isset($_GET[$data])) {
            return $_GET[$data];
        } else {
            return false;
        }
    }
?>