<?php
  require_once './database.php';
	if ( !isset( $_GET['searchKey'] ) )
	{
		die( "[]" );
	}
	$searchKey = $_GET['searchKey'];
	$arrayOfCourses = Database::searchCourses($searchKey);
	//var_dump($arrayOfCourses);
	$JSONArray = '['; //begin JSONArray string
	foreach($arrayOfCourses as $record) {
		$courseName = $record['name'];
		$semester = $record['semester'];
		$instructor = $record['instructor'];
		$JSONArray = $JSONArray . '{ "courseName": "' . $courseName . ' ",';
		$JSONArray = $JSONArray . '"semester": "' . $semester . ' ",';
		$JSONArray = $JSONArray . '"instructor": "' . $instructor . ' "';
		$JSONArray = $JSONArray . '},';
	}
	$JSONArray = rtrim($JSONArray, ","); //remove last comma
	$JSONArray = $JSONArray . ']'; //end JSONArray string
	echo $JSONArray; //echo for use in AJAX in index.html
?>
