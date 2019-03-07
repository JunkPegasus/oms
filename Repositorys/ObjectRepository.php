<?php 
// Konfiguration der Datenbank
include_once("../Common/Database.php");

function loadObjectList() {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM objects ORDER BY name");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function loadObjectType($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM objects WHERE id=:id");
    $stmt->bindParam("id", $id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result;
}


function loadObjectSubList($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT name, id, refId, refName, public, views, dateCreated, dateChanged, userCreated, userChanged FROM objectlist WHERE refId=:id");
    $stmt->bindParam("id", $id);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}


function loadObject($id) {
    $object = null;
    $fields = null;
    $images = null;

    global $conn;
    $stmt = $conn->prepare("SELECT * FROM objectlist WHERE id=:id");
    $stmt->bindParam("id", $id);
    $stmt->execute();
    $object = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = null;

    if($object != false) { 
        if($object['hasFields']) {
            $stmt = $conn->prepare("SELECT `fields`.*, fieldvalues.value FROM `fields` LEFT JOIN fieldvalues ON fieldvalues.refField=fields.id AND fieldvalues.refObj=:refObj WHERE `fields`.refId=:id");
            $stmt->bindParam("id", $object['refId']);
            $stmt->bindParam("refObj", $id);
            $stmt->execute();
            $fields = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $fields = null;
        }
        
        if($object['hasImages']) {
            $stmt = $conn->prepare("SELECT * FROM images WHERE refId=:id");
            $stmt->bindParam("id", $id);
            $stmt->execute();
            $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $images = null;
        }
    }

    $result['object'] = $object;
    $result['fields'] = $fields;
    $result['images'] = $images;
    return $result;
}




function saveFieldData($fields) {
    if(connectToDB()) {
        $success = true;
        $success = saveBerichtOrVorstand($fields);
        if($success) {
            foreach($fields as $field) {
                $success = saveField($field);
                if(!$success) {
                    break;
                }
            }
        }
        new Response($success,"Feld Daten speichern");
    }
}

function saveField($field) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM fieldvalues WHERE refField=:refField AND refObj=:refObj");
    $stmt->bindParam(':refField', $field['id']);
    $stmt->bindParam(':refObj', $field['refId']);
    $stmt->execute();
    $fields = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if(sizeof($fields) > 0) {
        $stmt = $conn->prepare("UPDATE fieldvalues SET value=:value WHERE refField=:refField AND refObj=:refObj");
        $stmt->bindParam(':value', $field['value']);
        $stmt->bindParam(':refField', $field['id']);
        $stmt->bindParam(':refObj', $field['refId']);
        return $stmt->execute();
    } else {
        $stmt = $conn->prepare("INSERT INTO fieldvalues (refField, refObj, value) VALUES (:refField, :refObj, :value)");
        $stmt->bindParam(':value', $field['value']);
        $stmt->bindParam(':refField', $field['id']);
        $stmt->bindParam(':refObj', $field['refId']);
        return $stmt->execute();
    }

}

function saveBerichtOrVorstand($fields) {
    if(connectToDB()) {
        global $conn;
        $obj = loadObject($fields[0]['refId']);
        $refId = $obj['object']['refId'];
        $id = $obj['object']['id'];
        if($refId == "1" || $refId == "2") {
            switch ($refId) {
                case "1":
                    $datum = formatDate($fields[3]['value']);
                    if($datum !== false) {
                        $stmt = $conn->prepare("UPDATE berichte SET name=:name, text=:text, datum=:datum, ort=:ort WHERE refObj=:id");
                        $stmt->bindParam(':name', $fields[0]['value']);
                        $stmt->bindParam(':text', $fields[1]['value']);
                        $stmt->bindParam(':datum', $datum);
                        $stmt->bindParam(':ort', $fields[2]['value']);
                        $stmt->bindParam(':id', $id);
                        return $stmt->execute();
                    } else {
                        return false;
                    }
                    break;
                case "2":
                    $stmt = $conn->prepare("UPDATE vorstand SET name=:name, rang=:rang, charakteristik=:charakteristik, reihenfolge=:reihenfolge WHERE refObj=:id");
                    $stmt->bindParam(':name', $fields[0]['value']);
                    $stmt->bindParam(':rang', $fields[1]['value']);
                    $stmt->bindParam(':reihenfolge', $fields[2]['value']);
                    $stmt->bindParam(':charakteristik', $fields[3]['value']);
                    $stmt->bindParam(':id', $id);
                    return $stmt->execute();
                    break;
            }
        }
    }
}

