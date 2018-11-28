<?php 
// Bibliothek um die Session zu starten
include("SessionStarter.php");

// Bibliothek einiger allgemeinen Funktionen
include_once("Functions.php");

function userLoggedIn() {
    if(checkSession("userToken") && checkSession("userName") && checkSession("user")) {
        $token = getSession("userToken");

        $token = explode("%§",$token);
        if(sizeof($token) == 2) {
            if($token[0] == getSession("userName") && intval($token[1]) % 6 == 0) {
                return true;
            }
        } 
    } 
    return false;
}

?>