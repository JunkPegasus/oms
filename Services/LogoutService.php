<?php
// Bibliothek um die Session zu starten
include("../Common/SessionStarter.php");

$_SESSION = [];
session_destroy();
header("Location: ../index.php");

?>