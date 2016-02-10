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
		echo "Bad request";
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
			echo "The NetID {$response[ 1 ]} does not have permission to view this page.";
		}
	}
	else
	{
		//the response showed an invalid ticket, show an error
		echo "Could not login via webauth.";
	}
}
else if ( Session::userLoggedIn() )
{	
	echo "Logged in";
	//TODO: redirect to other page
	//header( "Location: admin.php" );
	exit();
}
else
{
	die( "Default case reached" );
}

