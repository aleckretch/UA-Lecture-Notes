<?php
	require_once './database.php';
	require_once "./session.php";

	if ( !isset( $_GET['id'] ) )
	{
		$message = urlencode( "Missing the course number." );
		header( "Location: error.php?error=${message}" );
		exit();
	}

	//if the user is not logged in then redirect
	if ( !Session::userLoggedIn() )
	{
		header( "Location: login.php" );
		exit();
	}

	$token = Session::token();
	$searchId = $_GET['id'];
	$retrievedCourse = Database::getCoursebyID($searchId);
	//if the id provided is not actually a valid course then redirect
	if ( !isset( $retrievedCourse['id'] ) )
	{
		$message = urlencode( "The course provided is not valid." );
		header( "Location: error.php?error=${message}" );
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
		echo "var PHP_token = \"{$token}\";\n";
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
			$user = Database::getUserId( Session::user() );
			$account = Database::getAccount( $user, $searchId );
			if ( $account !== NULL && $account->canUpload() )
			{
			?>
			<div id="uploadFrame" class="upload">
				<a id="uploadLink" href="#">Upload Notes</a>
			</div>
			<?php
			}
			?>
			<p>
			<?php echo $retrievedCourse['name'] . " - " . $retrievedCourse['semester']; ?>
			</p>
			<p>
			Instructor: <?php echo $retrievedCourse['instructor']; ?>
			</p>
			</header>
			<main>
		
			<?php 
			if (empty($notes)) {
				echo "No notes found for this course.";
			} else {
			foreach ($notes as $note) {
			?>
				<div class="float"><a href="download.php?id=<?php echo $note['id']?>"><img src="images/pdf.png" alt="" height="150" width=auto></a>
				<?php
				if ( $account !== NULL && $account->canDelete() )
				{
				?>
				<div title='Remove File' class="topleft" onclick='<?php echo "location.href=\"form.php?note=${note['id']}\"" ?>'></div>
				<?php
				}?>
				<p><?php echo $note['lectureDate']; ?></p></div>
				<?php } }?>

			</main>
		</article>
		
		<footer id="foot1">
		<p>The University of Arizona | All contents copyright &copy; 2016. Arizona Board of Regents</p>
	</footer>
	
	
	</body>
	
	
</html>
