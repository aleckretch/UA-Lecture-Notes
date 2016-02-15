<?php
/*
	This class acts as a wrapper for PHP sessions.
	The corresponding PHP code outside of here does not need to know exactly how the session data is stored.
	This file also starts the session when it is included, so it should only be included once to prevent errors.
*/
require_once( "./database.php" );
session_start();

class Session
{
	/*
		Returns true if there is a user logged in on the current session or false otherwise.
	*/
	public static function userLoggedIn()
	{
		return ( isset( $_SESSION['user'] ) );
	}

	/*
		Returns the current user in the session.
	*/
	public static function user()
	{
		return ( self::userLoggedIn() ? $_SESSION['user'] : NULL );
	}

	/*
		Sets the username of the current user in the session.
	*/
	private static function setUser( $username )
	{
		session_regenerate_id( true );
		$_SESSION['user'] = $username;
		return $_SESSION['user'];		
	}

	/*
		Sets the user in the session to the username provided
	*/
	public static function loginUser( $username )
	{
		self::setUser( $username );
		return true;
	}

	/*
		Returns true if the CSRF token in the session hashes to the token provided.
	*/
	public static function verifyToken( $token )
	{
		if ( !isset( $_SESSION['token'] ) )
		{
			self::generateToken();
			return false;
		}

		return ( Database::hashVerify( $token , $_SESSION['token'] ) === true );
	}

	/*
		Generates a new CSRF token and puts it in the session.
	*/
	private static function generateToken()
	{
		$_SESSION['token'] = Database::randomToken();
	}

	/*
		Returns a hashed form of the CSRF token from the session.
		Will generate the token if it does not exist already.
	*/
	public static function token()
	{
		if ( !isset( $_SESSION['token'] ) )
		{
			self::generateToken();
		}
		return Database::hashToken( $_SESSION['token'] );
	}

	/*
		Destroys the session and unsets all the data for the session, effectively logging the user out.
		Also regenerates the session id afterwards for a little extra security.
	*/
	public static function logoutUser()
	{
		session_unset();
		session_destroy();
		setcookie( session_name(),'',0,'/' );
		session_regenerate_id( true );
	}
}
