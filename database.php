<?php
/*
Holds functions pertaining to the database
*/
require_once "./config.php";

require_once "./account.php";

class Database
{
	/*
		Creates a connection to the database if it does not already exist
		If a connection exists then return that connection
	*/
	public static function connect()
	{
		//$conn holds the connection to the database if it has been opened already
		//otherwise, a connection is created and $conn points to that connection
		static $conn;

		//If there is already an existing connection, return that connection
		if ( $conn )
			return $conn;

		$dbName = Config::$DB_NAME;
		$dbUser = Config::$DB_USER;
		$dbPass = Config::$DB_PASS;
		$dbHost = Config::$DB_HOST;
		$dataSrc = "mysql:host={$dbHost};dbname={$dbName}";
		try 
		{
			//create the connection with the parameters given
			$conn = new PDO( $dataSrc, $dbUser , $dbPass );
			//make associative arrays the default so that $stmt->fetch() doesn't need PDO::FETCH_ASSOC every time
			$conn->setAttribute( PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC );
		} 
		catch ( PDOException $e ) 
		{
			self::logError( "Error establishing Connection\n{$e->getMessage()}\n" );
			exit();
		}

		return $conn;
	}

	/*
		Writes the message provided to the error log.
		If fail is true(by default), also stops more code from running.
		If the config constant LOG_TO_FILE is false then the message is displayed to the page as html(sanitized).
	*/
	public static function logError( $message, $fail = TRUE )
	{
		if ( Config::$LOG_TO_FILE === TRUE )
		{
			error_log( $message );
		}
		else
		{
			echo nl2br( self::sanitizeData( $message ) );
		}
		
		if ( $fail )
		{
			exit();
		}
	}

	/*
		Generates a random token for CSRF prevention.
		Length is the number of bytes that will be generated.
		Returns the hexadecimal representation of the generated bytes as a string.
	*/
	public static function randomToken( $length = 32 )
	{
		$strong = false;
		$bytes = openssl_random_pseudo_bytes( $length, $strong );
		//if strong is false, then the bytes were not generated with a cryptographically strong algorithm
		//	if that is the case, then error out
		if ( $strong !== true )
		{
			self::logError( "Could not generate secure token\n" );
			exit();			
		}

		return bin2hex( $bytes );
	}

	/*
		Sanitizes the input given to prevent XSS.
	*/
	public static function sanitizeData( $str )
	{
		return htmlspecialchars( $str, ENT_QUOTES, 'UTF-8', false);		
	}

	/*
		Reverts the input given back to its original form, meaning any HTML tags will be there.
	*/
	public static function unsanitizeData( $str )
	{
		return ( htmlspecialchars_decode( $str, ENT_QUOTES ) );
	}

	/*
		Returns the hashed value of the token provided.
	*/
	public static function hashToken( $token )
	{
		return hash( "sha512" , $token, FALSE );
	}

	/*
		Returns true if the token matches the hashed provided or false otherwise.
	*/
	public static function hashVerify( $hashed, $token )
	{
		return ( self::hashToken( $token ) === $hashed );
	}

	/*
		Inserts a user into the user table in the database.
		Uses the netID provided as the username.
		Returns the id of the user that was inserted.
	*/
	public static function createUser( $netID )
	{
		$username = strtolower( $netID );
		$conn = self::connect();
		$stmt = $conn->prepare( "INSERT INTO Users( username ) values( :username )" );
		$stmt->bindParam( "username" , $username );
		$stmt->execute();	
		return $conn->lastInsertId();	
	}

	/*
		Inserts a course into the course table in the database.
		Uses the name,semester and instructor provided for the corresponding values in the table.
		Returns the id of the course that was inserted.
	*/
	public static function createCourse( $name, $semester, $instructor )
	{
		$args = array( $name, $semester, $instructor );
		$conn = self::connect();
		$stmt = $conn->prepare( "INSERT INTO Course( name,semester,instructor ) values( ? , ? , ? )" );
		$stmt->execute( $args );
		return $conn->lastInsertId();		
	}

	/*
		Inserts an account into the account table in the database.
		Uses the userID and courseID as the primary key.
		The accountType is what level of permissions the user has over that course.
		This should not be used for creating new users, see createUser above.
		Returns true always.
	*/
	public static function createAccount( $userID , $courseID , $accountType )
	{
		$args = array( $userID , $courseID , $accountType );
		$conn = self::connect();
		$stmt = $conn->prepare( "INSERT INTO Account( userID,courseID,accountType ) values( ? , ? , ? )" );
		$stmt->execute( $args );
		return TRUE;
	}

