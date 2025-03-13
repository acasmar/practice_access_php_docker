<?php

function validate_general($e){

    /*
        Usernames can contain characters a-z, 0-9, underscores and periods. 
        The username cannot start with a period nor end with a period. 
        It must also not have more than one period sequentially. 
        Min Length is 6 chars.
        Max length is 32 chars.
    */

    $pattern = "(?!.*\.\.)(?!.*\.$)[^\W][\w.]{6,32}$";

    if (preg_match($pattern, $e) == false){
        
        return  false;
    } else {
        return true;
    }

}