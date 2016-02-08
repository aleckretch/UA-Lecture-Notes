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

/*
//if the user is not logged in, do not allow the upload to continue into database
if ( !Session::userLoggedIn() )
{
	header( "Location: login.php" );
	exit();
}

//if error code is not set on file upload array then do not allow upload to continue into database
if ( !isset($_FILES['file']['error']) )
{
	$message = urlencode( "File not provided" );
	header( "Location: admin.php?agenda=yes&uploaded={$message}" );
	exit();
}

if ( !isset( $_POST['title'] ) )
{
	$message = urlencode( "Title not provided" );
	header( "Location: admin.php?agenda=yes&uploaded={$message}" );
	exit();
}

//if the file was not uploaded correctly then do not allow the upload to continue into database
if ( $_FILES['file']['error'] !== UPLOAD_ERR_OK )  
{
	$message = urlencode( "Upload failed with error {$_FILES['file']['error']}" );
	header( "Location: admin.php?agenda=yes&uploaded={$message}" );
	exit();
}

//if the file uploaded is larger then 20mb don't allow the upload to continue into database
if ( $_FILES['file']['size'] > 20000000) 
{
	$message = urlencode( "Could not upload file, file too large" );
	header( "Location: admin.php?agenda=yes&uploaded={$message}" );
	exit();
}

//open resource to get actual mime type from the file
$finfo = finfo_open(FILEINFO_MIME_TYPE);
//get the mime type from the file information on the server( doesn't use info sent by client)
$mime = finfo_file( $finfo, $_FILES['file']['tmp_name'] );

//if the mime type is not a PDF file, then ignore the file
if ( $mime !== "application/pdf" )
{
	$message = urlencode( "{$mime} is not PDF" );
	header( "Location: admin.php?agenda=yes&uploaded={$message}" );
	exit();
}

if ( !isset( $_POST['token'] ) )
{
	$message = urlencode( "Token not passed" );
	header( "Location: admin.php?agenda=yes&uploaded={$message}" );
	exit();
}

if ( !Session::verifyToken( $_POST['token'] ) )
{
	$str = urlencode( "Request could not be handled, token does not match" );
	header( "Location: admin.php?agenda=yes&uploaded={$str}" );
	exit();
	
}

$title = $_POST['title'];
$result = true;
//if the uploads folder does not exist, create it
if ( !file_exists( "./uploads" ) )
{
	$result = mkdir( "./uploads" );
}

//if the upload has been created in the past at some point
if ( $result === true )
{
	Database::archiveAllAgendas();
	//Create a new agenda with title of Test
	$id = Database::createAgenda( $title );
	$dir = "./uploads/Agenda{$id}.pdf";
	if ( file_exists( $dir ) )
	{
		Database::removeAgendaWithID( $id );
		$message = urlencode( "Cannot upload, file already exists" );
		header( "Location: admin.php?agenda=yes&uploaded={$message}" );
		exit();

	}
	

	//move the uploaded file to the uploads folder under the name of its id
	move_uploaded_file( $_FILES['file']['tmp_name'] , $dir  );

	//change the permissions on the uploaded file in the uploads folder to RW-R--R--
	chmod( $dir, 0644 );	
	header( "Location: admin.php?agenda=true&uploaded=yes" );
	exit();
}
else
{
	$message = urlencode( "Failed to create uploads folder" );
	header( "Location: admin.php?agenda=yes&uploaded={$message}" );
	exit();
}
*/
