<?php
// Bibliothek um die Session zu starten
include("../Common/SessionStarter.php");

// Bibliothek um Benutzerinformationen zu bekommen.
include_once("../Common/UserInfo.php");

// Bibliothek einiger allgemeinen Funktionen
include_once("../Common/Functions.php");

// Konfiguration der Datenbank
include_once("../Common/Database.php");


if(!userLoggedIn()) {
    if(connectToDB() && checkPost("password") && checkPost("username")) {
        global $conn;

        $stmt = $conn->prepare("SELECT * FROM user WHERE username=:username");
        $stmt->bindParam(":username", getPost("username"));
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if(password_verify(getPost("password"), $result['password']) && $result['isAdminAllowed'] == 1) {
            $userToken = $result['username']."%§".(rand(1, 9999) * 6);
            $_SESSION['user'] = $result;
            $_SESSION['userToken'] = $userToken;
            $_SESSION['admin'] = true;
            $_SESSION['userName'] = $result['username'];
            header("Location: ../oms.php");
        } else {
            header("Location: ../index.php?login=error");
        }
    } else if(!checkPost("password") || !checkPost("username")) {
        header("Location: ../index.php?login=credentials");
    } else {
        header("Location: ../index.php?login=server");
    }
} else {
    header("Location: ../oms.php");
}

?>