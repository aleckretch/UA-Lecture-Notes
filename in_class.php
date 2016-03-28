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
		$message = urlencode( "The course provided does not exist" );
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
		<form id='removalForm' method='post' action='form.php'>
			<input id='removeToken' type='hidden' name='token' value="<?php echo $token;?>">
			<input id='removedValue' type='hidden' name='note'>
		</form>
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
			//if the current user can upload notes, add a link to allow them to upload a file
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
			
				$note_limit = 12; // 12 notes per page
				$note_count = count($notes);
				if ( isset($_GET['page'])) 
				{
					$page = $_GET['page'] + 1;
					$offset = $note_limit * $page;
				} 
				else 
				{
					$page = 0;
					$offset = 0;
				}
				
				$current_notes = Database::getNotesByCourseLimited($searchId, $offset, $note_limit);
				if ( empty( $current_notes ) )
				{
					echo "No notes found for this page.";
				}
				foreach ($current_notes as $note) {
				?>
				<div class="float"><a href="download.php?id=<?php echo $note['id']?>"><img src="images/pdf.png" alt="" height="150" width=auto></a>
				<?php
				//if the current user can delete notes files then add a delete link to every notes icon
				if ( $account !== NULL && $account->canDelete() )
				{
				?>
				<div title='Remove File' class="topleft" onclick='removeNote( <?php echo $note['id']; ?>)'></div>
				<?php
				}?>
				<p><?php echo $note['lectureDate']; ?></p></div>
				

			<?php } 
			echo "</main>";
			echo "<div class='perNext'>";
			$last = $page;
			$perClass = "";
			$nextClass = "";
			//if past the first page, then add previous page link
			if( $page > 0 ) 
			{
				//-2 since pages begin at -1 and are added 1 to
           	 		$last = $page - 2;
         		}
			else if( $page == 0 ) 
			{
				//since this is the first page, give the previous link a disabled class so it does not do anything
				$perClass = "disabled";
         		}
			
			if( count( $current_notes ) < $note_limit ) 
			{
				//if there are less than the total possible notes on this page then it is the last page
				//give the next page link a disabled class so it does nothing when clicked
				$nextClass = "disabled";
        		}

			?>
			<a class="per <?php echo $perClass;?>" href = "in_class.php?id=<?php echo $searchId;?>&page=<?php echo $last;?>" > &#10094; Prev </a> -
			<a class="next <?php echo $nextClass;?>" href = "in_class.php?id=<?php echo $searchId;?>&page=<?php echo $page;?>" >Next &#10095;</a>
			<?php
			echo "</div>";
         }?>
		</article>
		
		<footer id="foot1">
		<p>The University of Arizona | All contents copyright &copy; 2016. Arizona Board of Regents</p>
	</footer>
	
	
	</body>
	
	
</html>
