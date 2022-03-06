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
			
			function convertToUTF8($string) {
				$charset =  mb_detect_encoding($string,"UTF-8, ISO-8859-1, ISO-8859-15",true);
				$string =  mb_convert_encoding($string, "UTF-8", $charset);
				return $string;
			}
			function convertToWindowsCharset($string) {
				$charset =  mb_detect_encoding($string,"UTF-8, ISO-8859-1, ISO-8859-15",true);
				$string =  mb_convert_encoding($string, "Windows-1252", $charset);
				return $string;
			}
			function convertDecimalSeparator_PointToComma($string) {
				$string = str_replace('.', ',', $string);
				return $string;
			}
			function convertDecimalSeparator_CommaToPoint_StringToNumber($string) {
				$string = str_replace(',', '.', $string);
				$number= floatval($string);
				return $number;
			}
			
			#Get Filename
			$filename = 'barliste_' . date('WY') . '.csv';
			$filename_log = 'barliste_log_' . date('WY') . '.csv';
			if(!file_exists($filename)){
				$fp = fopen($filename, "w");
				fclose($fp);
			}
			if(!file_exists($filename_log)){
				$fp = fopen($filename_log, "w");
				fclose($fp);
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
					array_push($names, convertToUTF8($line[0]));
				}
			}		
			fclose($name_file);
			$numberOfColumns = 6;
			$numberOfRows = ceil((count($menu) + 2) / $numberOfColumns); # +2 because of comment and diverse
		?>
		
		
	</head>

	<body>
	
		<form method="post">
			<h1>Die Stricherliste</h1>
			<table>
				<tr>
					<td><select name="name" id="name">
					<?php
						foreach($names as $key => $val){
							echo('<option value=' . $key . '>' . $val . '</option>');
						}
					?>
					</select></td>
				</tr>
					<?php
						for($j = 0; $j < $numberOfRows; $j++){
							echo('<tr>');
							echo('<th></th>');
							for($i = 0; $i <$numberOfColumns and ($numberOfColumns*$j+$i) < count($menu) ; $i++){
								echo('<th>' . $menu[$numberOfColumns * $j + $i] . '</th>');
							}
							echo('</tr><tr>');
							echo('<th></th>');
							for($i = 0; $i <$numberOfColumns and ($numberOfColumns*$j+$i) < count($menu); $i++){
								echo('<th>' . $prices[$numberOfColumns * $j + $i] . '</th>');	
							}
							echo('</tr><tr>');
							echo('<th></th>');
							for($i = 0; $i <$numberOfColumns and ($numberOfColumns*$j+$i) < count($menu); $i++){
								echo('<td><input name=' . 'menu'. ($numberOfColumns * $j + $i) . ' type="number" step="1" min="0", max="100" ></td>');		
							}
							echo('</tr>');
						}
					?>
				<tr>
				<td></td><th>Diverses</th><th>Kommentar</th>
				</tr>
				<tr>
				<td></td><th>(in Euro)</th><th></th>
				</tr>
				<tr>
					<td></td><td><input name="diverse" type="number" min="0"max="9999" step=0.01></td>
					<td><input name="comment" type="text" maxlength="500" autocomplete="off"></td>
				</tr>
				<tr><td></td>
				</tr>
				<tr>
					<td></td><td></td><td></td><td></td><td><button type="submit">Hinzufügen!</button> </td>
				</tr>
			</table> 
		</form>
		
		
		
		

		<?php
			if ($_SERVER['REQUEST_METHOD'] == 'POST'){
				$inp_menu = array();
				for($i = 0; $i < count($menu); $i++){
					$inp_menu[$i] = filter_input(INPUT_POST, 'menu' . $i, FILTER_SANITIZE_NUMBER_INT);
				}
				
				$inp_name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_NUMBER_INT);
				$inp_diverse = filter_input(INPUT_POST, 'diverse', FILTER_SANITIZE_STRING);
				$inp_comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING);
				
				
				#Get data from list
				$bierliste = fopen($filename, "r") or die("Unable to open file!");
				$bier_data = array();
				while (($line = fgetcsv($bierliste, 1000, ";")) !== FALSE) {
					array_push($bier_data, $line);
					}
				fclose($bierliste);
				
				#Write Logfile
				$bierliste_log = fopen($filename_log, "a") or die("Unable to open file!");
				$txt = "";
				foreach($menu as $key => $val){
					$txt = $txt . str_replace("\n","",$val) . ";" . str_replace("\n","",$inp_menu[$key]) . ";";
				}
				$txt =$txt . "Diverses;" . convertDecimalSeparator_PointToComma($inp_diverse) . ";";
				$txt =$txt . "Kommentar;" . str_replace("\n","",strip_tags($inp_comment)) . ";";
				$txt = $names[$inp_name] . ';' . $txt;
				fwrite($bierliste_log, (convertToWindowsCharset($txt) . "\n"));
				fclose($bierliste_log);
				
				
				#Add new order to list
				
				#Write Menu to barliste
				$bierliste = fopen($filename, "w") or die("Unable to open file!");
				$bier_data[0][0] = "Bierliste";
				for($i = 0; $i < count($menu); $i++){
					$bier_data[0][$i+1] = convertToWindowsCharset($menu[$i]);
				}
				$bier_data[0][$i +1] = convertToWindowsCharset("Diverses");
				$bier_data[0][$i +2] = convertToWindowsCharset("Kommentar");
				
				#Write Names to barliste
				for($i = 0; $i < count($names); $i++){
					$bier_data[$i+1][0] = convertToWindowsCharset($names[$i]);
				}

				#Add order to barliste
				for($i = 0; $i <= count($names); $i++){	
					if(convertToUTF8($bier_data[$i][0]) == $names[$inp_name]){
						foreach ($menu as $key => $val){
							if (array_key_exists($key + 1, $bier_data[$i])){
								$bier_data[$i][$key + 1] = intval($bier_data[$i][$key + 1]) + intval($inp_menu[$key]);
							}
							else{
								$bier_data[$i][$key + 1] = intval($inp_menu[$key]);
							}
							
						}
						if (array_key_exists($key + 2, $bier_data[$i])){
								$bier_data[$i][$key + 2] = convertDecimalSeparator_CommaToPoint_StringToNumber($bier_data[$i][$key + 2]) + floatval($inp_diverse);
								$bier_data[$i][$key + 2] = convertDecimalSeparator_PointToComma($bier_data[$i][$key + 2]);
							}
							else{
								$bier_data[$i][$key + 2] = floatval(strip_tags($inp_diverse));
								$bier_data[$i][$key + 2] = convertDecimalSeparator_PointToComma($bier_data[$i][$key + 2]);
							}
						if (array_key_exists($key + 3, $bier_data[$i])){
								$bier_data[$i][$key + 3] = convertToWindowsCharset($bier_data[$i][$key + 3] . ";" . $inp_comment);
							}
							else{
								$bier_data[$i][$key + 3] = convertToWindowsCharset($inp_comment);
							}
					}
					fputcsv($bierliste, $bier_data[$i], ";");
				}
				fclose($bierliste);
			}
		?>
		
	</body>

</html>