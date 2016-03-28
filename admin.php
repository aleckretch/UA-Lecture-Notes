<?php
/*
	Shows admin page for a specific course to instructors.
	Shows admin page to add courses for admin.
*/
require_once "./database.php";
require_once "./session.php";

//if the user is not logged in, redirect them to the login page
if ( !Session::userLoggedIn() )
{
	header( "Location: login.php" );
	exit();
}

$token = Session::token();
if ( isset( $_GET['course'] ) )
{
	//show the admin page for instructors

	//if the user does not have permission to see the admin page for the course then redirect them to the home page
	$course = $_GET['course'];
	$retrievedCourse = Database::getCoursebyID($course);
	if ( !isset( $retrievedCourse['id'] ) )
	{
		$message = urlencode( "The course provided is not valid." );
		header( "Location: error.php?error=${message}" );
		exit();
	}

	$user = Database::getUserId( Session::user() );
	$account = Database::getAccount( $user, $course );
	if ( $account === NULL || $account->canPromote() !== TRUE )
	{
		$message = urlencode( "You do not have permission to add uploaders for this course." );
		header( "Location: error.php?error=${message}" );
		exit();
	}
	$token = Session::token();
?>
<!doctype html>
<html>
	<head>
    <meta charset="utf-8">
    <title>Arizona Notes</title>
	  
	<link rel="stylesheet" type="text/css" href="css/main.css">
	<link rel="stylesheet" type="text/css" href="css/fonts.css">
	<script src="js/jquery-2.1.4.min.js"></script>
	<script src="js/upload.js"></script>
	</head>
	
	<body>
		<form id='removalForm' method='post' action='form.php'>
			<input id='removeToken' type='hidden' name='token' value="<?php echo $token;?>">
			<input id='removedValue' type='hidden' name='removed'>
			<input id='removedCourse' type='hidden' name='remove' value='<?php echo $_GET['course'];?>'>
		</form>
		<div class="darken_div"></div>
		<div class="main-logo">
			<a href="index.php">
			<img src="images/logo.png" height="90px" width=auto></a>	
		</div>
		
		<article class="main-content">
			<header>
			<p>
			<?php echo $retrievedCourse['name'] . " - " . $retrievedCourse['semester']; ?>
			</p>
			<p>
			Instructor: <?php echo $retrievedCourse['instructor']; ?>
			</p>
			</header>
			<main>
				<div class='leftDiv'>
				<div class='divHead'>Uploaders</div>
			<?php
				//show a list of accounts that can upload
				//form to add an account to the list
				//x button to remove an account from the list
				$uploaders = Database::getUploadersForCourse( $course );
				foreach ( $uploaders as $user )
				{
					$id = $user['id'];
					$data = Database::getUserData( $id );
					if ( !isset( $data['username'] ) )
					{
						continue;
					}
					echo "<div>${data['username']} - <a title='Remove uploader' onclick='return removeNote( ${id} );' href='#'>X</a></div>";
				}
			?>
				</div>
				<div class='rightDiv'>
					<div class='divHead'>Add Uploader</div>
					<form method='POST' action='form.php?uploader=yes'>
						NetID: <br><input type='text' name='user' /><br>
						<input type='hidden' name='course' value='<?php echo "${course}";?>'/>
						<input type='hidden' name='token' value='<?php echo "${token}";?>'/>
						<input type='submit' value='Add'/><br>
					</form>
				</div>
			</main>
		</article>
	</body>
	</html>
<?php
}
else
{
	//show the admin page for admins
	//redirect if the user is not an admin
	if ( !Session::getAdmin() )
	{
		$message = urlencode( "You do not have permission to view this page." );
		header( "Location: error.php?error=${message}" );
		exit();
	}
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
		<div class="darken_div"></div>
		<div class="main-logo">
			<a href="index.php">
			<img src="images/logo.png" height="90px" width=auto></a>	
		</div>
		
		<article class="main-content">
			<header>
			<p>
			Admin Panel
			</p>
			<p>
			Add Course
			</p>
			</header>
			<main>
				<div>
					<form method='POST' action='form.php?course=yes'>
						Course Name: <br><input type='text' name='name' placeholder='CSC 335'/><br>
						Semester: <br><input type='text' name='semester' placeholder='Spring 2016'/><br>
						Instructor Name: <br><input type='text' name='instructor'/><br>
						Instructor NetID: <br><input type='text' name='netid' /><br>
						<input type='hidden' name='token' value='<?php echo "${token}";?>'/>
						<input type='submit' value='Add Course'/><br>
					</form>
				</div>

			</main>
		</article>
	</body>
	</html>
<?php
}

