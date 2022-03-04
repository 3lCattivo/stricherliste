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
			#Get Names from Array and write to csv
			$names = array();
			$name_file = fopen("namen.csv", "r") or die("Unable to open file!");
			while (($line = fgetcsv($name_file, 1000, ";")) !== FALSE) {
				array_push($names, convertToWindowsCharset($line[0]));
			}		
			fclose($name_file);
		?>
		
		
	</head>

	<body>
	
		<form method="post">
		<h1>Die Stricherliste</h1>
		<table>
			<tr>
				<th>Name</th>
				<th>Bier</th>
				<th>Toast</th>
			</tr>
			<tr>
			<td><select name="name" id="name">
			<?php
				foreach($names as $key => $val){
					echo('<option value=' . $val . '>' . $val . '</option>');
				}
			?>

				</select></td>
				<td><input name="bier" type="number"  step="1" min="0", max="100" ></textarea></td>
				<td><input name="toast" type="number" step="1" min="0" max="100" ></td>
			</tr>
		</form>
		</table> 
		
		
		<button type="submit">Hinzufügen!</button> 

		<?php
			if ($_SERVER['REQUEST_METHOD'] == 'POST'){
				
				#Get data from list
				$bierliste = fopen("stricherliste.csv", "r") or die("Unable to open file!");
				$bier_data = array();
				while (($line = fgetcsv($bierliste, 1000, ";")) !== FALSE) {
					array_push($bier_data, $line);
					}
				fclose($bierliste);
				
				#Write Logfile
				$bierliste_log = fopen("stricherliste_log.csv", "a") or die("Unable to open file!");
				$txt = strval($_POST["name"]) . ";" . strval($_POST["bier"]) . ";" . strval($_POST["toast"]) . "\n";
				fwrite($bierliste_log, convertToWindowsCharset($txt));
				fclose($bierliste_log);
				
				#Add new order to list
				$bierliste = fopen("stricherliste.csv", "w") or die("Unable to open file!");
				$header = array("Name", "Bier", "Toast");

				foreach ($bier_data as $element) {
					if($element[0] == $_POST["name"]){
						$element[1] = intval($element[1]) + intval($_POST["bier"]);
						$element[2] = intval($element[2]) + intval($_POST["toast"]);
					}
					fputcsv($bierliste, $element, ";");
				}
				fclose($bierliste);
			}
		?>
		
	</body>

</html>