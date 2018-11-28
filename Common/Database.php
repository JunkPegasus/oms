<?php
// Klassendefinition der Serverantworten
include_once("Response.php");
$conn;

function connectToDB() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    global $conn;

    try {
        $conn = new PDO("mysql:host=$servername;dbname=oms", $username, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
    catch(PDOException $e)
        {
        new Response(false, "Can't connect to DB");
        return false;
        }
    return true; 

}
?>