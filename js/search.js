$(document).ready(function(){
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
	
	$('#search_input').keyup(function() {
		var keywords =$('#search_input').val();
		if (keywords ==""){
			
			$('.autoComplete_div').html("");
			return;
		}
		
		$.ajax()
		
		
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
		xhttp.open("GET", "form.php?searchKey=" + keywords, true);
		xhttp.send();
		
	});
});
