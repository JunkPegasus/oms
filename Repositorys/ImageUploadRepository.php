<?php
include_once("ObjectRepository.php");


function uploadImagesRequest($objId) {
    if(connectToDB()) {
        $refObj = loadObject($objId);
        if($refObj != false) {
            if($refObj['object']['hasImages'] == 1) {
                $path = "../".$refObj['object']['path'];
                if(file_exists($path)) {
                    $success = false;
                    $success2 = false;
                    if(isset($_FILES["publicImages"])) {
                        $success = uploadImages($path, "publicImages", $objId);
                    } else {
                        $success = true;
                    }
                    if(isset($_FILES["internalImages"])) {
                        $success2 = uploadImages($path, "internalImages", $objId);
                    }else {
                        $success2 = true;
                    }
                    if($success && $success2) {
                        new Response(true, "Bilder hochladen");
                    }
                } else {
                    new Response(false, "Pfad ungültig: $path");
                }
            } else {
                new Response(false, "Objekt darf keine Bilder haben");
            }
        } else {
            new Response(false, "Objekt nicht gefunden");
        }
    }
}



function uploadImages($path,$postVariable, $objId) {
    $total = count($_FILES[$postVariable]['name']);
    $overallSuccess = true;
	// Loop through each file
	for($i=0; $i<$total; $i++) {
	  	//Get the temp file path
		$tmpFilePath = $_FILES[$postVariable]['tmp_name'][$i];
		
	  	//Make sure we have a filepath
	  	if ($tmpFilePath != ""){
			$imageFileType = strtolower(pathinfo($_FILES[$postVariable]['name'][$i],PATHINFO_EXTENSION));

            $newFilePath = $path . "image_" . $i . "." . $imageFileType;
            $newFilePath = checkFilePath($newFilePath, $imageFileType, $path);
			
			
			//Upload the file into the temp dir
            $success = move_uploaded_file($tmpFilePath, $newFilePath);
            $success2 = saveImageDetailsInDB($objId, substr($newFilePath,3), $postVariable);
            //cropImages($newFilePath, $imageFileType);
            if(!($success && $success2)) {
                $overallSuccess = false;
            }
        }
    }
    return $overallSuccess;
}

function saveImageDetailsInDB($objId, $path, $postVariable) {
    if(connectToDB()) {
        global $conn;
        $internal = 1;
        if($postVariable == "publicImages") {
            $internal = 0;
        }

        $stmt = $conn->prepare("INSERT INTO images (internal, path, refId) VALUES (:internal, :path, :refId)");
        $stmt->bindParam(":internal", $internal);
        $stmt->bindParam(":path", $path);
        $stmt->bindParam(":refId", $objId);

        return $stmt->execute();
    }
}

function checkFilePath($path, $fileType, $root) {
    if(!file_exists($path)) {
        return $path;
    }
    while(file_exists($path)) {
        $path = $root."image_".rand(0,9999999).".".$fileType;
    }
    return $path;
}

function cropImages($path, $imageType) {
	$size = getimagesize($path);
	if($imageType == 'jpg' || $imageType == 'jpeg') {
		$src = imagecreatefromjpeg($path);
	} else if($imageType == 'png') {
		$src = imagecreatefrompng($path);
	}
	
	$width = $size[0];
	$height = $size[1];
	$thumb_width = 900;
	$thumb_height = 600;

	$original_aspect = $width / $height;
	$thumb_aspect = $thumb_width / $thumb_height;
	if ( $original_aspect >= 0.9) {
		if ( $original_aspect >= $thumb_aspect ) {
		   $new_height = $thumb_height;
		   $new_width = $width / ($height / $thumb_height);
		} else {
		   $new_width = $thumb_width;
		   $new_height = $height / ($width / $thumb_width);
		}

		$tmp = imagecreatetruecolor( $thumb_width, $thumb_height );

		// Resize and crop
		imagecopyresampled($tmp,
						   $src,
						   0 - ($new_width - $thumb_width) / 2, // Center the image horizontally
						   0 - ($new_height - $thumb_height) / 2, // Center the image vertically
						   0, 0,
						   $new_width, $new_height,
						   $width, $height);

		if($imageType == 'jpg' || $imageType || 'jpeg') {
			imagejpeg($tmp, $path);
		} else if($imageType == 'png') {
			imagepng($tmp, $path);
		}
	}
}



?>