<!DOCTYPE html>
<html>

	<head>
		<!--looki looki -> https://w3codegenerator.com/article/how-to-display-select-option-of-select-tag-as-selected-using-foreach-method-in-php -->
		<meta charset="UTF-8">
		<meta name="viewport" content="user-scalable=no, width=device-width">

		<title>NdW Stricherliste</title>

		<link rel="stylesheet" type="text/css" href="stylesheet.css">

		<!--So geht ein Kommentar-->
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"> </script>
		
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
			
			#Get Names
			while (($line = fgetcsv($name_file, 1000, ";")) !== FALSE) {
				if($line[0] != ""){
					array_push($names, convertToUTF8($line[0]));
				}
			}		
			fclose($name_file);
			$numberOfColumns = 5;
			$numberOfRows = ceil((count($menu) + 2) / $numberOfColumns); # +2 because of comment and diverse
			
			$inp_name_select = filter_input(INPUT_POST, 'name_select', FILTER_SANITIZE_NUMBER_INT);
			$inp_name_set = array_key_exists('name', $_POST);
			
			
			#Update HotList
			$hotlist_name = array();
			$hotlist_file = fopen("hotlist.csv", "r") or die("Unable to open file!");
			fgetcsv($hotlist_file, 1000, ";");         #Header
			while (($line = fgetcsv($hotlist_file, 1000, ";")) !== FALSE) {
				array_push($hotlist_name, convertToUTF8($line[0]));
			}		
			fclose($hotlist_file);
			if(in_array($names[$inp_name_select], $hotlist_name)){
				array_splice($hotlist_name, array_search($names[$inp_name_select], $hotlist_name), 1); #remove name from list
			}
			else{
				array_pop($hotlist_name); # remove last name
			}
			array_unshift($hotlist_name,$names[$inp_name_select]); #put name in front
			#write names to hotlist file
			$hotlist_file = fopen("hotlist.csv", "w") or die("Unable to open file!");
			fputcsv($hotlist_file, ["Hotlist Name"]);
			for ($i=0; $i<count($hotlist_name); $i++){
				fputcsv($hotlist_file, [$hotlist_name[$i]]);
			}		
			fclose($hotlist_file);
		?>
		
		
	</head>

	<body class="ndwbody">
		<!-- Menu List -->
		<form method="post">
			<input type="hidden" id="name" name="name" value=<?php echo($inp_name_select);?>>
			<input type="hidden" id="name_select" name="name_select" value=<?php echo($inp_name_select);?>>
			<h1><?php echo($names[$inp_name_select]);?></h1>
			<table class='table_order'>
					<?php
						for($j = 0; $j < $numberOfRows; $j++){
							echo('<tr>');
							echo('<th></th>');
							for($i = 0; $i <$numberOfColumns and ($numberOfColumns*$j+$i) < count($menu); $i++){
								echo('<td><button class="button_menu" type="button" name="choose_order" onclick=addListItem(' . $numberOfColumns * $j + $i . ')>' . $menu[$numberOfColumns * $j + $i] . '<br>' . $prices[$numberOfColumns * $j + $i] . '</button></td>');
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
					<td></td><td><input id="get_diverse" name="get_diverse" type="number" min="0"max="9999" step=0.01></td>
					<td><input id="get_comment" name="get_comment" type="text" maxlength="500" autocomplete="off"></td>
				</tr>
				<tr><td></td>
				</tr>
				<tr>
				</tr>
			</table> 
		</form>
		

		
		<?php
			if ($inp_name_set){
				
				$inp_menu = array();
				for($i = 0; $i < count($menu); $i++){
					if (array_key_exists('menu'.$i, $_POST)){
						$inp_menu[$i] = filter_input(INPUT_POST, 'menu'.$i, FILTER_SANITIZE_NUMBER_INT);
					}
					else{
						$inp_menu[$i] = 0;
					}
				}
				$inp_name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_NUMBER_INT);
				$inp_diverse = filter_input(INPUT_POST, 'diverse', FILTER_SANITIZE_STRING);
				$inp_comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING);
				
				
				
				//foreach($menu as $key => $val){
				//	echo("<p>" .$inp_menu[$key]. "</p>");
				//}
				
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
				header('Location: index.php');
				exit();
			}
		?>
		
		
		<!-- Table Monitor -->
		<form method="post">
			<input type="hidden" id="comment" name="comment" value = "">
			<input type="hidden" id="diverse" name="diverse" value = "0">
			<input type="hidden" id="name" name="name" value=<?php echo($inp_name_select)?>>
			<input type="hidden" id="name_select" name="name_select" value=<?php echo($inp_name_select);?>>
			<table class="tableMonitor" id="table">
			
				<!--<col width="200px" />
				<col width="250px" />
				<col width="50px" />
				<col width="0px" />
				<col width="0px" />-->
			
				<tbody id="tbody_monitor">
			
					<tr class = "trOrder">
						<td class="cell">Diverses:</td><td class="cell" id= "td_diverse"><td class="close"><button type="button" onClick="resetDiverse()">&times</button></td>
					</tr>
					<tr class ="trOrder">
						<td class="cell">Kommentar:</td><td id= "td_comment"><td class="close"><button type='button' onClick="resetComment()">&times</button></td>
					</tr>
				</tbody>
			</table>
			<button type=submit>Gogogo</button>
		</form>

		
		
		<script>
		/* Get all elements with class="close" */

		var js_menu = <?php echo json_encode($menu)?>;
		var js_prices = <?php echo json_encode($prices)?>;
		var itemArray = [];
		var closebtns;
		
		
		function addListItem(element) {
			var table = document.getElementById("table");
			if (!itemArray.includes(element)){
				var row = table.insertRow(-1);
				row.className = 'trOrder';
				var cell0 = row.insertCell(0);
				var cell1 = row.insertCell(1);
				var cell2 = row.insertCell(2);
				var cell3 = row.insertCell(3);
				var cell4 = row.insertCell(4);
				
				cell0.innerHTML = js_menu[element];
				cell1.innerHTML = "1";
				cell2.innerHTML = '<button type="button" >&times</button>'; 
				cell3.innerHTML = '<input type="hidden" id=amount' + element + ' name=menu' + element + ' value=1>';
				cell0.className = 'cell';
				cell1.className = 'cell';
				cell2.className = 'close';
				cell3.className = 'cell';
				cell2.id = element;
				itemArray.push(element);
				closebtns = document.getElementById(element);
				closebtns.addEventListener("click", closeButton);
			}
			else{
				var table = document.getElementById('table');
				for (var r = 0, n = table.rows.length; r < n; r++) {
					if(table.rows[r].cells[0].innerHTML == js_menu[element]){
						table.rows[r].cells[1].innerHTML = parseInt(table.rows[r].cells[1].innerHTML) + 1;
						document.getElementById("amount"+ element).value = parseInt(document.getElementById("amount" + element).value) + 1;
					}
				}
			}
		}
		
		
		function closeButton() {
			var amount = this.parentNode.childNodes[1].innerHTML;
			if (amount > 1){
				this.parentNode.childNodes[1].innerHTML = parseInt(this.parentNode.childNodes[1].innerHTML) - 1;
				this.parentNode.childNodes[3].childNodes[0].value = this.parentNode.childNodes[3].childNodes[0].value - 1;

			}
			else {
				this.parentNode.remove();
				var index = itemArray.indexOf(parseInt(this.id));
				if (index != -1) {
					itemArray.splice(index, 1);
				}
			}
		}
		
		//Add Diverse and Comment to Monitor table
		document.getElementById("get_diverse").addEventListener("keyup", function(event) {
			if (event.key === "Enter") {
				document.getElementById("td_diverse").innerHTML = document.getElementById("get_diverse").value;
				document.getElementById("diverse").value = document.getElementById("get_diverse").value;
			}
		 });
		 document.getElementById("get_comment").addEventListener("keyup", function(event) {
			if (event.key === "Enter") {
				document.getElementById("td_comment").innerHTML = document.getElementById("get_comment").value;
				document.getElementById("comment").value = document.getElementById("get_comment").value;
			}
		 });
		 
		 
		 function resetDiverse(){
				document.getElementById('td_diverse').innerHTML = '';
				document.getElementById('diverse').innerHTML = 0;
			}
		function resetComment(){
				document.getElementById('td_comment').innerHTML = '';
				document.getElementById('comment').innerHTML = 0;
			}
		 
		
		</script>
	</body>

</html>