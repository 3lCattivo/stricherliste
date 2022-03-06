<!DOCTYPE html>
<html>

	<head>
		<!--looki looki -> https://w3codegenerator.com/article/how-to-display-select-option-of-select-tag-as-selected-using-foreach-method-in-php -->
		<meta charset="UTF-8">
		<meta name="viewport" content="user-scalable=no, width=device-width">

		<title>NdW Stricherliste</title>
		<link rel="stylesheet" type="text/css" href="stylesheet.css" >

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

			#Get Names, Menu and Prices from Array and write to csv
			$names = array();
			$name_file = fopen("barliste_template.csv", "r") or die("Unable to open file!");
			
			#Get Menu
			$line = fgetcsv($name_file, 1000, ";");

			#Get Prices
			$line = fgetcsv($name_file, 1000, ";");
			
			while (($line = fgetcsv($name_file, 1000, ";")) !== FALSE) {
				if($line[0] != ""){
					array_push($names, convertToUTF8($line[0]));
				}
			}		
			fclose($name_file);
			
			$numberOfColumns = 6;
			$numberOfRows = ceil(count($names) / $numberOfColumns);
		?>
			
			

		
		
	</head>

	<body>
		<h1>Die Stricherliste</h1>
		<form method="post" action="order.php">
			<table>
				<?php
					for($j = 0; $j < $numberOfRows; $j++){	
						echo('<tr>');
						for($i = 0; $i <$numberOfColumns and ($numberOfColumns*$j+$i) < count($names) ; $i++){
							echo('<td><button class="button" type="submit" name="name_select" value=' . $numberOfColumns * $j + $i . '>' . $names[$numberOfColumns * $j + $i] . '</button></td>');
						}
						echo('</tr>');
					}	
				?>
			</table> 
		</form>

	</body>

</html>