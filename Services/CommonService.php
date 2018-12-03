<?php
// Bibliothek um die Session zu starten
include("../Common/SessionStarter.php");

// Bibliothek um Benutzerinformationen zu bekommen.
include_once("../Common/UserInfo.php");

// Bibliothek einiger allgemeinen Funktionen
include_once("../Common/Functions.php");

// Bibliothek der Datenbank Funktionen der Benutzer
include_once("../Repositorys/CommonRepository.php");

// Klassendefinition der Serverantworten
include_once("../Common/Response.php");


if(userLoggedIn()) {
    checkRequest();
} else {
    new Response(false, "User not logged in");
}


function checkRequest() {
    if(checkPost("request")) {
        switch (getPost("request")) {
            case "changelog":
                sendChangelog();
                break;
            case "settings":
                sendSettings();
                break;
            case "saveSettings":
                if(checkPost("settings")) {
                    saveSettings(getPost("settings"));
                }
                break;
            default:
                new Response(false, "Wrong request");
                break;
        }
    }
}











?>