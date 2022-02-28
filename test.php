<!DOCTYPE html>
<html>

	<head>

		<meta charset="UTF-8">
		<meta name="viewport" content="user-scalable=no, width=device-width">

		<title>NdW Stricherliste</title>

		<link rel="stylesheet" type="text/css" href="stylesheet.css">
		<!--So geht ein Kommentar-->
		
	</head>
	
	<body>
	<p>Stricherliste</p>
	
	<?php
	
	function convertToWindowsCharset($string) {
		$charset =  mb_detect_encoding($string,"UTF-8, ISO-8859-1, ISO-8859-15",true);
		$string =  mb_convert_encoding($string, "Windows-1252", $charset);
		return $string;
	}

	$myfile = fopen("stricherliste.csv", "r") or die("Unable to open file!");
	$list = fgetcsv($myfile);
	fclose($myfile);
	$myfile = fopen("stricherliste.csv", "a") or die("Unable to open file!");
	$txt = strval($_POST["name"]) . ";Bier;" . strval($_POST["bier"]) . ";Toast;" . strval($_POST["toast"]) . "\n";
	fwrite($myfile, convertToWindowsCharset($txt));
	fclose($myfile);
	?> 
	
	</body>
</html>