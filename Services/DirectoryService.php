<?php
// Bibliothek um die Session zu starten
include("../Common/SessionStarter.php");

// Bibliothek um Benutzerinformationen zu bekommen.
include_once("../Common/UserInfo.php");

// Bibliothek einiger allgemeinen Funktionen
include_once("../Common/Functions.php");


if(userLoggedIn()) {
    echo checkRequest();
}
else {
    echo "Error: Can't find user info";
}

// Diese Funktion prüft, welche Response geschickt werden soll
function checkRequest() {
    switch (getPost("request")) {
         case "fullTree":
            return fullTree();
            break;
        case "delete":
            $path = getPost("path");
            if($path != null) {
                return delete($path);
            }
            break;
        case "deleteMultiple":
            $path = getPost("path");
            if($path != null) {
                return deleteMultiple($path);
            }
            break;    
        case "fileContent":
            $path = getPost("path");
            if($path != null) {
                return fileContent($path);
            }
            break;
        case "saveContent":
            $path = getPost("path");
            $content = getPost("content");
            if($path != null && $content != null) {
                return saveContent($path, $content);
            }
        case null:
            echo "Error: No request sent!";
        default:
            echo "Error: Incorrect request!";
    }
}

?>