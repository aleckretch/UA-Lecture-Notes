<?php
/*
	This script should automate the process of setting everything up for the database.
*/

/*
This function returns true if this script is being run from the command line or false otherwise.
*/
function fromCommandLine()
{
	return (php_sapi_name() === 'cli' OR defined('STDIN'));
}

//if this is not being run from the command line, then exit
if ( !fromCommandLine() )
{
	exit();
}

//Reads a line from standard in, trimming the ending newline
function readValue( $prompt = '' )
{
    echo $prompt;
    return addslashes( rtrim( fgets( STDIN ), "\n" ) );
}

echo "Hello, welcome to the notes setup script\n";
$name = readValue( "What is the name of the database setup from the cpanel?\n" );
$user = readValue( "What is the username of the database setup from the cpanel?\n" );
$pass = readValue( "What is the password of this username setup from the cpanel?\n" );
//echo "A netid of someone who will be able to login to the site's admin panel is needed.\n";
//$netid = readValue( "The netid for that person is?\n" );
$url = readValue( "What will the url for the site be?\n" );
if ( strpos( $url , "http://" ) === false )
{
	$url = "http://${url}";
}

if ( $url[ strlen( $url ) - 1 ] === "/" )
{
	$url .= "login.php";
}
else
{
	$url .= "/login.php";
}

//For each placeholder in copyConfig.txt, replace placeholder with the actual text
$str = file_get_contents( "./copyConfig.php" );
$replace = array(
	"#NAME#" => $name,
	"#USER#" => $user,
	"#PASS#" => $pass,
	"#URL#" => $url
);

foreach( $replace as $key=>$value )
{
	$str = str_replace( $key, $value, $str );
}
//put the newly generated php code for config into config.php, creating it if it does not exist
file_put_contents( "./config.php" , $str );

//Now the database can be setup
require_once "./database.php";
Database::setup();


