<?php
require_once "./session.php";
require_once "./database.php";
require_once "./config.php";

//change the address in the string when we have webspace
$service = urlencode( Config::$NET_LOGIN_URL );
//The banner string is passed along in the request and shows on the NetID login page
$banner = urlencode( Config::$NET_LOGIN_BANNER );

if ( !isset( $_GET['ticket'] ) && !Session::userLoggedIn() )
{
	//redirect to login page for webauth passing along a callback url as the service
	header( "Location: https://webauth.arizona.edu/webauth/login?service={$service}&banner={$banner}" );
}
else if ( isset( $_GET['ticket'] ) && !Session::userLoggedIn() )
{
	//received a ticket parameter
	$ticket = urlencode( $_GET['ticket'] );
	//use curl to send a get request to webauth to validate the ticket and service
	$curl = curl_init();
	curl_setopt_array($curl, array(
	    CURLOPT_RETURNTRANSFER => 1,
	    CURLOPT_URL => "https://webauth.arizona.edu/webauth/validate?service={$service}&ticket={$ticket}"
	));
	$response = curl_exec( $curl );
	//if the return value from the curl was false, then something went wrong with the request and no response was received
	if ( $response === false )
	{
		$message = urlencode( "Something went wrong with logging in." );
		header( "Location: error.php?error=${message}" );
		exit();
	}
	
	/*
		response is in the format:
			Yes\nNetID\n
		or:
			No\nNot valid
	*/

	$response = explode( "\n" , $response );
	//if the first line of the request is the word yes, then the next line will be the username
	if ( $response[ 0 ] === "yes" )
	{
		//if the username received from the request is allowed to login as an admin, 
		//	then save their username in the session
		if ( Session::loginUser( $response[ 1 ] ) )
		{
			//redirect to this page afterwards, should then show way to upload blog post/agenda/roster
			header( "Location: login.php" );
			exit();	
		}
		else
		{
			//the username received isn't in the whitelist of users, so show them an error
			$message = urlencode( "{$response[ 1 ]} does not have permission to view this page." );
			header( "Location: error.php?error=${message}" );
			exit();
		}
	}
	else
	{
		//the response showed an invalid ticket, show an error
		$message = urlencode( "A problem went wrong with logging in." );
		header( "Location: error.php?error=${message}" );
		exit();
	}
}
else if ( Session::userLoggedIn() )
{	
	header( "Location: index.php" );
	exit();
}
else
{
	$message = urlencode( "Default case reached in login.php script." );
	Database::logError( "${message}\n", false );
	header( "Location: error.php?error=${message}" );
	exit();
}

