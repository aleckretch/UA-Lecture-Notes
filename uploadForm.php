<?php
	require_once "./database.php";
	require_once "./session.php";
	if ( !Session::userLoggedIn() )
	{
		header( "Location: login.php" );
		exit();
	}

	if ( !isset( $_GET['course'] ) )
	{
		header( "Location: index.html" );
		exit();
	}
?>
<!DOCTYPE HTML>
<html>
<head>
	<meta charset='UTF-8'>
	<title>Upload Prototype</title>
</head>
<body>
	<h1>Upload Notes</h1>
	<div>
	   	<form action='upload.php' enctype='multipart/form-data' method='post'>
			<input type='hidden' name='token' value='<?php echo Session::token();?>'>
			<input type='hidden' name='course' value='<?php echo $_GET['course'];?>'>
			Date:<br><input type='date' name='date'><br>
			File:<br><input type='file' name='file'><br>
			<br><input type='submit' value='Send'><br>
		</form>
		<?php
			//Testing that search works correctly
			/*
			echo json_encode( Database::searchCourses( "CSC" ) );
			echo "<br>";
			echo json_encode( Database::searchCourses( "CSC 4" ) );
			echo "<br>";
			echo json_encode( Database::searchCourses( "CSC 337" ) );
			echo "<br>";
			*/
		?>
	</div>
</body>
</html>

