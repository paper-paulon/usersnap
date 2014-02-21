<?php
	if ($_POST['token'] != "d9959da8-717d-4385-9dfa-424d16672eba") {
		header(
		$_SERVER['SERVER_PROTOCOL'].'500 Internal Server Error', 
		true, 
		500
		);
		echo "no access";
	} else {
		//All data will be sent with a multipart form upload
		$reportid = $_POST['reportid'];
		
		$path = "./usersnap/".$time();
		//create new directory
        mkdir($path, 0777, true);
		  
		//$req_dump = print_r($_POST, TRUE);
		$req_dump = var_export($_POST, TRUE);		
		$fp = fopen($path."/information.txt", 'w');
		fwrite($fp, $req_dump);
		fclose($fp);
		$filename = $_FILES['file']['name'];
		//copy screenshot to the directory
		move_uploaded_file(
			$_FILES['file']['tmp_name'], 
			$path."/".$_FILES['file']['name']
		); 		
		
		//breakdown the detials
		$additional_info = $_POST['additinalinfo'];
		$additional_info = json_decode($additional_info);		
		
		//build query connection
		$mysqli = new mysqli("localhost", "psmadmin", "eBVRupvN", "psmhelp_admin");
		$qry = "insert into ticket_screenshot(ticket_id,token,sendercomment,referer,reportid,browser,ipaddress,senderemail,subject,path) values
			(".$additional_info->_ticketId.",'".mysqli_real_escape_string($mysqli,$_POST['token'])."','".mysqli_real_escape_string($mysqli,$_POST['sendercomment'])."',
			'".mysqli_real_escape_string($mysqli,$_POST['referer'])."','".mysqli_real_escape_string($mysqli,$_POST['reportid'])."','".mysqli_real_escape_string($mysqli,$_POST['browser'])."','".mysqli_real_escape_string($mysqli,$_POST['ipaddress'])."',
			'".mysqli_real_escape_string($myslqi,$additional_info->_email)."','".mysqli_real_escape_string($mysqli,$_POST['subject'])."','".$path."/".$filename."')";							
		
		$fp = fopen($path."/insert.txt", 'w');
		fwrite($fp, $qry);
		fclose($fp);
		
		$mysqli->query($qry);
		
		$qry = "update tickets set ticket_screenshot_id = " . $mysqli->insert_id . " where ticket_id=" . $aditional_info->_ticketId .";";
			
		$fp = fopen($path."/update.txt", 'w');
		fwrite($fp, $qry);
		fclose($fp);
		
		$mysqli->query($qry);
		
		echo "success";
	}
?>