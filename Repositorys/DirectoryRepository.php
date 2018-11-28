<?php

// Diese Funktion gibt den Kompletten Verzeichnis-Baum des Servers zurück 
function fullTree() {
    global $config;
    $root = $config->rootDir;
    return json_encode(getContentRecursive($root));
}

// Diese Funktion gibt alle Inhalte eines Pfades ohne die Punkte (".", "..") zurück.
function getScandirWithoutDots($path) {
    $ar = array();
    foreach(scandir($path, 2) as $fileOrFolder) {
        if($fileOrFolder !== "." && $fileOrFolder !== "..") {
            $ar[] = $fileOrFolder;
        }
    }
    return $ar;
} 

// Diese Funktion gibt anhand des übergebenen Pfades den Inhalt des Pfades mit den jeweiligen Unterordnern Rekursiv zurück 
function getContentRecursive($path) {
    $scan = getScandirWithoutDots($path);
    $arr = array();
    $folders = array();
    $files = array();
    foreach($scan as $fileOrFolder) {
        $name = $path."/".$fileOrFolder;
        if(is_dir($name)) {
            $folder = new Folder($fileOrFolder,$name);
            if(!is_dir_empty($name)) {
                $folder->content = getContentRecursive($name);
            }
            $folders[] = $folder;
        } else {
            $file = new File($fileOrFolder, $name, filesize($name), getMIMEType($fileOrFolder));
            $files[] = $file;
        }
    }
    $arr["files"] = $files;
    $arr["folders"] = $folders;
    return $arr;
}

// Klasse um einen Ordner im Server-Verzeichnis darstellen zu können
class Folder {
    public $name;
    public $path;
    public $content;

    function __construct($name, $path) {
        $this->name = $name;
        $this->path = $path;
    }
}


// Klasse um eine Datei im Server-Verzeichnis darstellen zu können
class File {
    public $name;
    public $path;
    public $size;
    public $type;

    function __construct($name, $path, $size, $type) {
        $this->name = $name;
        $this->path = $path;
        $this->size = $size;
        $this->type = $type;
    }
}

// Diese Funktion überprüft, ob ein Verzeichnis leer ist
function is_dir_empty($dir) {
    if (!is_readable($dir)) return NULL; 
    return (count(scandir($dir, 2)) == 2);
  }

function getMIMEType($filename) {
    $a = explode(".", $filename);
    if($a > 0) {
        $type = $a[sizeof($a) - 1];
        switch ($type) {
            case "jpg":
                return "image";
            case "jpeg":
                return "image";
            case "png":
                return "image";
            case "gif":
                return "image";
            case "svg":
                return "image";
            default:
                return "text";  
        }
    }
    return null;
}

function delete($path) {
    if(file_exists($path)) {
        if(is_dir($path)) {
            rrmdir($path);
        } else {
            $res = unlink($path);
        }
    }
}

function fileContent($path) {
    $content = file_get_contents($path);
    $result;
    if($content !== false) {
        $result["status"] = "success";
        $result["data"] = $content;
    }
    else {
        $result["status"] = "error";
    }
    return json_encode($result);
}

function saveContent($path, $content) {
    $status = file_put_contents($path, $content);
    $result;
    if($status !== false) {
        $result["status"] = "success";
        $result["bytes-written"] = $status;
    } else {
        $result["status"] = "error";
    }
    return json_encode($result);
}

function deleteMultiple($paths) {
    $bErrors = array();
    $bSuccess = true;
    foreach($paths as $path) {
        if(file_exists($path)) {
            if(is_dir($path)) {
                $res["path"] = $path;
                $res["status"] = rrmdir($path);
                if(!$res["status"]) {
                    $bSuccess = false;
                }
                $bErrors[] = $res;
            } else {
                $res["path"] = $path;
                $res["status"] = unlink($path);
                if(!$res["status"]) {
                    $bSuccess = false;
                }
                $bErrors[] = $res;
            }
        }
    }
    $result["status"] = "success";
    $result["filestatus"] = $bErrors;

    return json_encode($result);
}

function rrmdir($dir) { 
    if (is_dir($dir)) { 
      $objects = scandir($dir); 
      foreach ($objects as $object) { 
        if ($object != "." && $object != "..") { 
          if (is_dir($dir."/".$object))
            rrmdir($dir."/".$object);
          else
            unlink($dir."/".$object); 
        } 
      }
      return rmdir($dir); 
    } 
  }
?>