	/*
		Inserts a note into the notes table in the database.
		Uses the arguments provided as values for the corresponding columns in the table.
		Returns the id of the note that was created.
	*/
	public static function createNote( $fileName, $fileType, $lectureDate, $courseID, $uploaderID )
	{
		$args = array( $fileType, $fileName, $lectureDate, $courseID, $uploaderID );
		$conn = self::connect();
		$stmt = $conn->prepare( "INSERT INTO Notes( filetype,filename,lectureDate,uploadDate,courseID,userID ) values( ? , ? , ?, CURDATE(), ?, ? )" );
		$stmt->execute( $args );
		return $conn->lastInsertId();
	}

	/*
		Returns the notes uploaded for a specific course given by courseID.
		Returns an empty array if there are no notes for the course provided.
	*/
	public static function getNotesByCourse( $courseID )
	{
		$args = array( $courseID );
		$conn = self::connect();
		$stmt = $conn->prepare( "SELECT * FROM Notes WHERE courseID=?" );
		$stmt->execute( $args );
		return $stmt->fetchAll();
	}

	/*
		Returns the id of the user with the netID provided.
		Returns -1 if there is no user with that netID.
	*/
	public static function getUserId( $netID )
	{
		$args = array( strtolower( $netID ) );
		$conn = self::connect();
		$stmt = $conn->prepare( "SELECT id FROM Users WHERE username=?" );
		$stmt->execute( $args );
		$row = $stmt->fetch();
		if ( !isset( $row[ "id" ] ) )
		{	
			return -1;
		}
		return $row[ "id" ];
	}

	/*
		Returns an array of courses with the searchFor term at the beginning of the name.
		If no courses match then an empty array is returned.
	*/
	public static function searchCourses( $searchFor )
	{
		$args = array( $searchFor . "%" );
		$conn = self::connect();
		$stmt = $conn->prepare( "SELECT * FROM Course WHERE name LIKE ? ORDER BY semester DESC,instructor ASC" );
		$stmt->execute( $args );
		return $stmt->fetchAll();
	}

	/*
		Returns an array of courses with the searchFor term at the beginning of the instructor's name.
		If no courses match then an empty array is returned.
	*/
	public static function searchCoursesByProfessor( $searchFor )
	{
		$args = array( $professor . "%" );
		$conn = self::connect();
		$stmt = $conn->prepare( "SELECT * FROM Course WHERE instructor LIKE ? ORDER BY semester DESC,instructor ASC" );
		$stmt->execute( $args );
		return $stmt->fetchAll();
	}

	/*
		Returns an Account object for a specific user/course combination.
		See account.php for the objects.
	*/
	public static function getAccount( $userID, $courseID )
	{
		$args = array( $userID, $courseID );
		$conn = self::connect();
		$stmt = $conn->prepare( "SELECT accountType FROM Account WHERE userID=? and courseID=?" );
		$stmt->execute( $args );
		$account = $stmt->fetch();
		$account = ( isset( $account['accountType'] ) ? $account['accountType'] : "" );
		return Account::factory( $account );
	}

	/*
		Returns true if the MIME type provided is an allowed upload type or false otherwise.
		Uses the config constant ALLOWED_TYPES to determine if the type is allowed.
	*/
	public static function verifyFileType( $type )
	{
		return ( isset( Config::$ALLOWED_TYPES[ $type ] ) );
	}

	/*
		Returns the data of the course with the id provided.
	*/
	public static function getCourseByID( $courseID )
	{
		$args = array( $courseID );
		$conn = self::connect();
		$stmt = $conn->prepare( "SELECT * FROM Course WHERE id=?" );
		$stmt->execute( $args );
		return $stmt->fetch();
	}

	/*
		Returns the data of the notes file with the id provided.
	*/
	public static function getNotesByID( $noteID )
	{
		$args = array( $noteID );
		$conn = self::connect();
		$stmt = $conn->prepare( "SELECT * FROM Notes WHERE id=?" );
		$stmt->execute( $args );
		return $stmt->fetch();
	}
}

?>
