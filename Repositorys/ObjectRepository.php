<?php 

function loadObjectList() {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM objects");
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
    $object;
    $fields;
    $images;

    global $conn;
    $stmt = $conn->prepare("SELECT * FROM objectlist WHERE id=:id");
    $stmt->bindParam("id", $id);
    $stmt->execute();
    $object = $stmt->fetch(PDO::FETCH_ASSOC);

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



function insertField($field, $refId) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO fieldvalues (refField, refObj, `value`) VALUES (:refField, :refObj, :value) ");
    $stmt->bindParam(':refObj', $refId);
    $stmt->bindParam(':refField', $field['refField']);
    $stmt->bindParam(':value', $field['value']);
    return $stmt->execute();
}




function saveFieldData($fields) {
    if(connectToDB()) {
        $success = true;
        foreach($fields as $field) {
            $success = saveField($field);
            if(!$success) {
                break;
            }
        }
        $response = new Response("Feld Daten speichern", $success);
        $response->sendResponse();
    } else {
        $response = new Response("Can't connect to Database", false);
        $response->sendResponse();
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

function deleteImage($id) {
    if(connectToDB()) {
        global $conn;
        $stmt = $conn->prepare("SELECT path FROM images WHERE id=:id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $path = $stmt->fetch(PDO::FETCH_ASSOC);
        if($path != false) {
            $path = $path['path'];
            if(file_exists($path)) {
                unlink($path);
            }  
        }

        $stmt = $conn->prepare("DELETE FROM images WHERE id=:id");
        $stmt->bindParam(':id', $id);
        $success = $stmt->execute();


        $response = new Response("Bild löschen", $success);
        $response->sendResponse();
    } else {
        $response = new Response("Can't connect to Database", false);
        $response->sendResponse();
    }
}


function sendObjectList() {
    if(connectToDB()) {
        $objectList = loadObjectList();
        $response;
        if($objectList != false) {
            $response = new Response($objectList, true);
        } else {
            $response = new Response("Objects can't be loaded from DB", false);
        }
        $response->sendResponse();
    } else {
        $response = new Response("Can't connect to Database", false);
        $response->sendResponse();
    }
}

function sendObjectSubList($id) {
    if(connectToDB()) {
        $objectList = loadObjectSubList($id);
        $response;
        if($objectList != false) {
            $response = new Response($objectList, true);
        } else {
            $response = new Response("Objects can't be loaded from DB", false);
        }
        $response->sendResponse();
    } else {
        $response = new Response("Can't connect to Database", false);
        $response->sendResponse();
    }
}


function sendObject($id) {
    if(connectToDB()) {
        $object = loadObject($id);
        $response;
        if($object != false) {
            $response = new Response($object, true);
        } else {
            $response = new Response("Object can't be loaded from DB", false);
        }
        $response->sendResponse();
    } else {
        $response = new Response("Can't connect to Database", false);
        $response->sendResponse();
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


        $response = new Response("Objekt löschen.", $success);
        $response->sendResponse();
    } else {
        $response = new Response("Can't connect to Database", false);
        $response->sendResponse();
    }
}

function createObject($obj) {
    if(connectToDB()) {
        $refId = $obj['refId'];
        $objectType = loadObjectType($refId);

        $path = createObjectFolder($objectType['name']);

        if($path == null) {
            $response = new Response("Can't create folder", false);
            $response->sendResponse();
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
        $stmt->bindParam(':userCreated', $_SESSION['username']);
        $stmt->bindParam(':userChanged', $_SESSION['username']);

        $success = $stmt->execute();

        $response = new Response($objectType['name']." erstellen.", $success);
        $response->sendResponse();
    } else {
        $response = new Response("Can't connect to Database", false);
        $response->sendResponse();
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

        $response = new Response("Status ändern.", $success);
        $response->sendResponse();
    } else {
        $response = new Response("Can't connect to Database", false);
        $response->sendResponse();
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

?>