<?php

// Gibt zurück, ob die übergebene POST Variable gesetzt ist
function checkPost($variableName) {
    return isset($_POST[$variableName]);
}

// Gibt zurück, ob die übergebene GET Variable gesetzt ist
function checkGet($variableName) {
    return isset($_GET[$variableName]);
}

// Gibt zurück, ob die übergebene SESSION Variable gesetzt ist
function checkSession($variableName) {
    return isset($_SESSION[$variableName]);
}

// Gibt die gespeicherte POST Variable zurück
function getPost($variableName) {
    if(checkPost($variableName)) {
        return $_POST[$variableName];
    } else {
        return null;
    }
}

// Gibt die gespeicherte GET Variable zurück
function getGet($variableName) {
    if(checkGet($variableName)) {
        return $_GET[$variableName];
    }
    return null;
}

// Gibt die gespeicherte SESSION Variable zurück
function getSession($variableName) {
    if(checkSession($variableName)) {
        return $_SESSION[$variableName];
    }
    return null;
}
?>