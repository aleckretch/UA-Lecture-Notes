<?php

	require_once './database.php';

	if ( !isset( $_GET['id'] ) )
	{
		die( "[]" );
	}
	$searchId = $_GET['id'];
	$retrievedCourse = Database::getCoursebyID($searchId);
	$retrievedCourse['name'];
	$notes = Database::getNotesByCourse($searchId);
?>

<!doctype html>
<html>
	<head>
    <meta charset="utf-8">
    <title>Arizona Notes</title>
	  
	<link rel="stylesheet" type="text/css" href="css/main.css">
	<link rel="stylesheet" type="text/css" href="css/fonts.css">

	</head>
	
	<body>
		<div class="main-logo">
			<a href="index.html">
			<img src="images/logo.png" height="80" width=auto></a>	
		</div>
		
		<article class="main-content">
			<header>
			<div class="upload">
				<a href="#">Upload Notes</a>
			</div>
			<p>
			<?php echo $retrievedCourse['name'] . " - " . $retrievedCourse['semester']; ?>
			</p>
			<p>
			Instructor: <?php echo $retrievedCourse['instructor']; ?>
			</p>
			</header>
			<main>
			<?php foreach ($notes as $note) {
			?>
				<div class="float"><a href="#"><img src="images/pdf.png" alt="" height="150" width=auto></br>
				<p><?php echo $note['lectureDate']; ?></p></div>
				<?php } ?>

				<!-- <div class="float"><a href="#"><img src="images/pdf.png" alt="" height="150" width=auto></br>
				<p>2/3/16</p></div>
				<div class="float"><a href="#"><img src="images/pdf.png" alt="" height="150" width=auto></br>
				<p>2/2/16</p></div>
				<div class="float"><a href="#"><img src="images/pdf.png" alt="" height="150" width=auto></br>
				<p>2/1/16</p></div>
				<div class="float"><a href="#"><img src="images/pdf.png" alt="" height="150" width=auto></br>
				<p>1/31/16</p></div>
				<div class="float"><a href="#"><img src="images/pdf.png" alt="" height="150" width=auto></br>
				<p>1/30/16</p></div>
				<div class="float"><a href="#"><img src="images/pdf.png" alt="" height="150" width=auto></br>
				<p>1/29/16</p></div>
				
				<div class="space"> &nbsp; </div>
				
				<div class="float"><a href="#"><img src="images/pdf.png" alt="" height="150" width=auto></br>
				<p>2/3/15</p></div>
				<div class="float"><a href="#"><img src="images/pdf.png" alt="" height="150" width=auto></br>
				<p>2/2/15</p></div>
				<div class="float"><a href="#"><img src="images/pdf.png" alt="" height="150" width=auto></br>
				<p>2/1/15</p></div>
				<div class="float"><a href="#"><img src="images/pdf.png" alt="" height="150" width=auto></br>
				<p>1/31/15</p></div>
				<div class="float"><a href="#"><img src="images/pdf.png" alt="" height="150" width=auto></br>
				<p>1/30/15</p></div>
				<div class="float"><a href="#"><img src="images/pdf.png" alt="" height="150" width=auto></br>
				<p>1/29/15</p></div> -->
			</main>
		</article>
		
		<footer>
			<p>The University of Arizona | All contents copyright &copy; 2016. Arizona Board of Regents</p>
		</footer>
	
	
	</body>
	
	
</html>
