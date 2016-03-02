<?php
//This page handles form requests
require_once "./database.php";
require_once "./session.php";


if ( !Session::userLoggedIn() )
{
	header( "Location: login.php" );
	exit();
}

if ( isset( $_GET['course'] ) )
{
	//a new course is being created
	//make sure token provided is correct
	//make sure has all required parameters in POST: name, semester, instructor, netid, token
	
	//make sure user is an admin

	//TODO: after security checks, use database api to add course
}
else if ( isset( $_GET['uploader'] ) )
{
	//an uploader is being added to a course
	//make sure token provided is correct
	//make sure has all required parameters in POST: user, course, token

	//make sure user can promote for the course provided
	
	//TODO: after security checks, use database api to add uploader
}
else if ( isset( $_GET['remove'] ) && isset( $_GET['removed'] ) )
{
	//make sure user can promote for the course provided
	//courseID in in remove, userID of uploader to remove is in removed
	//make sure the course provided in remove is an actual course
	//make sure the user provided in removed is an actual user with an account for the course provided that is an uploader

	//TODO: after security checks, use database api to remove uploader
}
else
{
	header( "Location: index.php";
	exit();
}
