<?
/*
	This class holds configuration values that are constant. Namely the database credentials.
*/
class Config
{
	public static $DB_NAME;
	
	public static $DB_USER;

	public static $DB_PASS;

	public static $DB_HOST;

	public static $NET_LOGIN_BANNER;

	public static $NET_LOGIN_URL;

	public static $LOG_TO_FILE;

	public static $ALLOWED_TYPES;

	static function init()
	{
		self::$DB_NAME = "LECTURE";

		self::$DB_USER = "root";

		self::$DB_PASS = "";

		self::$DB_HOST = "localhost";

		self::$NET_LOGIN_BANNER = "UA Lecture Notes";

		self::$NET_LOGIN_URL = "http://localhost/UA-Lecture-Notes/login.php";

		self::$LOG_TO_FILE = TRUE;

		self::$ALLOWED_TYPES = array( "application/pdf" => "pdf" );
	}
}
Config::init();
