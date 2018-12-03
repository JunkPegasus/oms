<?php
include_once("../Common/Database.php");

function sendChangelog() {
    if(connectToDB()) {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM changelog ORDER BY date DESC");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        new Response(true, $result);
    }
}

function sendSettings() {
    if(connectToDB()) {
        global $conn;
        $stmt = $conn->prepare("SELECT * FROM settings");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        new Response(true, $result);
    }
}

function saveSettings($settings) {
    $suc = true;
    if(connectToDB()) {
        global $conn;
        foreach($settings as $setting) {
            $active = 0;
            if($setting['active'] == "true") {
                $active = 1;
            }
            $stmt = $conn->prepare("UPDATE settings SET isActive=:active WHERE id=:id");
            $stmt->bindParam(":active", $active);
            $stmt->bindParam(":id", $setting['id']);
            if($stmt->execute() == false) {
                $suc = false;
            }
        }
        new Response($suc, "Einstellungen speichern");
    }
}

?>