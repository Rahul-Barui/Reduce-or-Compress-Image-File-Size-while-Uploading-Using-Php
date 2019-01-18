<?php
	$con = mysqli_connect("localhost","root","","rahul");
	if(!$con) {
		die('Connection Error');
	}
	$id='';
	if(isset($_REQUEST['id'])){
		$id = $_REQUEST['id'];
	}
		
	$sql_get = "SELECT * FROM `image_upload` WHERE `id` = '$id'";
	$res_get = mysqli_query($con,$sql_get) or("Error in Display Photo page".mysqli_error($con));
	$row = mysqli_fetch_assoc($res_get);
	$filepath = "upload/".$row['img_name'];
				
	header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename="'.basename($filepath).'"');
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	header('Content-Length: ' . filesize($filepath));
	flush(); // Flush system output buffer
	readfile($filepath);
	exit;
?>