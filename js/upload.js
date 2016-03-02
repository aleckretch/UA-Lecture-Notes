$( document ).ready( function()
{
	$( "#uploadLink" ).click( function() 
	{
		var str = "<form action='upload.php' enctype='multipart/form-data' method='post'>";
		str += "<input type='hidden' name='token' value='" + PHP_token + "'>";
		str += "<input type='hidden' name='course' value='" + PHP_course + "'>";
		//str += "Date:<br><input type='date' name='date'><br>";
		str += "<input type='file' name='file'>";	
		str += "<input type='submit' value='Send'></form>";
		$( "#uploadFrame" ).html( str );	
	});
}
);
