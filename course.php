<?php
require_once "./database.php";
require_once "./session.php";

if ( !isset( $_GET['course'] ) )
{
	header( "Location: index.html" );
	exit();
}

$courseInfo = Database::getCourseByID( $_GET['course'] );
if ( !isset( $courseInfo[ 'id' ] ) )
{
	header( "Location: index.html" );
	exit();	
}

$files = Database::getNotesByCourse( $_GET['course'] );
?>
<!DOCTYPE HTML>
<html>
<head>
	<meta charset='UTF-8'>
	<title>Course Page</title>
</head>
<body>
	<h1><?php echo Database::sanitizeData( $courseInfo[ 'name' ] );?></h1>
	<div>
		<?php
			foreach( $files as $file )
			{
				echo "<a href='download.php?id={$file['id']}'>{$file['filetype']} = {$file['lectureDate']}</a><br>";
			}
		?>
	</div>
</body>
</html>

