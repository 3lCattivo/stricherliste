<!DOCTYPE html>
<html>

	<head>
		<!--looki looki -> https://w3codegenerator.com/article/how-to-display-select-option-of-select-tag-as-selected-using-foreach-method-in-php -->
		<meta charset="UTF-8">
		<meta name="viewport" content="user-scalable=no, width=device-width">

		<title>NdW Stricherliste</title>

		<link rel="stylesheet" type="text/css" href="stylesheet.css">
		<!--So geht ein Kommentar-->
		<!--<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"> </script> -->
		
		<?php 
			header('Content-type: text/html; charset=utf-8');
			
			function convertToWindowsCharset($string) {
				$charset =  mb_detect_encoding($string,"UTF-8, ISO-8859-1, ISO-8859-15",true);
				$string =  mb_convert_encoding($string, "UTF-8", $charset);
				return $string;
			}
			
			
			#Get Names, Menu and Prices from Array and write to csv
			$names = array();
			$menu = array();
			$prices = array();
			$name_file = fopen("barliste_template.csv", "r") or die("Unable to open file!");
			
			#Get Menu
			$line = fgetcsv($name_file, 1000, ";");
			array_shift($line);
			$menu = $line;
			#Get Prices
			$line = fgetcsv($name_file, 1000, ";");
			array_shift($line);
			$prices = $line;
			
			while (($line = fgetcsv($name_file, 1000, ";")) !== FALSE) {
				if($line[0] != ""){
					array_push($names, convertToWindowsCharset($line[0]));
				}
			}		
			fclose($name_file);
		?>
		
		
	</head>

	<body>
	
		<form method="post">
		<h1>Die Stricherliste</h1>
		<table>
		
			<tr>
				<!-- Header -->
				<th>Name</th>
				<?php
					for ($i = 0; $i <= 5 or $i > count($menu); $i++) {
						#echo('<th>' . $menu[$key] . " " . $prices[$key] . '</th>');
						echo('<th>' . $menu[$i] . '</th>');
					}
				?>
			</tr>
			
			<tr>
				<th>Preis</th>
				<?php
					for ($i = 0; $i <= 5 or $i > count($menu); $i++) {
						echo('<th>' . $prices[$i] . '</th>');
					}
				?>				
			</tr>
			
			<tr>
				<!-- Inputs! -->
				<td><select name="name" id="name">
				<?php
					foreach($names as $key => $val){
						echo('<option value=' . $key . '>' . $val . '</option>');
					}
				?>
				</select></td>
				
				<?php
					for ($i = 0; $i <= 5 or $i > count($menu); $i++) {
						echo('<td><input name=' . 'menu'. $i . ' type="number" step="1" min="0", max="100" ></td>');
					}
				?>
			
			<!-- 2. Runde: -->
			<tr>
				<!-- Header -->
				<th>Name</th>
				<?php
					for ($i = 6; $i <= 10 or $i > count($menu); $i++) {
						#echo('<th>' . $menu[$key] . " " . $prices[$key] . '</th>');
						echo('<th>' . $menu[$i] . '</th>');
					}
				?>
				<th>Diverses (in Euro)</th>
				<th>Kommentar</th>
			</tr>
			</tr>
			
			<tr>
				<th>Preis</th>
				<?php
					for ($i = 6; $i <= 10 or $i > count($menu); $i++) {
						echo('<th>' . $prices[$i] . '</th>');
					}
				?>				
			</tr>
			
			<tr>
				<!-- Inputs! -->
				<th></th>
				<?php
					for ($i = 6; $i <= 10 or $i > count($menu); $i++) {
						echo('<td><input name=' . 'menu'. $i . ' type="number" step="1" min="0", max="100" ></td>');
					}
				?>
				<td><input name="diverse" type="number" step="1" min="0"max="9999"></td>

				<td><input name="comment" type="text" maxlength="500" ></td>
			</tr>
		</form>
		</table> 
		
		
		<button type="submit">Hinzufügen!</button> 

		<?php
			if ($_SERVER['REQUEST_METHOD'] == 'POST'){
				#Get data from list
				$bierliste = fopen("barliste.csv", "r") or die("Unable to open file!");
				$bier_data = array();
				while (($line = fgetcsv($bierliste, 1000, ";")) !== FALSE) {
					array_push($bier_data, $line);
					}
				fclose($bierliste);
				
				#Write Logfile
				#$bierliste_log = fopen("stricherliste_log.csv", "a") or die("Unable to open file!");
				#$txt = strval($_POST["name"]) . ";" . strval($_POST["bier"]) . ";" . strval($_POST["toast"]) . "\n";
				#fwrite($bierliste_log, convertToWindowsCharset($txt));
				#fclose($bierliste_log);
				
				#Add new order to list
				$bierliste = fopen("barliste.csv", "w") or die("Unable to open file!");
				foreach ($bier_data as $element) {					
					if($element[0] == $names[strip_tags($_POST["name"])]){
						foreach ($menu as $key => $val){
							$element[$key + 1] = intval($element[$key + 1]) + intval(strip_tags($_POST["menu" . $key]));
						}
						$element[$key + 2] = intval($element[$key + 2]) + intval(strip_tags($_POST["diverse"]));
						$element[$key + 3] = $element[$key + 3] . ";" . strip_tags($_POST["comment"]);
					}
					fputcsv($bierliste, $element, ";");
				}
				fclose($bierliste);
			}
		?>
		
	</body>

</html>