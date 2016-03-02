<?php
/*
	This php file acts as an intermediary for anyone to download the notes for a course.
	It also allows us to send back a recommended filename for the file to the browser.
	This supports whatever file types are supplied in the Config constant Allowed_Types in config.php.
	
*/
require_once( "./database.php" );
require_once( "./config.php" );
require_once( "./session.php" );

if ( !Session::userLoggedIn() )
{
	header( "Location: login.php" );
	exit();
}

//get the id provided as a get parameter
if ( !isset( $_GET['id'] ) )
{
	header( "Location: index.php" );
	exit();
}

//if the id provided is not an actual id of a note in the database, error out
$note = Database::getNotesByID( $_GET['id'] );
if ( !isset( $note['id'] ) )
{
	//TODO: show error message page when in production
	die( "File does not exist" );
}

//if the note with the id provided is not an actual file, error out
$path = Database::getUploadPath( $note['id'] , $note['filetype'] );
if ( !file_exists( $path ) )
{
	//Log the error so that the server knows a file is missing for a valid note
	Database::logError( "File '{$path}' could not be found\n", false );

	//TODO: show some error message to the user when in production
	die( "File could not be found" );
}

//tell browser to expect the mime type of whatever type the file is
$content = Database::getMimeFromType( $note['filetype'] );
header("Content-type:{$content}");

$fileName = $note['filename'];//"Course_${note['courseID']}_${mysqldate}";

//tell the browser that the downloaded file's name should be the one in the database
header("Content-Disposition:attachment;filename=\"${fileName}.${note['filetype']}\"");

//output the files contents to the browser, allowing user to download file
readfile( $path );
