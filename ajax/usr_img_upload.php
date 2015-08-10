<?php // this will be run with ajax once the user presses the "Upload image" button.

require_once('../includes/mysqli_connect.php');
session_start();

if (isset($_SESSION['user'])) {
	$user = mysqli_fetch_assoc($mysqli->query("SELECT id FROM users WHERE username='{$_SESSION['user']}'"));
} else if (isset($_SESSION['fbId'])) {
	$user = mysqli_fetch_assoc($mysqli->query("SELECT id FROM users WHERE fb_id='{$_SESSION['fbId']}'"));
}
$userId = $user['id'];

if ($_POST['label'])
	$label = $_POST['label'];

$allowedExts = array('gif', 'jpeg', 'jpg', 'png');
$temp = explode(".", $_FILES["file"]["name"]);
$extension = end($temp);

$data = array('status' => 0, 'response' => '', 'filename' => '');

if ((($_FILES["file"]["type"] == "image/gif")
|| ($_FILES["file"]["type"] == "image/jpeg")
|| ($_FILES["file"]["type"] == "image/jpg")
|| ($_FILES["file"]["type"] == "image/pjpeg")
|| ($_FILES["file"]["type"] == "image/x-png")
|| ($_FILES["file"]["type"] == "image/png"))
&& ($_FILES["file"]["size"] < 2097152)
&& in_array($extension, $allowedExts)) {

	if ($_FILES["file"]["error"] > 0) {
        $data['response'] = 'Sorry, there was an error uploading your file.';
    } else {
		$root = __DIR__ . "/../images/users/";
		$unique = uniqid();
		$filename = $root . $unique;

		while (file_exists($filename)) {
			$unique = uniqid();
			$filename = $root . $unique;
		} // now we have a unique filename, and we can move the file to it.

		if ($_FILES["file"]["type"] == "image/gif")
			$extension = "gif";
		else if ($_FILES["file"]["type"] == "image/jpeg" || $_FILES["file"]["type"] == "image/jpg" || $_FILES["file"]["type"] == "image/pjpeg")
			$extension = "jpg";
		else if ($_FILES["file"]["type"] == "image/x-png" || $_FILES["file"]["type"] == "image/png")
			$extension = "png";
		$filename = $filename . "." . $extension;

		move_uploaded_file($_FILES['file']['tmp_name'], $filename);
		mysqli_query($mysqli, "UPDATE users SET image='$filename' WHERE id=$userId");
		// success: update response.
		$data['status'] = 1;
		$data['response'] = '<i class="fa fa-check-circle"></i> Your upload was successful!';
		$data['filename'] = $filename;
    }
} else if ($_FILES["file"]["size"] >= 2097152) {
	$data['response'] = '<i class="fa fa-times-circle"></i> Your file exceeds the limit of 2MB.';
} else if (!in_array($extension, $allowedExts)) {
	$data['response'] = '<i class="fa fa-times-circle"></i> This is not a valid file format. Please upload JPG, PNG or GIF files.';
} else {
	$data['response'] = '<i class="fa fa-times-circle"></i> An error occurred.';
}

echo json_encode($data);





// if there is already an image inside the DB
//	update with the new image id





?>