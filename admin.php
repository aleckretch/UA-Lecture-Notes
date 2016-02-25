<?php
/*
	Shows admin page for a specific course to instructors.
	Shows admin page to add courses for admin.
*/
require_once "./database.php";
require_once "./session.php";

//if the user is not logged in, redirect them to the login page
if ( !Session::userLoggedIn() )
{
	header( "Location: login.php" );
	exit();
}

if ( isset( $_GET['course'] ) )
{
	//show the admin page for instructors

	//if the user does not have permission to see the admin page for the course then redirect them to the home page
	$course = $_GET['course'];
	$user = Database::getUserId( Session::user() );
	$account = Database::getAccount( $user, $course );
	if ( $account === NULL || $account->canPromote() !== TRUE )
	{
		header( "Location: index.php" );
		exit();
	}

	//TODO: NEED TO OUTPUT HTML DISPLAY
}
else
{
	//show the admin page for admins
	//redirect if the user is not an admin
	if ( !Session::getAdmin() )
	{
		header( "Location: index.php" );
		exit();
	}
	//TODO: NEED TO OUTPUT HTML DISPLAY
}

