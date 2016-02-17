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
	//TODO: show error message to user in some form when in production
	Database::logError( "File not provided\n" );
	exit();
}

if ( !isset( $_POST['course'] ) )
{
	//TODO: show error message to user in some form when in production
	Database::logError( "Course not provided\n" );
	exit();
}

//if the file was not uploaded correctly then do not allow the upload to continue into database
if ( $_FILES['file']['error'] !== UPLOAD_ERR_OK )  
{
	//TODO: show error message to user in some form when in production
	Database::logError( "Upload failed with error {$_FILES['file']['error']}\n" );
	exit();
}

//if the file uploaded is larger then 20mb don't allow the upload to continue into database
if ( $_FILES['file']['size'] > 20000000) 
{
	//TODO: show error message to user in some form when in production
	Database::logError( "Could not upload file, file too large\n" );
	exit();
}

//open resource to get actual mime type from the file
$finfo = finfo_open(FILEINFO_MIME_TYPE);
//get the mime type from the file information on the server( doesn't use info sent by client)
$mime = finfo_file( $finfo, $_FILES['file']['tmp_name'] );

//if the mime type is not a PDF file, then ignore the file
if ( Database::verifyFileType( $mime ) !== TRUE )
{
	//TODO: show error message to user in some form when in production
	Database::logError( "{$mime} is not an allowed type.\n" );
	exit();
}

if ( !isset( $_POST['token'] ) )
{
	//TODO: show error message to user in some form when in production
	Database::logError( "Token not passed\n" );
	exit();
}

if ( !isset( $_POST['date'] ) )
{
	//TODO: show error message to user in some form when in production
	Database::logError( "Lecture date is missing\n" );
	exit();
}

if ( !Session::verifyToken( $_POST['token'] ) )
{
	//TODO: show error message to user in some form when in production
	Database::logError( "Request could not be handled, token does not match\n" );
	exit();
	
}

$course = $_POST['course'];
$user = Database::getUserId( Session::user() );
$account = Database::getAccount( $user, $course );
if ( $account === NULL || $account->canUpload() !== TRUE )
{
	//TODO: show error message to user in some form when in production
	Database::logError( "User does not have permission to upload files for this course.\n" );
	exit();	
}

$date = trim($_POST['date']);
$date = date("Y-m-d", strtotime($date));
$fileType = Config::$ALLOWED_TYPES[ $mime ];

//get the filename excluding any non-alphanumeric characters
$fileName = Database::sanitizeFileName( $_FILES['file']['name']);
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
		Database::logError( "File with {$id} already exists\n" );
		exit();

	}
	
	//move the uploaded file to the uploads folder under the name of its id
	move_uploaded_file( $_FILES['file']['tmp_name'] , $dir  );

	//change the permissions on the uploaded file in the uploads folder to RW-R--R--
	chmod( $dir, 0644 );	

	header( "Location: index.html" );
	exit();
}
else
{
	Database::logError( "Failed to create uploads folder\n" );
	exit();
}

