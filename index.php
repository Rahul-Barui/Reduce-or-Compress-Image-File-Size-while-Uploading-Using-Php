<?php 
	$con = mysqli_connect("localhost","root","","rahul");
	if(!$con) {
		die('Connection Error');
	}
	if(isset($_POST["submit"])){
		extract($_POST);
		$filename = $_FILES['image']['name'];
		$tmpname = $_FILES['image']['tmp_name'];
		$file_size = $_FILES['image']['size'];
		$file_type = $_FILES['image']['type'];
		$ext = pathinfo($filename, PATHINFO_EXTENSION);
		
		//----------- Get Current Date and Time --------------
		
		date_default_timezone_set('Asia/Kolkata');
		$timestamp = time();
		$date = date("Y-m-d", $timestamp);
		$timestamp2 = time();
		$time = date("H:i:s", $timestamp2);
		
		//-------- Let a flag variable ( $uploadOk ) --------------------
		
		$uploadOk = 1;
		
		if($filename==''){ // Check If Blank
		?>
		<script type="text/javascript">
			alert("Sorry !! You Haven't select any image.");
		</script>
		<?php
			$uploadOk = 0;
		}
		if($ext!= "png" && $ext!="PNG" && $ext!="JPG" && $ext!="jpg" && $ext!="jpeg" && $ext!="JPEG" ){	//check File Type
		?>
		<script type="text/javascript">
			alert('Sorry !! Only JPG, JPEG & PNG files are allowed.');
		</script>
		<?php
		$uploadOk = 0;
		}
		if ($uploadOk == 0) { ?>
		<script type="text/javascript">
			//alert('So, Your Photo was not uploaded.');
			window.location.href='index.php';
		</script>
		<?php
		} else {
			
			// ---------- Define Upload Directory ------------------
			
			$upload_dir = "upload/";
			$upload_file = $upload_dir.$filename;
			if(move_uploaded_file($tmpname,$upload_file)){
				$source_image = $upload_file;
				$image_destination = $upload_dir."rb_".$filename;
				
				// ---------- Here Called The Compree/Reduce Image function compressImage() ------------------
				
				$compress_images = reduceImage($source_image,$image_destination);
				
				// ---------- Images are store in Blob Concept ------------------
				
				/*list($width, $height) = getimagesize($compress_images);
			
				$fp = fopen($compress_images, 'r');
				$content = fread($fp, filesize($compress_images));
				$content = addslashes($content);
				fclose($fp);
				*/
				
				// Here write your own SQL Inseration Code 
				$file_name = "rb_".$filename;
				$sql_check = "SELECT * FROM `image_upload` WHERE `img_name` = '$file_name'";
				$res_check = mysqli_query($con,$sql_check) or("Error in Check".mysqli_error($con));
				$tot = mysqli_num_rows($res_check);
				if( $tot == 0 ){ 
					$sql = "INSERT INTO `image_upload`(`id`,`img_name`,`img_type`,`img_date`,`img_time`) VALUES ('','$file_name','$file_type','$date','$time')";
					$ok = mysqli_query($con,$sql) or("Error in Upload Photo page(Insert)".mysqli_error($con));
					
					// ---------- Destroy Main File and Store Comprressed File ------------------
					
					if($ok){
						unlink($upload_file);
						?>
						<script type="text/javascript">
							alert('Uploaded');
							window.location.href='index.php';
						</script>
						<?php
					} else {
						?>
						<script type="text/javascript">
							alert('Not Uploaded.');
							window.location.href='index.php';
						</script>
						<?php
					}
				} else {
					$sql = "UPDATE `image_upload` SET `img_name`='$file_name',`img_type`='$file_type',`img_date`='$date',`img_time`='$time' WHERE `img_name` = '$file_name'";
					$ok = mysqli_query($con,$sql) or("Error in Upload Photo page (Update)".mysqli_error($con));
					
					// ---------- Destroy Main File and Store Comprressed File ------------------
					
					if($ok){
						unlink($upload_file);
						?>
						<script type="text/javascript">
							alert('Updated');
							window.location.href='index.php';
						</script>
						<?php
					} else {
						?>
						<script type="text/javascript">
							alert('Not Updated.');
							window.location.href='index.php';
						</script>
						<?php
					}
				}
				
				// ---------- If you want to Unlink the Comprressed File ------------------
				
				//unlink($image_destination);
			}	
		}
	}
	
	// created compressed JPEG file from source file. Function Body
	
	function reduceImage($source_image,$reduce_image) {
		$image_info = getimagesize($source_image);
		if ($image_info['mime'] == 'image/jpeg') {
			$source_image = imagecreatefromjpeg($source_image);
			imagejpeg($source_image, $reduce_image, 9);
		} elseif ($image_info['mime'] == 'image/gif') {
			$source_image = imagecreatefromgif($source_image);
			imagegif($source_image, $reduce_image, 9);
		} elseif ($image_info['mime'] == 'image/png') {
			$source_image = imagecreatefrompng($source_image);
			imagepng($source_image, $reduce_image, 2);
		}
		return $reduce_image;
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Compress Image File Size in PHP</title>
		
	</head>

<body>
	<br /><br /><br />
	<center>
	<font color="#990000" size="5"> <b>Reduce or Compress Image File Size while Uploading Using Php</b> </font><br><br>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
			<table align="center" border="1" width="100%" height="300px">
				<tr align="center">
					<th align="center">
						<label><font color="#0000FF">Upload Your Photo : </font></label>
						<input type="file" name="image" accept="image/*" required />
					</th>	
				</tr>
				<tr align="center">
					<th align="center">
						<input type="submit" name="submit" value="Submit" />
					</th>	
				</tr>
				<tr align="center">
					<th align="center">
						<?php 
							$sql_get = "SELECT * FROM `image_upload` ORDER BY `id` DESC";
							$res_get = mysqli_query($con,$sql_get) or("Error in Display Photo page".mysqli_error($con));
							$row = mysqli_fetch_assoc($res_get);
							$count = mysqli_num_rows($res_get);
							if( $count > 0 ) {
							?>
							<label>Uploaded Photo : <?php echo $row['img_name']." | ".$row['img_date']." | ".$row['img_time']; ?></label><br /><br />
							<img src="upload/<?php echo $row['img_name']; ?>" height="60" width="60"><br /><br />
							<a href="download.php?id=<?php echo $row['id'];?>"> <b>Download</b></a>
							<?php } ?>
					</th>	
				</tr>
			</table>
		</form>
	</center>


	
</body>
</html>