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
		
		
			#Get HOTList
			$hotlist_name = array();
			$hotlist_number = array();
			$hotlist_file = fopen("hotlist.csv", "r") or die("Unable to open file!");
			fgetcsv($hotlist_file, 1000, ";");         #Header
			while (($line = fgetcsv($hotlist_file, 1000, ";")) !== FALSE) {
				array_push($hotlist_name, convertToUTF8($line[0]));
			}		
			fclose($hotlist_file);
			
			$numberOfColumns = 6;
			$numberOfRows = ceil(count($names) / $numberOfColumns);
			$numberOfHotlistRows = ceil(count($hotlist_name) / $numberOfColumns);
			
			for ($i = 0; $i < count($hotlist_name); $i++){
				array_push( $hotlist_number, array_search($hotlist_name[$i], $names));
			}
			array_multisort($hotlist_name, $hotlist_number);
			
		?>
			
		<script>	
		function autocomplete(inp, arr) {
		  /*the autocomplete function takes two arguments,
		  the text field element and an array of possible autocompleted values:*/
		  var currentFocus;
		  /*execute a function when someone writes in the text field:*/
		  inp.addEventListener("input", function(e) {
			  var a, b, i, val = this.value;
			  /*close any already open lists of autocompleted values*/
			  closeAllLists();
			  if (!val) { return false;}
			  currentFocus = -1;
			  /*create a DIV element that will contain the items (values):*/
			  a = document.createElement("DIV");
			  a.setAttribute("id", this.id + "autocomplete-list");
			  a.setAttribute("class", "autocomplete-items");
			  /*append the DIV element as a child of the autocomplete container:*/
			  this.parentNode.appendChild(a);
			  /*for each item in the array...*/
			  for (i = 0; i < arr.length; i++) {
				/*check if the item starts with the same letters as the text field value:*/
				if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
				  /*create a DIV element for each matching element:*/
				  b = document.createElement("DIV");
				  /*make the matching letters bold:*/
				  b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
				  b.innerHTML += arr[i].substr(val.length);
				  /*insert a input field that will hold the current array item's value:*/
				  b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
				  /*execute a function when someone clicks on the item value (DIV element):*/
					  b.addEventListener("click", function(e) {
					  /*insert the value for the autocomplete text field:*/
					  inp.value = this.getElementsByTagName("input")[0].value;
					  /*close the list of autocompleted values,
					  (or any other open lists of autocompleted values:*/
					  closeAllLists();
				  });
				  a.appendChild(b);
				}
			  }
		  });
		  /*execute a function presses a key on the keyboard:*/
		  inp.addEventListener("keydown", function(e) {
			  var x = document.getElementById(this.id + "autocomplete-list");
			  if (x) x = x.getElementsByTagName("div");
			  if (e.keyCode == 40) {
				/*If the arrow DOWN key is pressed,
				increase the currentFocus variable:*/
				currentFocus++;
				/*and and make the current item more visible:*/
				addActive(x);
			  } else if (e.keyCode == 38) { //up
				/*If the arrow UP key is pressed,
				decrease the currentFocus variable:*/
				currentFocus--;
				/*and and make the current item more visible:*/
				addActive(x);
			  } else if (e.keyCode == 13) {
				/*If the ENTER key is pressed, prevent the form from being submitted,*/
				e.preventDefault();
				if (currentFocus > -1) {
				  /*and simulate a click on the "active" item:*/
				  if (x) x[currentFocus].click();
				}
			  }
		  });
		  function addActive(x) {
			/*a function to classify an item as "active":*/
			if (!x) return false;
			/*start by removing the "active" class on all items:*/
			removeActive(x);
			if (currentFocus >= x.length) currentFocus = 0;
			if (currentFocus < 0) currentFocus = (x.length - 1);
			/*add class "autocomplete-active":*/
			x[currentFocus].classList.add("autocomplete-active");
		  }
		  function removeActive(x) {
			/*a function to remove the "active" class from all autocomplete items:*/
			for (var i = 0; i < x.length; i++) {
			  x[i].classList.remove("autocomplete-active");
			}
		  }
		  function closeAllLists(elmnt) {
			/*close all autocomplete lists in the document,
			except the one passed as an argument:*/
			var x = document.getElementsByClassName("autocomplete-items");
			for (var i = 0; i < x.length; i++) {
			  if (elmnt != x[i] && elmnt != inp) {
			  x[i].parentNode.removeChild(x[i]);
			}
		  }
		}
		/*execute a function when someone clicks in the document:*/
		document.addEventListener("click", function (e) {
			closeAllLists(e.target);
		});
		} 
		</script>
		
	</head>

	<body>
		<!-- SuchFeld -->
		<!-- php write to csv latest customers? -->
		<h1>Die HOTList</h1>
		
		<table>
			<form method="post" action="order.php" autocomplete="off">
				<tr>
					<td ><div class="autocomplete">
						<input id="names_autocomplete" type="text" name="names_autocomplete" placeholder="Name">
						</div>
					</td>
					<td>
						<input type="submit" value="Submit">
					</td>
				</tr>
				<!--<tr><td> <select name="name_select" id="dropdown_names">
				<?php 
					for($j = 0; $j < count($names); $j++){	
						echo('<option value=' . $j . '>' . $names[$j] . '</option>');
					}
				?>
				</select></td>
				</td><td><input type="submit" value="Submit"></td>
				</tr>-->
			</form>
			<form method="post" action="order.php">
				<?php
					for($j = 0; $j < $numberOfHotlistRows; $j++){	
						echo('<tr class="hotlist">');
						for($i = 0; $i <$numberOfColumns and ($numberOfColumns*$j+$i) < count($hotlist_name) ; $i++){
							echo('<td><button class="button" type="submit" name="name_select" value=' . $hotlist_number[$numberOfColumns * $j + $i] . '>' . $hotlist_name[$numberOfColumns * $j + $i] . '</button></td>');
						}
						echo('</tr>');
					}	
				?>
			</form>
		</table> 
<!--		
		<h1>Die Stricherliste</h1>
		
		<form method="post" action="order.php">
			<table>
				<?php
					#for($j = 0; $j < $numberOfRows; $j++){	
					#	echo('<tr>');
					#	for($i = 0; $i <$numberOfColumns and ($numberOfColumns*$j+$i) < count($names) ; $i++){
					#		echo('<td><button class="button" type="submit" name="name_select" value=' . $numberOfColumns * $j + $i . '>' . $names[$numberOfColumns * $j + $i] . '</button></td>');
					#	}
					#	echo('</tr>');
					#}	
				?>
			</table> 
		</form>
-->


		<script>
			var names_autocomplete = <?php echo json_encode($names); ?>;
			autocomplete(document.getElementById("names_autocomplete"), names_autocomplete);
		</script> 
	</body>

</html>