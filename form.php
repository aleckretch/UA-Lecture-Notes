<?php
//This page handles form requests
require_once "./database.php";
require_once "./session.php";
require_once "./account.php";

function checkParams( $argsNeeded, $allArgs )
{
	foreach( $argsNeeded as $param )
	{
		if ( !isset( $allArgs[ $param ] ) || trim( $allArgs[ $param ] ) === "" )
		{
			return false;
		}
	}
	return true;
}

if ( !Session::userLoggedIn() )
{
	header( "Location: login.php" );
	exit();
}

if ( isset( $_GET['searchKey'] ) )
{
	if ( trim( $_GET['searchKey'] ) === "" )
	{
		die( "[]" );
	}
	$searchKey = $_GET['searchKey'];
	$arrayOfCourses = Database::searchCourses($searchKey);
	$JSONArray = '['; //begin JSONArray string
	foreach($arrayOfCourses as $record) {
		$id = $record['id'];
		$courseName = $record['name'];
		$semester = $record['semester'];
		$instructor = $record['instructor'];

		$JSONArray = $JSONArray . '{ "courseName": "' . $courseName . '",';
		$JSONArray = $JSONArray . '"id": "' . $id . '",';
		$JSONArray = $JSONArray . '"semester": "' . $semester . '",';
		$JSONArray = $JSONArray . '"instructor": "' . $instructor . '"';
		$JSONArray = $JSONArray . '},';
	}
	$JSONArray = rtrim($JSONArray, ","); //remove last comma
	$JSONArray = $JSONArray . ']'; //end JSONArray string
	echo $JSONArray; //echo for use in AJAX in index.html
	exit();
}
else if ( isset( $_GET['course'] ) )
{
	//a new course is being created
	if ( !Session::getAdmin() )
	{
		$message = urlencode( "You do not have permission to add a course." );
		header( "Location: error.php?error=${message}" );
		exit();
	}

	$needed = array( "token" , "name" , "semester" , "instructor" , "netid" );
	if ( !checkParams( $needed, $_POST ) )
	{
		$message = urlencode( "A parameter is missing from the form submitted." );
		header( "Location: error.php?error=${message}" );
		exit();
	}

	if ( !Session::verifyToken( $_POST['token'] ) )
	{
		$message = urlencode( "The token provided does not match." );
		header( "Location: error.php?error=${message}" );
		exit();
	}

	$id = Database::getUserId( $_POST['netid'] );
	if (  $id === -1 )
	{
		$id = Database::createUser( $_POST['netid'] );
	}
	
	$course = Database::createCourse( $_POST['name'], $_POST['semester'], $_POST['instructor'] );
	Database::createAccount( $id, $course, Instructor::getName() );
	header( "Location: in_class.php?id=${course}" );
	exit();
}
else if ( isset( $_GET['uploader'] ) )
{
	//an uploader is being added to a course
	$needed = array( "token" , "course" , "user" );
	if ( !checkParams( $needed, $_POST ) )
	{
		$message = urlencode( "A parameter is missing from the form submitted." );
		header( "Location: error.php?error=${message}" );
		exit();
	}

	if ( !Session::verifyToken( $_POST['token'] ) )
	{
		$message = urlencode( "The token provided does not match." );
		header( "Location: error.php?error=${message}" );
		exit();
	}

	$courseInfo = Database::getCourseByID( $_POST['course'] );
	if ( !isset( $courseInfo[ 'id'] ) )
	{
		$message = urlencode( "The course provided is not valid." );
		header( "Location: error.php?error=${message}" );
		exit();
	}

	$myAcc = Database::getAccount( Database::getUserId( Session::user() ) , $courseInfo['id'] );
	if ( $myAcc === NULL || !( $myAcc->canPromote() ) )
	{
		$message = urlencode( "You do not have permission to add uploaders for this course." );
		header( "Location: error.php?error=${message}" );
		exit();
	}

	$id = Database::getUserId( $_POST['user'] );
	if (  $id === -1 )
	{
		$id = Database::createUser( $_POST['user'] );
	}

	$acc = Database::getAccount( $id , $_POST['course'] );
	if ( $acc !== NULL && $acc->canUpload() )
	{
		$message = urlencode( "The uploader you want to add is already an uploader." );
		header( "Location: error.php?error=${message}" );
		exit();
	}
	Database::createAccount( $id, $_POST['course'] , Uploader::getName() );
	header( "Location: admin.php?course=${courseInfo['id']}" );
	exit();
}
else if ( isset( $_GET['remove'] ) && isset( $_GET['removed'] ) )
{	
	$courseInfo = Database::getCourseByID( $_GET['remove'] );
	//if the course with the id provided is not in the database then redirect and exit
	if ( !isset( $courseInfo[ 'id'] ) )
	{
		$message = urlencode( "The course provided is not valid." );
		header( "Location: error.php?error=${message}" );
		exit();
	}

	$myAcc = Database::getAccount( Database::getUserId( Session::user() ) , $courseInfo['id'] );
	//if the current user does not have an account with promote/demote permissions then redirect and exit
	if ( $myAcc === NULL || !( $myAcc->canPromote() ) )
	{
		$message = urlencode( "You do not have permission to remove uploaders for this course." );
		header( "Location: error.php?error=${message}" );
		exit();
	}

	$acc = Database::getAccount( $_GET['removed'] , $_GET['remove'] );
	//if the user provided in removed does not have an account that can upload then redirect and exit
	if ( $acc === NULL || !$acc->canUpload() )
	{
		$message = urlencode( "The uploader you want to remove is not an uploader." );
		header( "Location: error.php?error=${message}" );
		exit();
	}

	Database::removeAccount( $_GET['removed'] , $_GET['remove'] );
	header( "Location: admin.php?course=${courseInfo['id']}" );
	exit();
}
else if ( isset( $_GET['note'] ) )
{
	//attempts to remove the note with the id provided in $_GET['note']
	$note = Database::getNotesByID( $_GET['note'] );
	if ( !isset( $note['id'] ) )
	{
		$message = urlencode( "The file you want to remove does not exist." );
		header( "Location: error.php?error=${message}" );
		exit();
	}

	$myAcc = Database::getAccount( Database::getUserId( Session::user() ) , $note['courseID'] );
	//if the current user does not have an account with file delete permissions then redirect and exit
	if ( $myAcc === NULL || !( $myAcc->canDelete() ) )
	{
		$message = urlencode( "You do not have permission to remove files for this course." );
		header( "Location: error.php?error=${message}" );
		exit();
	}

	if ( !Database::removeNoteFile( $note['id'] ) )
	{
		$message = urlencode( "The file could not be deleted." );
		header( "Location: error.php?error=${message}" );
		exit();		
	}

	Database::removeNoteWithID( $note['id'] );
	header( "Location: admin.php?course=${note['courseID']}" );
	exit();
}
else
{
	header( "Location: index.php" );
	exit();
}