function formatDate($s) {
    if(strpos($s, ".") !== false) {
        $arr = explode(".", $s);
        if(sizeof($arr) == 3) {
            if(strlen($arr[0]) == 2 && strlen($arr[1]) == 2 && strlen($arr[2]) == 4) {
                return $arr[2]."-".$arr[1]."-".$arr[0];
            }
        }
    }
    return false;
}

function deleteImage($id) {
    if(connectToDB()) {
        global $conn;
        $stmt = $conn->prepare("SELECT path FROM images WHERE id=:id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $path = $stmt->fetch(PDO::FETCH_ASSOC);
        if($path != false) {
            $path = "../".$path['path'];
            if(file_exists($path)) {
                unlink($path);
            }  
        }

        $stmt = $conn->prepare("DELETE FROM images WHERE id=:id");
        $stmt->bindParam(':id', $id);
        $success = $stmt->execute();


       new Response($success, "Bild löschen");
    } 
}


function sendObjectList() {
    if(connectToDB()) {
        $objectList = loadObjectList();
        if($objectList != false) {
            new Response(true, $objectList);
        } else {
            new Response(false,"Objects can't be loaded from DB");
        }
    }
}

function sendObjectSubList($id) {
    if(connectToDB()) {
        $objectList = loadObjectSubList($id);
        if($objectList != false) {
            new Response(true, $objectList);
        } else {
            new Response(true, []);
        }
    }
}


function sendObject($id) {
    if(connectToDB()) {
        $object = loadObject($id);
        if($object != false) {
            new Response(true, $object);
        } else {
            new Response(false, "Objects can't be loaded from DB");
        }
    } 
}



function deleteObject($id) {
    if(connectToDB()) {
        global $conn;

        $object = loadObject($id);
        if($object['object'] != null) {
            include_once("../Repositorys/DirectoryRepository.php");
            delete("../".$object['object']['path']);
        }

        $stmt = $conn->prepare("DELETE FROM objectlist WHERE id=:id; DELETE FROM fieldvalues WHERE refObj=:id; DELETE FROM images WHERE refId=:id");
        $stmt->bindParam(':id', $id);

        $success = $stmt->execute();

        switch($object['object']['refId']) {
            case "1":
                $success = deleteFromBerichte($id);
                break;
            case "2":
                $success = deleteFromVorstand($id);
                break;
        }
            

        new Response($success, "Objekt löschen.");
    }
}

function deleteFromVorstand($refId) {
    if(connectToDB()) {
        global $conn;

        $stmt = $conn->prepare("DELETE FROM vorstand WHERE refObj=:id");
        $stmt->bindParam(':id', $refId);

        return $stmt->execute();
    }
}

function deleteFromBerichte($refId) {
    if(connectToDB()) {
        global $conn;

        $stmt = $conn->prepare("DELETE FROM berichte WHERE refObj=:id");
        $stmt->bindParam(':id', $refId);

        return $stmt->execute();
    }
}

function createObject($obj) {
    if(connectToDB()) {
        $refId = $obj['refId'];
        $objectType = loadObjectType($refId);

        $path = createObjectFolder($objectType['name']);

        if($path == null) {
            new Response(false, "Can't create folder");
            return false;
        } else {
            $path = substr($path, 3);
        }

        global $conn;
        $stmt = $conn->prepare("INSERT INTO objectlist (name, path, refId, refName, hasImages, hasFields, userCreated, userChanged) VALUES (:name,:path, :refId, :refName, :hasImages, :hasFields, :userCreated, :userChanged) ");
        $stmt->bindParam(':name', $obj['name']);
        $stmt->bindParam(':refId', $obj['refId']);
        $stmt->bindParam(':path', $path);
        $stmt->bindParam(':hasImages', $objectType['hasImages']);
        $stmt->bindParam(':hasFields', $objectType['hasFields']);
        $stmt->bindParam(':refName', $objectType['name']);
        $stmt->bindParam(':userCreated', $_SESSION['userName']);
        $stmt->bindParam(':userChanged', $_SESSION['userName']);

        $success = $stmt->execute();

        
        switch($refId) {
            case "1":
                $success = createBericht($conn->lastInsertId());
                break;
            case "2":
                $success = createVorstand($conn->lastInsertId());
                break;
        }

        new Response($success, $objectType['name']." erstellen.");
    }
}

function createBericht($id) {
    if(connectToDB()) {
        global $conn;
        $stmt = $conn->prepare("INSERT INTO berichte (refObj) VALUES (:refObj) ");
        $stmt->bindParam(':refObj', $id);

        return $stmt->execute();
    }
}

function createVorstand($id) {
    if(connectToDB()) {
        global $conn;
        $stmt = $conn->prepare("INSERT INTO vorstand (refObj) VALUES (:refObj) ");
        $stmt->bindParam(':refObj', $id);

        return $stmt->execute();
    }
}
function changePublicStatus($id) {
    if(connectToDB()) {
        $object = loadObject($id);

        $public = 1;
        if($object['object']['public'] == 1) {
            $public = 0;
        }

        global $conn;
        $stmt = $conn->prepare("UPDATE objectlist SET public=:public WHERE id=:id");
        $stmt->bindParam(':public', $public);
        $stmt->bindParam(':id', $id);

        $success = $stmt->execute();

        new Response($success, "Status ändern.");
    }
}


function createObjectFolder($name) {
    if(!file_exists("../../objects")) {
        mkdir("../../objects");
    }
    if(!file_exists("../../objects/".$name)) {
        mkdir("../../objects/".$name);
    }
    $z = 0;
    while(true) {
        $folder = createFolder("../../objects/".$name);
        if($folder != false) {
            return $folder;
        }
        $z++;
        if($z > 100) {
            return null;
        }
    }
}

function createFolder($path) {
    $i = rand(0, 999999);
    $path = $path."/".$i."/";
    if(!file_exists($path)) {
        if(mkdir($path)) {
            return $path;
        }
    } else {
        return false;
    }
}

function getImage($id) {
    global $conn;

    $stmt = $conn->prepare("SELECT * FROM images WHERE id=:id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    return $stmt->fetch();

}

function changeImagePublic($id) {
    if(connectToDB()) {
        global $conn;
        $image = getImage($id);
        if($image != false) {
            $public = 0;
            if($image['public'] == "0") {
                $public = 1;
            }
            $stmt = $conn->prepare("UPDATE images SET public=:public WHERE id=:id");
            $stmt->bindParam(':public', $public);
            $stmt->bindParam(':id', $id);
            $success = $stmt->execute();
            new Response($success, "Bild veröffentlichen");
        } else {
            new Response(false, "Bild nicht vorhanden");
        }

    }
}

function changeImageCover($id) {
    if(connectToDB()) {
        global $conn;
        $image = getImage($id);
        if($image != false) {
            $refId = $image['refId'];
            $stmt = $conn->prepare("UPDATE images SET isCoverImage=0 WHERE refId=:refId");
            $stmt->bindParam(':refId', $refId);
            $success = $stmt->execute();

            
            $stmt = $conn->prepare("UPDATE images SET isCoverImage=1, public=1 WHERE id=:id");
            $stmt->bindParam(':id', $id);
            $success = $stmt->execute();

            new Response($success, "Bild veröffentlichen");
        } else {
            new Response(false, "Bild nicht vorhanden");
        }

    }
}

function changeImageInternal($id) {
    if(connectToDB()) {
        global $conn;
        $image = getImage($id);
        if($image != false) {
            $internal = 0;
            if($image['internal'] == "0") {
                $internal = 1;
            }
            $stmt = $conn->prepare("UPDATE images SET internal=:internal WHERE id=:id");
            $stmt->bindParam(':internal', $internal);
            $stmt->bindParam(':id', $id);
            $success = $stmt->execute();
            new Response($success, "Bild in internen Bereich schieben");
        }else {
            new Response(false, "Bild nicht vorhanden");
        }

    }
}

?>