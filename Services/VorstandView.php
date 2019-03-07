<?php
// Bibliothek um die Session zu starten
require_once("../Common/SessionStarter.php");
// Konfiguration der Datenbank
include_once("../Common/Database.php");
if(isset($_POST['id'])) {
    $id = $_POST['id'];
    if(!isset($_SESSION['vorstandList'])) {
        $_SESSION['vorstandList'] = array();
    }
    if(!isset($_SESSION['vorstandList'][$id])){
        if(connectToDB()) {
            global $conn;
            $stmt = $conn->prepare("UPDATE objectlist SET views=views+1 WHERE id=:id");
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            $_SESSION['vorstandList'][$id] = true;
            $stmt = $conn->prepare("SELECT SUM(views) AS c FROM objectlist WHERE refId=2");
            $stmt->execute();
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            $stmt = $conn->prepare("UPDATE objects SET views=:views WHERE id=2");
            $stmt->bindParam(":views", $res['c']);
            $stmt->execute();
        }
    }
}
?>