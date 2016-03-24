$( document ).ready( function()
{
	$( "#uploadLink" ).click( function() 
	{
		var today = new Date();
		var dd = today.getDate();
		var mm = today.getMonth() + 1; //January is 0
		var yyyy = today.getFullYear();
		//get the current date
		//if the current day of the month is less than ten then add a leading zero
		if ( dd < 10 ) 
		{
		    dd = '0' + dd;
		} 

		//if the current month is less than ten add a leading zero
		if ( mm < 10 ) 
		{
		    mm = '0' + mm;
		} 			

		//format the date so that the server knows what date the lecture file is for
		var myDate = mm + "/" + dd + "/" + yyyy;
		var str = "<form id='uploadForm' action='upload.php' enctype='multipart/form-data' method='post'>";
		//PHP_token and PHP_course should be echoed out in in_class.php in a script tag
		str += "<input type='hidden' name='token' value='" + PHP_token + "'>";
		str += "<input type='hidden' name='course' value='" + PHP_course + "'>";
		//put the date in a hidden input, the form processing expects a date field for the notes file
		//in the future, may allow the user to specify an actual date for what lecture date the notes file was for
		str += "<input type='hidden' name='date' value='" + myDate + "'>";
		str += "<input type='file' id='fileUp' name='file'>";	
		$( "#uploadFrame" ).html( str );	
		//make it so that when the file input has an actual file chosen, submit the form automatically
		$( '#fileUp' ).on( 'change' , function( event ){
			console.log( 'hello' );
			$( '#uploadForm' ).submit();
		}
		);
	});
}
);
