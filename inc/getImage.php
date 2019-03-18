<?php

include 'xmlVocReadAnnotationsFile.php';
include 'configuration.php';

$imageId = $_POST["imageId"];

# Search the xml file in a $dir
function getXmlFile($dir, $filename)
{
	$xml_filepath = null;
    $files = scandir($dir);
    $results = null;

    foreach($files as $key => $value)
	{
        if ( strcasecmp($value, $filename) == 0 )
		{
            $xml_filepath = $dir.DIRECTORY_SEPARATOR.$filename;
			return $xml_filepath;
		}
    }

    return $xml_filepath;
}

$it = new RecursiveDirectoryIterator($IMAGE_ROOT_DIR);

# List of images to process
$list_of_images = array();

# Index of images
$image_index = 0;

foreach(new RecursiveIteratorIterator($it) as $file)
{

	# Process file
	if ( (strpos(strtoupper($file), '.JPG') !== false) && (strstr($file, $COLLECTION_NAME)) )
	{
		# echo $file . "<br>";
		$delimiter = "/";
		$item = explode($delimiter, $file);
		$nbItems = count($item);
		# Should be A/C type / MSN / Image name
		if ($nbItems>=3)
		{

			$image_name = $item[$nbItems-1];
			$msn = $item[$nbItems-2];
			$type = $item[$nbItems-3];
			$image_info = array("type" => $type, "msn" => $msn,
			   "name" => $image_name);

			# Add the image in the list
			$list_of_images[$image_index] = $image_info;
			$image_index = $image_index + 1;
		}
	}
}

function search($arrays, $key, $search) {
    foreach($arrays as $arr) {
        if(strcmp($arr[$key], $search) == 0) {
            return $arr;
        };
    }
    return null;
}

$image_info = search($list_of_images, 'name', "media-".$imageId.".jpg");

$url = $IMAGE_WEB_DIR."/".$image_info["type"] . "/" . $image_info["msn"] . "/" . $image_info["name"];

# Remove extension
$id = str_replace(array(".jpg",".JPG"),".jpg", $image_info["name"]);

# Get the xml file, replace .jpg by xml
$xml_filename = str_replace(array(".jpg",".JPG"), ".xml", $id);

# Try to find the annotation
$xml_filepath = getXmlFile($ANNOTATIONS_DIR, $xml_filename);

if ($xml_filepath != null)
{
	$annotations = [];
	$xml = new xmlVocReadAnnotationsFile($xml_filepath);

	if (!$xml->hasError())
	{
		file_put_contents($file, "Parse XML\n",FILE_APPEND | LOCK_EX);
		$xml->parseXML();
		if (!$xml->hasError())
		{
			$annotations = $xml->getAnnotations();
		}
	}
	else
	{
		$annotations = [];
	}
}
else
{
	$annotations = [];
}

# Prepare message to send
$data = array ("url" => $url, "id" => $id, "folder" => $image_info["type"] . "/" . $image_info["msn"],
				"annotations" => $annotations);

header('Content-Type: application/json');
echo json_encode($data);

?>

