$directory = "/public_html/wp-content/example.folder/";
$types = array("jpg","gif","png");
$ext = null;

if (! isset($_GET['img'])) {
    die("Invalid URL");
}

$nameOld = filter_var($_GET['img'], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW);
$nameNew = uniqid(basename($nameOld));

// File the file
foreach ( $types as $type ) {
    if (is_file($nameOld . "." . $type)) {
        $ext = $type;
        break;
    }
}

if ($ext == null) {
    die("Sorry Image Not Found");
}
$nameOld .= "." . $ext;
$type = image_type_to_mime_type(exif_imagetype($nameOld));

header("Content-Transfer-Encoding: binary");
header('Content-type: ' . $type);
header("Content-disposition: attachment; filename=$nameNew"); //
readfile($nameOld);
