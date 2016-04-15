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
		return ( isset( $_SESSION['user'] ) && !self::checkTimeOut() );
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
		Queries the database to see if the user is an admin and initializes the session variable for that result.
	*/
	public static function setAdmin( $username )
	{
		$id = Database::getUserId( $username );
		$_SESSION['admin'] = false;
		if ( $id !== -1 )
		{
			$_SESSION['admin'] = Database::isAdmin( $id );
		}
	}

	/*
		Returns true if the user of this session is an admin or false otherwise.
	*/
	public static function getAdmin()
	{
		if ( isset( $_SESSION['admin'] ) )
		{
			return $_SESSION['admin'];
		}
		return false;
	}

	/*
		Sets the user in the session to the username provided
	*/
	public static function loginUser( $username )
	{
		self::setUser( $username );
		self::setAdmin( $username );
		self::setTimer();
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

	/*
		Sets the time in the session to the time provided.
		This value is used to determine if a user has been logged in for too long.
	*/
	public static function setTimer( $toTime = NULL )
	{
		if ( $toTime === NULL )
		{
			date_default_timezone_set( "America/Phoenix" );
			$toTime = time();
		}
		$_SESSION[ 'time' ] = $toTime;
		
	}

	/*
		Returns the time currently in session if there is a time, or 0 if there is not.
	*/
	public static function getTimer()
	{
		return ( isset( $_SESSION[ 'time' ] ) ? $_SESSION[ 'time' ] : 0 );
	}

	/*
		Returns true if the user has been logged in for longer then a certain amount of time or false otherwise.
	*/
	public static function isTimedOut()
	{
		if ( !isset( $_SESSION[ 'time' ] ) )
		{
			return true;
		}
		
		$totalTime = 60 * 60 * 1;
		date_default_timezone_set( "America/Phoenix" );
		$currentTime = time();
		return ( $_SESSION[ 'time' ] + $totalTime < $currentTime ); 
	}

	/*
		Checks whether the current user is timed out, and if so logs them out and returns true.
		Otherwise returns false if they were not logged out.
	*/
	private static function checkTimeOut()
	{
		if ( !self::isTimedOut() )
		{
			return false;
		}
		Session::logoutUser();
		return true;
	}
}
