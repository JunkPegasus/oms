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

function sendUser($id) {
    if(connectToDB()) {
        $user = getUser($id);
        new Response(true, $user);
    }
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

function editOrAddUser($user) {
    if(connectToDB()) {
        $mode = $user['mode'];
        global $conn;
        $rights = intval($_SESSION['user']['rights']);
        if($rights > $user['rights']) {
            $rights = $user['rights'];
        }
        $isAdminAllowed = 0;
        if($user['isAdminAllowed'] == "true") {
            $isAdminAllowed = 1;
        }
        if($mode == "new") {
            $password = $user['name'].$user['surname'];
            $password = password_hash($password, PASSWORD_BCRYPT);
            
            $stmt = $conn->prepare("INSERT INTO user (username, name, password, surname, rights, isAdminAllowed, userCreated, userChanged, dateChanged) VALUES (:username, :name, :password, :surname, :rights, :isAdminAllowed, :userCreated, :userChanged, CURRENT_TIMESTAMP)");
            $stmt->bindParam(":username", $user['username']);
            $stmt->bindParam(":name", $user['name']);
            $stmt->bindParam(":password", $password);
            $stmt->bindParam(":surname", $user['surname']);
            $stmt->bindParam(":rights", $rights);
            $stmt->bindParam(":isAdminAllowed", $isAdminAllowed);
            $stmt->bindParam(":userCreated", $_SESSION['userName']);
            $stmt->bindParam(":userChanged", $_SESSION['userName']);

            new Response($stmt->execute(), "Benutzer anlegen.");

        } else if($mode == "edit") {
            $targetUser = getUser($user['id']);
            if($targetUser['editable'] == 1) {
                $stmt = $conn->prepare("UPDATE user SET name=:name, username=:username, surname=:surname, rights=:rights, isAdminAllowed=:isAdminAllowed, userChanged=:userChanged, dateChanged=CURRENT_TIMESTAMP WHERE id=:id");
                $stmt->bindParam(":username", $user['username']);
                $stmt->bindParam(":name", $user['name']);
                $stmt->bindParam(":surname", $user['surname']);
                $stmt->bindParam(":rights", $rights);
                $stmt->bindParam(":isAdminAllowed", $isAdminAllowed);
                $stmt->bindParam(":userChanged", $_SESSION['userName']);
                $stmt->bindParam(":id", $user['id']);
                
                new Response($stmt->execute(), "Benutzer bearbeiten.");

            } else {
                new Response(false, "Benutzer bearbeiten.");
            }
        }
    }
}

?>