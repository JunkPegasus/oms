<?php
// Bibliothek um die Session zu starten
include("../Common/SessionStarter.php");

// Bibliothek um Benutzerinformationen zu bekommen.
include_once("../Common/UserInfo.php");

// Bibliothek einiger allgemeinen Funktionen
include_once("../Common/Functions.php");

// Klassendefinition der Serverantworten
include_once("../Common/Response.php");

require_once "../Common/Database.php";

require_once __DIR__.'/../Tools/SimpleXLSX.php';

if(userLoggedIn()) {
    $target_dir = "../tmp/";
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    // Check if file already exists
    if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }
    // Check file size
    if ($_FILES["fileToUpload"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }
    // Allow certain file formats
    if($fileType != "xlsx") {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            if ( $xlsx = SimpleXLSX::parse($target_file) ) {
                unlink($target_file);
                if(connectToDB()) {
                    global $conn;
                    $stmt = $conn->prepare("TRUNCATE TABLE putzplan");
                    $stmt->execute();
                    foreach($xlsx->rows() as $row) {
                        $correctData = true;
                        $von = "";
                        $bis = "";
                        $name1 = "";
                        $name2 ="";
                        $name3 = "";
                        if(isset($row[0])) {
                            $tmp = tryParseDate($row[0]);
                            if($tmp !== false) {
                                $von = $tmp;
                            } else {
                                $correctData = false;
                            }
                        }
                        if(isset($row[1])) {
                            $tmp = tryParseDate($row[1]);
                            if($tmp !== false) {
                                $bis = $tmp;
                            } else {
                                $correctData = false;
                            }
                        }
                        if(isset($row[2])) {
                            $name1 = $row[2];
                        } else {
                            $correctData = false;
                        }
                        if(isset($row[3])) {
                            $name2 = $row[3];
                        } else {
                            $correctData = false;
                        }
                        if(isset($row[4])) {
                            $name3 = $row[4];
                        } else {
                            $correctData = false;
                        }
                        if($correctData) {
                            $stmt = $conn->prepare("INSERT INTO putzplan(von, bis, name1, name2, name3) VALUES (:v, :b, :n1, :n2, :n3)");
                            $stmt->bindParam(":v", $von);
                            $stmt->bindParam(":b", $bis);
                            $stmt->bindParam(":n1", $name1);
                            $stmt->bindParam(":n2", $name2);
                            $stmt->bindParam(":n3", $name3);
                            $stmt->execute();
                        } else {
                            new Response(false, "Invalid xlsx structure");
                            die();
                        }
                    }
                    new Response(true, "successfully importet");
                }
            } else {
                echo SimpleXLSX::parseError();
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}    
else {
    echo "Error: Can't find user info";
}


function tryParseDate($date) {
    $tmp= explode(" ", $date);
    $tmp = explode("-", $tmp[0]);
    if(sizeof($tmp) == 3) {
        return $tmp[0]."-".$tmp[1]."-".$tmp[2];
    }
    else {
        return false;
    }
}
?>