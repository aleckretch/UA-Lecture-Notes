<?php
	require_once './database.php';
	require_once "./session.php";

	if ( !isset( $_GET['id'] ) )
	{
		header( "Location: index.php" );
		exit();
	}

	//if the user is not logged in then redirect
	if ( !Session::userLoggedIn() )
	{
//		header( "Location: login.php" );
//		exit();
	}

	$token = Session::token();
	$searchId = $_GET['id'];
	$retrievedCourse = Database::getCoursebyID($searchId);
	//if the id provided is not actually a valid course then redirect
	if ( !isset( $retrievedCourse['id'] ) )
	{
		header( "Location: index.php" );
		exit();
	}

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
	<script>
	<?php
		echo "var PHP_token = \"${token}\";\n";
		echo "var PHP_course = \"{$_GET['id']}\";\n";
	?>
	</script>
	<script src="js/jquery-2.1.4.min.js"></script>
	<script src="js/upload.js"></script>
	</head>
	
	<body>
		<div class="darken_div"></div>
		<div class="main-logo">
			<a href="index.php">
			<img src="images/logo.png" height="90px" width=auto></a>	
		</div>
		
		<article class="main-content">
			<header>
			<?php
//			$user = Database::getUserId( Session::user() );
//			$account = Database::getAccount( $user, $searchId );
//			if ( $account !== NULL && $account->canUpload() )
//			{
			?>
			<div id="uploadFrame" class="upload">
				<a id="uploadLink" href="#">Upload Notes</a>
			</div>
			<?php
//			}
			?>
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
				<div class="float"><a href="download.php?id=<?php echo $note['id']?>"><img src="images/pdf.png" alt="" height="150" width=auto></br>
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
		
		<footer id="foot1">
		<p>The University of Arizona | All contents copyright &copy; 2016. Arizona Board of Regents</p>
	</footer>
	
	
	</body>
	
	
</html>
