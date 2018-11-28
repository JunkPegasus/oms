<?php
// Bibliothek um die Session zu starten
include("../Common/SessionStarter.php");

// Bibliothek um Benutzerinformationen zu bekommen.
include_once("../Common/UserInfo.php");

// Bibliothek einiger allgemeinen Funktionen
include_once("../Common/Functions.php");

// Bibliothek der Datenbank Funktionen der Benutzer
include_once("../Repositorys/ObjectRepository.php");

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
            case "objectList":
                sendObjectList();
                break;
            case "deleteUser":
                if(checkPost("id")) {
                    deleteUser(getPost("id"));
                }
                break;
            case "editOrAddUser":
                if(checkPost("user")) {
                    editOrAddUser(getPost("user"));
                }
                break;
            case "userDetails":
                if(checkPost("id")) {
                    sendUser(getPost("id"));
                }
                break;
            case "objectSubList":
                if(checkPost("listId")) {
                    sendObjectSubList(getPost("listId"));
                }
                break;
            default:
                new Response(false, "Wrong request");
                break;
        }
    }
}











?>