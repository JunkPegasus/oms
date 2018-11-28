<?php
// Konfiguration der Datenbank
include_once("../Common/Database.php");

function sendUserList() {
    if(connectToDB()) {
        $list = getUserList();
        new Response(true, $list);
    }
}

function getUserList() {
    global $conn;
    $stmt = $conn->prepare("SELECT id,username, surname, name, rights, isAdminAllowed, dateCreated, userCreated, dateChanged, userChanged FROM user");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getUser($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT id,username, editable, surname, name, rights, isAdminAllowed, dateCreated, userCreated, dateChanged, userChanged FROM user WHERE id=:id");
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function deleteUser($id) {
    if(connectToDB()) {
        $user = getUser($id);
        if(intval($user['editable']) == 1 && intval($user['rights']) <= getSession("user")['rights']) {
            global $conn;
            $stmt = $conn->prepare("DELETE FROM user WHERE id=:id");
            $stmt->bindParam(":id", $id);
            $success = $stmt->execute();

            new Response($success, "Benutzer löschen.");
        } else {
            new Response(false, "Nicht ausreichend Rechte um den Benutzer zu löschen");
        }
    }
}

?>