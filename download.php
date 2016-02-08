<?php
/*
	This php file acts as an intermediary for anyone to download the agenda files.
	It also allows us to send back a recommended filename for the pdf to the browser.
	
*/
require_once( "./database.php" );

//get the id provided as a get parameter
/*
$agenda = Database::getMostRecentAgenda();
if ( !isset( $agenda[ 'id' ] ) )
{
	die( "Could not download agenda. <br>File not found." );
}
$id = $agenda[ 'id' ];

//if the result from getAgendaFromID is false then the id is invalid

$fileName = "./uploads/Agenda{$id}.pdf";
if ( !file_exists( $fileName ) )
{
	die ( "File does not exist" );
}

//tell browser to expect pdf file as response
header("Content-type:application/pdf");

//Set the timezone to Phoenix to stop warnings from timezone not being set...
date_default_timezone_set('America/Phoenix');

//get the date that the file was created and turn it into unix time
$phpdate = strtotime( $agenda[ 'uploadDate'] );

//turn the unix time into a date for filename
$mysqldate = date( 'm_d_Y', $phpdate );

//tell the browser that the filename to download the file as is Agenda_ followed by date the agenda was added to database
header("Content-Disposition:attachment;filename='Agenda{$mysqldate}.pdf'");

//output the files contents to the browser, allowing user to download file
readfile( $fileName );
*/
