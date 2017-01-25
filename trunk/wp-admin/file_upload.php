<?php
require_once('../phpmailer/class.phpmailer.php');
require_once('../phpmailer/config.php');
$targetfolder = "dropzone/files/".$_POST[ 'userEmail' ].'/';
if( isset( $_POST[ 'submit' ] ) && isset( $_POST[ 'userEmail' ]) && !empty( $_POST[ 'userEmail' ])) {

    $files = $_FILES[ 'file' ];
    $upload_overrides = array( 'test_form' => false );

    $attachments = array();
		if (!file_exists($targetfolder)) {
			mkdir($targetfolder, 0777, true);
		}
		move_uploaded_file($_FILES['file']['tmp_name'], $targetfolder.basename($_FILES['file']['name']));
		foreach($_FILES['file']['tmp_name'] as $i=>$file){
			move_uploaded_file($file, $targetfolder.basename($_FILES['file']['name'][$i]));
		}
		$attachments = array();
		if (is_dir($targetfolder)){
		  if ($dht = opendir($targetfolder)){
			while (($file = readdir($dht)) !== false){
				if($file=='..' || $file=='.'){
					continue;
				}
				$attachments []= $targetfolder.$file;
			}
			closedir($dht);
		  }
		}		
	$form_submission_id ='ID'. substr(number_format(time() * rand(),0,'',''),0,6);
	$message = 'Hi '.$POST['Name'].'! Please find attachment sent on '.$POST['date_entered'].'
				Form submission ID is '.$form_submission_id.'';			
	 create_zip($attachments,$targetfolder.'centro-form.zip');
	 
	$mail = new PHPMailer;
	//Enable SMTP debugging. 
	$mail->SMTPDebug = 3;                               
	//Set PHPMailer to use SMTP.
	$mail->isSMTP();            
	//Set SMTP host name                          
	$mail->Host = $email_config['host'];
	//Set this to true if SMTP host requires authentication to send email
	$mail->SMTPAuth = true;                          
	//Provide username and password     
	$mail->Username = $email_config['user_name'];                 
	$mail->Password = $email_config['password'];                           
	//If SMTP requires TLS encryption then set it
	$mail->SMTPSecure = $email_config['smtp'];                           
	//Set TCP port to connect to 
	$mail->Port = $email_config['port'];                                   

	$mail->From = $email_config['from_email'];
	$mail->FromName = $email_config['from_name'];

	$mail->addAddress($_POST[ 'userEmail' ], $_POST[ 'Name' ]);
	$mail->addAttachment($targetfolder."centro-form.zip", "centro-form.zip");
	$mail->isHTML(true);

	$mail->Subject = "Centro form data";
	$mail->Body = "Please find attached data and files you submitted. The form submission ID is {$form_submission_id}";
	$mail->AltBody = "Please find attached data and files you submitted. The form submission ID is {$form_submission_id}";			
	if($mail->send()) 
	{
		ob_clean();
		echo json_encode(array(
		'return_url'=>'?p=1238&form_submission_id='.$form_submission_id,
		)
		);
		
	} 
	else 
	{
		ob_clean();
		echo json_encode(array(
		'return_url'=>'?p=1238&not_sent=1',
		)
		);
	}	 
}
function create_zip($files = array(),$destination = '',$overwrite = false) {
	//if the zip file already exists and overwrite is false, return false
	if(file_exists($destination) && !$overwrite) { return false; }
	//vars
	$valid_files = array();
	//if files were passed in...
	if(is_array($files)) {
		//cycle through each file
		foreach($files as $file) {
			//make sure the file exists
			if(file_exists($file)) {
				$valid_files[] = $file;
			}
		}
	}
	//if we have good files...
	if(count($valid_files)) {
		//create the archive
		$zip = new ZipArchive();
		if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
			return false;
		}
		//add the files
		foreach($valid_files as $file) {
			$new_filename = substr($file,strrpos($file,'/') + 1);
			$zip->addFile($file,$new_filename);
		}
		//debug
		//echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
		
		//close the zip -- done!
		$zip->close();
		
		//check to make sure the file exists
		return file_exists($destination);
	}
	else
	{
		return false;
	}
}