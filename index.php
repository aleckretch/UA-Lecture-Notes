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
<script>
	$(document).ready(function(){
		var result =[
			{
			id:0,
			subject:'CSC',
			crourse_num: 352,
			crouse_name:'Object-Oriented Principles',
			term: 'Spring 2016',
				instr:'Patrick Homer'
			},
			{
			id:1,
			subject:'CSC',
			crourse_num: 352,
			crouse_name:'Object-Oriented Principles',
			term: 'Spring 2016'	,
				instr:'Patrick Homer'
				
			},
			{
			id:3,
			subject:'AFAS',
			crourse_num: 202,
			crouse_name:'Object-Oriented Principles',
			term: 'Spring 2016',
				instr:'Patrick Homer'
				
			},
			{
			id:4,
			subject:'ISTA',
			crourse_num: 151,
			crouse_name:'Object-Oriented Principles',
			term: 'Spring 2016'	,
			instr:'Patrick Homer'
				
			}
			
		];
		function highlightMatch(searchKey, str) { 
			var result = str;
			str = str.toLowerCase(); // probably should've used regexp, oh well
			searchKey = searchKey.toLowerCase();
			var i = str.indexOf(searchKey);
			if (i != -1) {
				var toReplace = result.substr(i, searchKey.length);
				result = result.replace(toReplace, toReplace.bold());
			}
			return result;
		}
		//console.log(result);
		
		$('#search_input').keyup(function() {
			var keywords =$('#search_input').val();
			if (keywords ==""){
				
				$('.autoComplete_div').html("");
				return;
			}
			var html = "";
			var xhttp = new XMLHttpRequest();
			// anonymous callback will execute upon server response
			xhttp.onreadystatechange = function() {
				// States 0 1 2 3 4 (4 means success)
				// 404 is bad xhttp.status, 200 is good
				if (xhttp.readyState == 4 && xhttp.status == 200) {
					// parse JSONArray (from courseSearch.php)
					console.log(xhttp.responseText);
					var array = JSON.parse(xhttp.responseText);
					
					for (i in array){
						var a = array[i];
						var text = '<div class="autoComplete-item" onclick="location.href=\'in_class.php?id=' + a.id + '\'"><span class="title">' 
						+
						highlightMatch(keywords, a.courseName) + " -- "+ a.semester +
						'</span><span class="instr">Instuctor: ' +
						highlightMatch(keywords, a.instructor)+ '</span></div>';
					html+=text;
					}
			
			 	$('.autoComplete_div').html(html);
				}
			}
			// use GET
			xhttp.open("GET", "courseSearch.php?searchKey=" + keywords, true);
			xhttp.send();
			
		});
	});
</script>

</html>
