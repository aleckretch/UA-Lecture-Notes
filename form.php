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

if ( isset( $_GET['course'] ) )
{
	//a new course is being created
	if ( !Session::getAdmin() )
	{
		header( "Location: index.php?error=admin" );
		exit();
	}

	$needed = array( "token" , "name" , "semester" , "instructor" , "netid" );
	if ( !checkParams( $needed, $_POST ) )
	{
		header( "Location: index.php?error=param" );
		exit();
	}

	if ( !Session::verifyToken( $_POST['token'] ) )
	{
		header( "Location: index.php?error=token" );
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
		header( "Location: index.php?error=param" );
		exit();
	}

	if ( !Session::verifyToken( $_POST['token'] ) )
	{
		header( "Location: index.php?error=token" );
		exit();
	}

	$courseInfo = Database::getCourseByID( $_POST['course'] );
	if ( !isset( $courseInfo[ 'id'] ) )
	{
		header( "Location: index.php?error=course" );
		exit();
	}

	$myAcc = Database::getAccount( Database::getUserId( Session::user() ) , $courseInfo['id'] );
	if ( $myAcc === NULL || !( $myAcc->canPromote() ) )
	{
		header( "Location: index.php?error=promote" );
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
		header( "Location: index.php?error=user" );
		exit();
	}
	Database::createAccount( $id, $_POST['course'] , Uploader::getName() );
	header( "Location: admin.php?course=${courseInfo['id']}" );
	exit();
}
else if ( isset( $_GET['remove'] ) && isset( $_GET['removed'] ) )
{
	//make sure user can promote for the course provided
	//courseID in in remove, userID of uploader to remove is in removed
	//make sure the course provided in remove is an actual course
	//make sure the user provided in removed is an actual user with an account for the course provided that is an uploader

	//TODO: after security checks, use database api to remove uploader
	
	$courseInfo = Database::getCourseByID( $_GET['remove'] );
	//if the course with the id provided is not in the database then redirect and exit
	if ( !isset( $courseInfo[ 'id'] ) )
	{
		header( "Location: index.php?error=course" );
		exit();
	}

	$myAcc = Database::getAccount( Database::getUserId( Session::user() ) , $courseInfo['id'] );
	//if the current user does not have an account with promote/demote permissions then redirect and exit
	if ( $myAcc === NULL || !( $myAcc->canPromote() ) )
	{
		header( "Location: index.php?error=promote" );
		exit();
	}

	$acc = Database::getAccount( $_GET['removed'] , $_GET['remove'] );
	//if the user provided in removed does not have an account that can upload then redirect and exit
	if ( $acc === NULL || !$acc->canUpload() )
	{
		header( "Location: index.php?error=user" );
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
		header( "Location: index.php?error=note" );
		exit();
	}

	$myAcc = Database::getAccount( Database::getUserId( Session::user() ) , $note['courseID'] );
	//if the current user does not have an account with file delete permissions then redirect and exit
	if ( $myAcc === NULL || !( $myAcc->canDelete() ) )
	{
		header( "Location: index.php?error=delete" );
		exit();
	}

	if ( !Database::removeNoteFile( $note['id'] ) )
	{
		header( "Location: index.php?error=file" );
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
