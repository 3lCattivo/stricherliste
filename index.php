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

		
		
	</head>

	<body>
	
	
	<?php 
		#Hier Namen aus File holen und in array schreiben.
		$names = array();
		$name_file = fopen("namen.csv", "r") or die("Unable to open file!");
		while (($line = fgetcsv($name_file, 1000, ";")) !== FALSE) {
			array_push($names, $line[0]);
		}		
		fclose($name_file);
		$data = $names#array(1 =>"Martl", 2=>"Diegl", 3=>"Boris"); ?>
	
	
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
				foreach($data as $key => $val){
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

				function convertToWindowsCharset($string) {
					$charset =  mb_detect_encoding($string,"UTF-8, ISO-8859-1, ISO-8859-15",true);
					$string =  mb_convert_encoding($string, "Windows-1252", $charset);
					return $string;
				}
				
				$myfile = fopen("stricherliste.csv", "r") or die("Unable to open file!");
				$data = array();
				while (($line = fgetcsv($myfile, 1000, ";")) !== FALSE) {
					array_push($data, $line);
					}
				fclose($myfile);
				
				$myfile = fopen("stricherliste_log.csv", "a") or die("Unable to open file!");
				$txt = strval($_POST["name"]) . ";Bier;" . strval($_POST["bier"]) . ";Toast;" . strval($_POST["toast"]) . "\n";
				fwrite($myfile, convertToWindowsCharset($txt));
				fclose($myfile);
				
				$liste = fopen("stricherliste.csv", "w") or die("Unable to open file!");
				$header = array("Name", "Bier", "Toast");
				#fputcsv($liste, $header, ";");
				$name_flag = False;
				foreach ($data as $element) {
					if($element[0] == $_POST["name"]){
						$element[1] += intval($_POST["bier"]);
						$element[2] += intval($_POST["toast"]);
						$name_flag = True;
					}

					fputcsv($liste, $element, ";");
				}
				if($name_flag == False){
					$new_name = array($_POST["name"], intval($_POST["bier"]), intval($_POST["toast"]));
					fputcsv($liste, $new_name, ";");
				}
				fclose($liste);

			}
		?>
		
	</body>

</html>