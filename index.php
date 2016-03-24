<?php
	require_once "./database.php";
	require_once "./session.php";

	if ( !Session::userLoggedIn() )
	{
		header( "Location: login.php" );
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
	<script src="js/jquery-2.1.4.min.js"></script>
	<script src="js/search.js"></script>
</head>
<body>
	<div class="darken_div"></div>
	<div class="header_div">
		<div class="main-logo">
			<a href="index.php">
			<img src="images/logo.png" height="90px" width=auto></a>	
		</div>
	</div>
	<div class="description_div">
		<a>Find your class. Stay up to date in lectures.</a>
	</div>
	<div class="searchbar_group">
		<div class=" searchbar_div">
			<div class="search_icon_div">
				<img src="images/Layer-1.png" />
			</div>
			<div class="search_input_div">
				<input id="search_input" type="text" />
			</div>
		</div>
		<div class="autoComplete_div">
		</div>
	</div>
	<footer id="foot1">
		<p>The University of Arizona | All contents copyright &copy; 2016. Arizona Board of Regents</p>
	</footer>
</body>
</html>
