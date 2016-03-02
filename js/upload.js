$( document ).ready( function()
{
	$( "#uploadLink" ).click( function() 
	{
		var today = new Date();
		var dd = today.getDate();
		var mm = today.getMonth()+1; //January is 0!
		var yyyy = today.getFullYear();
		if ( dd < 10 ) 
		{
		    dd = '0' + dd;
		} 

		if ( mm < 10 ) 
		{
		    mm = '0' + mm;
		} 			
		var myDate = mm + "/" + dd + "/" + yyyy;
		var str = "<form id='uploadForm' action='upload.php' enctype='multipart/form-data' method='post'>";
		str += "<input type='hidden' name='token' value='" + PHP_token + "'>";
		str += "<input type='hidden' name='course' value='" + PHP_course + "'>";
		str += "<input type='hidden' name='date' value='" + myDate + "'>";
		str += "<input type='file' id='fileUp' name='file'>";	
		//str += "<input type='submit' value='Send'></form>";
		$( "#uploadFrame" ).html( str );	
		$( '#fileUp' ).on( 'change' , function( event ){
			console.log( 'hello' );
			$( '#uploadForm' ).submit();
		}
		);
	});
}
);
