<?php
/*
	This file handles agenda uploads securely.
	Begins by checking that user is logged in, if not then they cannot upload.
	If there was a problem with the upload then exit.
	If the uploaded file is too large then exit.
	If the uploaded file is not actually a pdf file( checked mimetype from server side) then exit.
	Otherwise generate an entry in the database, get the id from that and move the file to the uploads folder.
	The filename for the file on the server will be Agenda_ID.pdf 
		where ID is replaced by the id returned from createAgenda.
	Finally the permissions on the file are changed to not allow execution.
*/
require_once "./database.php";
require_once "./session.php";


//if the user is not logged in, do not allow the upload to continue into database
if ( !Session::userLoggedIn() )
{
	header( "Location: login.php" );
	exit();
}

//if error code is not set on file upload array then do not allow upload to continue into database
if ( !isset($_FILES['file']['error']) )
{
	$message = urlencode( "The file did not upload." );
	header( "Location: error.php?error=${message}" );
	exit();
}

if ( !isset( $_POST['course'] ) )
{
	$message = urlencode( "A parameter is missing from the form submitted." );
	header( "Location: error.php?error=${message}" );
	exit();
}

//if the file was not uploaded correctly then do not allow the upload to continue into database
if ( $_FILES['file']['error'] !== UPLOAD_ERR_OK )  
{
	$message = urlencode( "The file did not upload because of {$_FILES['file']['error']}." );
	header( "Location: error.php?error=${message}" );
	exit();
}

//if the file uploaded is larger then 20mb don't allow the upload to continue into database
if ( $_FILES['file']['size'] > 20000000) 
{
	$message = urlencode( "The file did not upload because the file is too large." );
	header( "Location: error.php?error=${message}" );
	exit();
}

//open resource to get actual mime type from the file
$finfo = finfo_open(FILEINFO_MIME_TYPE);
//get the mime type from the file information on the server( doesn't use info sent by client)
$mime = finfo_file( $finfo, $_FILES['file']['tmp_name'] );

//if the mime type is not a PDF file, then ignore the file
if ( Database::verifyFileType( $mime ) !== TRUE )
{
	$message = urlencode( "{$mime} is not an allowed type." );
	header( "Location: error.php?error=${message}" );
	exit();
}

if ( !isset( $_POST['token'] ) )
{
	$message = urlencode( "No token was provided." );
	header( "Location: error.php?error=${message}" );
	exit();
}

if ( !isset( $_POST['date'] ) )
{
	$message = urlencode( "No date was provided." );
	header( "Location: error.php?error=${message}" );
	exit();
}

if ( !Session::verifyToken( $_POST['token'] ) )
{
	$message = urlencode( "The token provided does not match." );
	header( "Location: error.php?error=${message}" );
	exit();
	
}

$course = $_POST['course'];
$user = Database::getUserId( Session::user() );
$account = Database::getAccount( $user, $course );
if ( $account === NULL || $account->canUpload() !== TRUE )
{
	$message = urlencode( "You do not have permission to upload files for this course." );
	header( "Location: error.php?error=${message}" );
	exit();
}

$date = trim($_POST['date']);
$date = date("Y-m-d", strtotime($date));
$fileType = Config::$ALLOWED_TYPES[ $mime ];
$myName = "Lecture_${date}";
$fileName = Database::sanitizeFileName( $myName );
$id = Database::createNote( $fileName, $fileType, $date, $course, $user );

$result = true;
//if the uploads folder does not exist, create it
if ( !file_exists( "./uploads" ) )
{
	$result = mkdir( "./uploads" );
}

//if the upload has been created in the past at some point
if ( $result === true )
{
	$dir = Database::getUploadPath( $id , $fileType );
	if ( file_exists( $dir ) )
	{
		$message = urlencode( "The file already exists on the server." );
		Database::logError( "${message}\n" , false );
		header( "Location: error.php?error=${message}" );
		exit();
	}
	
	//move the uploaded file to the uploads folder under the name of its id
	move_uploaded_file( $_FILES['file']['tmp_name'] , $dir  );

	//change the permissions on the uploaded file in the uploads folder to RW-R--R--
	chmod( $dir, 0644 );	

	header( "Location: in_class.php?id=${course}" );
	exit();
}
else
{
	$message = urlencode( "Could not create uploads folder." );
	Database::logError( "${message}\n" , false );
	header( "Location: error.php?error=${message}" );
	exit();
}

