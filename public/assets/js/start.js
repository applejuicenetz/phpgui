$(document).ready(function(){
	setInterval(function(){//setInterval() method execute on every interval until called clearInterval()
		$('#load_posts').load("src/GUI/ajax.php").fadeIn("slow");
	 	//load() method fetch data from fetch.php page
	}, 1000);
	
   });