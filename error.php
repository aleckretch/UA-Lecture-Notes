<?php
	require_once "./database.php";
	$error = "";
	if ( isset( $_GET['error'] ) )
	{
		$error = $_GET['error'];
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
				<p>Oops, something went wrong!</p>
				<div class='leftDiv'><?php echo Database::sanitizeData( $error );?></div>
			</header>
		</article>
	</body>
	</html>
