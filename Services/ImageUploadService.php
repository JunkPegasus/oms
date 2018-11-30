<?php
// Bibliothek um die Session zu starten
include("../Common/SessionStarter.php");

// Bibliothek um Benutzerinformationen zu bekommen.
include_once("../Common/UserInfo.php");

// Bibliothek einiger allgemeinen Funktionen
include_once("../Common/Functions.php");

// Bibliothek der Datenbank Funktionen der Benutzer
include_once("../Common/Database.php");

// Klassendefinition der Serverantworten
include_once("../Common/Response.php");

// Bibliothek der Bilder Upload Funktionen
include_once("../Repositorys/ImageUploadRepository.php");


if(userLoggedIn()) {
    checkRequest();
} else {
    new Response(false, "User not logged in");
}


function checkRequest() {
    if(checkPost("request")) {
        switch (getPost("request")) {
            case "uploadImages":
                if(checkPost("objId")) {
                    uploadImagesRequest(getPost("objId"));
                }else {
                    new Response(false, "Wrong request");
                }
                break;
            default:
                new Response(false, "Wrong request");
                break;
        }
    }
}











?>