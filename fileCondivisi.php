<?php
	#Inizio a contare
	$start = microtime(true);


	$location = $_GET["loc"];

	#Per disabilitare il login commentare la riga quì sotto
	#include "checkLogin.php";

	#In caso la riga sopra sia commentata
	if (!isset($isAuthenticated)){
		$isAuthenticated = 1;
	}

	if($isAuthenticated) {

		#Usando $location come parametro di input restituisce
		#il path assoluto delle directory attraverso il
		#parametro $loc come output
		include "folderLocations.php";

		#Cambio nuovamente la root directory
		chdir($loc);
?>



<html>

<head>
	<meta charset="utf-8"/>


        <style type="text/css">
		/*tr{border: dotted 1px black;}*/
		@font-face {font-family: 'OpenSans-Regular'; src: url('images/OpenSans-Regular.ttf');}
		body {font-family: "OpenSans-Regular";}
		td{font-size: small;}
		td > a{text-decoration: none; color: #000;}
		td > a:hover {text-decoration: underline;}

		.dateCol {padding-right:5px;}
		.position {margin-top:0px; font-size: small;}
		.titleLoc {margin-bottom:0px;}
	</style>


	<title>File Condivisi</title>
</head>

<body>

<!--
	<!- - Log out option - ->
	<form class="controlbox" name="Logout" id="logout" action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST">
		<input type="hidden" name="op" value="logout"/>
		<input type="hidden" name="username"value="<?php echo $_SESSION["username"]; ?>" />
		<div align="center" style="border:1px solid black">
			Buonsalve <?php echo $_SESSION["username"]; ?>!
		<div align="center">
		<input type="submit" value="log out"/>
		</div>
			<a href="index.php">Home</a>
		</div>
	</form>
-->

<?php
		$defaultDir = ".";


		#Controllo se ho rivecuto il parametro dir tramite GET, se non l'ho ricevuto ne
		#assegno uno io di default (di solito la root folder "."). In caso abbia ricevuto
		#il parametro GET devo controllare che non contenga la substring "..", in caso la
		#la contenga sostituisco il paramentro ricevuto con quello di default.
		##Note that the use of !== false is deliberate; strpos returns either the offset
		##at which the needle string begins in the haystack string, or the boolean false
		##if the needle isn't found. Since 0 is a valid offset and 0 is "falsey", we
		##can't use simpler constructs like !strpos($a, 'are')
		if(isset($_GET["dir"])){
			$directory = $_GET["dir"];
			if(strpos($directory, "..") !== false){
				$directory = $defaultDir;
			}
		}
		else{
			$directory = $defaultDir;
		}

		#Mostra la posizione
		echo '<h1 class="titleLoc">' . $location . '</h1>';
		echo '<p class="position">' . $directory . '</p>';

		#Se sono in una sottodirectory della root folder, compare il tasto "Indietro" che mi fa tornare alla cartella superiore
		if($directory != $defaultDir){
			echo '<a href="?loc=' . $location . '&dir=' . dirname($directory) . '">↵Indietro</a>';
		}
		apriDirectory($directory, $location);
	}

	#Se l'utente non è loggato viene fatto il redirect alla login page
	else{
		header('Location: login.php?redirect='. urlencode(currentUrl()));
	}


	#[Debug] Decommentare la riga sotto per il debug
	#echo '<p>POST:' . str_replace("\n", "<br/>\n\t\t\t", print_r($_POST, true)) . '</p>';

	#[Debug] Decommentare la riga sotto per il debug
	#echo '<p>GET:' . str_replace("\n", "<br/>\n\t\t\t", print_r($_GET, true)) . '</p>';

	#Finisco di contare
	$end = microtime(true);
	$finish = round(($end - $start)*100, 3);
	echo  "<p>Tempo di esecuzione dello script: <i>" . $finish . "ms</i>.</p>";

?>
</body>
</html>
<?php

	function apriDirectory($directory, $location){
		if ($handle = opendir($directory)) {
			echo "<table bordercolor='#f6f6f6' border=1 frame=void rules=rows>";
			echo "<tr><td><b>Filename</b></td><td><b>Ultima Modifica</b></td><td><b>Dimensione</b></td></tr>";
			$i = 0;
			while (false !== ($file = readdir($handle)))
			{
				if($i%2==0){echo "<tr bgcolor='#f6f6f6'>";}else{echo "<tr>";}
				
				if ($file != "." && $file != ".." && is_dir($directory . "/" . $file))
				{
					echo '<td><img src="images/ico_folder.gif" title="Vai a docs" /><a href="'.$_SERVER['PHP_SELF'].'?loc=' . $location . '&dir=' . $directory . '/' . $file . '">'.$file.'</a></td><td class="dateCol">' . date("y-m-d H:i:s T",filemtime($directory . "/" . $file)) . '</td><td></td>';
		                        $i = $i + 1;
				}

				if ($file != "." && $file != ".." && !is_dir($directory . "/" . $file) && strtolower(substr($file, strrpos($file, '.') + 1)) != 'php')
				{
					echo '<td><img src="images/ico_file.gif" title="Apri README" /><a href="download.php?loc=' . $location . '&file=' . urlencode($directory . '/' . $file) . '">'.$file.'</a></td><td class="dateCol">' . date("y-m-d H:i:s T",filemtime($directory . "/" . $file)) . '</td><td> ' . formatSizeUnits(filesize($directory . "/" . $file)) . '</td>';
					$i = $i + 1;
				}
				echo "</tr>";
			}
			closedir($handle);
			echo "</table>";
			if($i == 0)
				echo "Nessun file presente.";
		}
	}



	function formatSizeUnits($bytes)
	    {
		if ($bytes >= 1073741824)
		{
		    $bytes = number_format($bytes / 1073741824, 2) . ' GB';
		}
		elseif ($bytes >= 1048576)
		{
		    $bytes = number_format($bytes / 1048576, 2) . ' MB';
		}
		elseif ($bytes >= 1024)
		{
		    $bytes = number_format($bytes / 1024, 2) . ' kB';
		}
		elseif ($bytes > 1)
		{
		    $bytes = $bytes . ' bytes';
		}
		elseif ($bytes == 1)
		{
		    $bytes = $bytes . ' byte';
		}
		else
		{
		    $bytes = '0 bytes';
		}

		return $bytes;
	}



	function currentUrl()
		{
		$protocol = strpos(strtolower($_SERVER['SERVER_PROTOCOL']),'https')=== FALSE ? 'http' : 'https';
		$currentUrl = $protocol.'://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING'];

		return $currentUrl;
		}

	function rutime($ru, $rus, $index) {
	    return ($ru["ru_$index.tv_sec"]*1000 + intval($ru["ru_$index.tv_usec"]/1000))
	     -  ($rus["ru_$index.tv_sec"]*1000 + intval($rus["ru_$index.tv_usec"]/1000));
	}


?>
