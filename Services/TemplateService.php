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

function checkRequest() {
    switch (getPost("request")) {
         case "allTemplates":
            return allTemplates();
            break;
        case null:
            echo "Error: No request sent!";
        default:
            echo "Error: Incorrect request!";
    }
}

function allTemplates() {
    $dir = getScandirWithoutDots("../Templates");

    $result = array();
    $index = 0;
    foreach($dir as $file) {
        $name = explode(".", $file);
        $result[$name[0]] = utf8_encode(file_get_contents("../Templates/".$file));
        $index++;
    }
    return json_encode($result);
}


// Diese Funktion gibt alle Inhalte eines Pfades ohne die Punkte (".", "..") zurück.
function getScandirWithoutDots($path) {
    $ar = array();
    foreach(scandir($path,2) as $fileOrFolder) {
        if($fileOrFolder !== "." && $fileOrFolder !== "..") {
            $ar[] = $fileOrFolder;
        }
    }
    return $ar;
} 
?>