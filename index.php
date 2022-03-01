<!DOCTYPE html>
<html>

	<head>

		<meta charset="UTF-8">
		<meta name="viewport" content="user-scalable=no, width=device-width">

		<title>NdW Stricherliste</title>

		<link rel="stylesheet" type="text/css" href="stylesheet.css">
		<!--So geht ein Kommentar-->
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"> </script>

		<script>
				$.get("namen.json",
					  function(data) {
						for (i in data) {
						  $("#name").append('<option value=' + data[i] + '>' + data[i] + '</option>');
						}
			  });

		</script>
		
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
				</select></td>
				<script>	

					var select = document.getElementById("name");
					for(index in example_array) 
					{
						select.options[select.options.length] = new Option(example_array[index], index);
					}
				</script>
				<td><input name="bier" type="number"  step="1" min="0" ></textarea></td>
				<td><input name="toast" type="number" step="1" min="0" ></td>
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
				$list = fgetcsv($myfile);
				fclose($myfile);
				$myfile = fopen("stricherliste.csv", "a") or die("Unable to open file!");
				$txt = strval($_POST["name"]) . ";Bier;" . strval($_POST["bier"]) . ";Toast;" . strval($_POST["toast"]) . "\n";
				fwrite($myfile, convertToWindowsCharset($txt));
				fclose($myfile);
			}
		 
	
		?>
		
		


	</body>

</html>