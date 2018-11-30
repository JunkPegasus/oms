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
                }else {
                    new Response(false, "Wrong request");
                }
                break;
            case "editOrAddUser":
                if(checkPost("user")) {
                    editOrAddUser(getPost("user"));
                }else {
                    new Response(false, "Wrong request");
                }
                break;
            case "userDetails":
                if(checkPost("id")) {
                    sendUser(getPost("id"));
                }else {
                    new Response(false, "Wrong request");
                }
                break;
            case "objectSubList":
                if(checkPost("listId")) {
                    sendObjectSubList(getPost("listId"));
                }else {
                    new Response(false, "Wrong request");
                }
                break;
            case "object":
                if(checkPost("objId")) {
                    sendObject(getPost("objId"));
                } else {
                    new Response(false, "Wrong request");
                }
                break;
            case "changePublic":
                if(checkPost("id")) {
                    changePublicStatus(getPost("id"));
                } else {
                    new Response(false, "Wrong request");
                }
                break;
            case "saveFieldData":
                if(checkPost("fields")) {
                    saveFieldData(getPost("fields"));
                } else {
                    new Response(false, "Wrong request");
                }
                break;
            case "createObject":
                if(checkPost("obj")) {
                    createObject(getPost("obj"));
                } else {
                    new Response(false, "Wrong request");
                }
                break;
            case "deleteObject":
                if(checkPost("objId")) {
                    deleteObject(getPost("objId"));
                } else {
                    new Response(false, "Wrong request");
                }
                break;
            case "deleteImage":
                if(checkPost("id")) {
                    deleteImage(getPost("id"));
                } else {
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