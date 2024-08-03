<?php
$Write = "<?php $" . "UIDresult=''; " . "echo $" . "UIDresult;" . " ?>";
file_put_contents('UIDContainer.php', $Write);
?>

<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta charset="utf-8">
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<script src="js/bootstrap.min.js"></script>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script>
		$(document).ready(function(){
			$("#getUID").load("UIDContainer.php");
			setInterval(function() {
				$("#getUID").load("UIDContainer.php");
			}, 500);
		});

		function getUrlParameter(name) {
			name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
			var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
			var results = regex.exec(location.search);
			return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
		}

		function myTimer() {
			var getID = document.getElementById("getUID").innerHTML;
			oldID = getID;
			if (getID != "") {
				myVar1 = setInterval(myTimer1, 500);
				showUser(getID);
				clearInterval(myVar);
			}
		}

		function myTimer1() {
			var getID = document.getElementById("getUID").innerHTML;
			if (oldID != getID) {
				myVar = setInterval(myTimer, 500);
				clearInterval(myVar1);
			}
		}

		function showUser(uid) {
			if (uid == "") {
				document.getElementById("show_user_data").innerHTML = "";
				return;
			} else {
				let xmlhttp;
				if (window.XMLHttpRequest) {
					xmlhttp = new XMLHttpRequest();
				} else {
					xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
				}
				xmlhttp.onreadystatechange = function() {
					if (this.readyState == 4 && this.status == 200) {
						document.getElementById("show_user_data").innerHTML = this.responseText;
					}
				};
				let product_id = getUrlParameter('product_id');
				let params = "id=" + uid + "&product_id=" + product_id;
				xmlhttp.open("GET", "read tag user data_privé.php?" + params, true);
				xmlhttp.send();
			}
		}

		var myVar = setInterval(myTimer, 1000);
		var myVar1 = setInterval(myTimer1, 1000);
		var oldID = "";
		clearInterval(myVar1);

		function validatePurchase() {
			var userId = document.getElementById("getUID").innerHTML;
			alert("Achat validé pour l'utilisateur ID: " + userId);
			// Vous pouvez ajouter ici une logique supplémentaire pour valider l'achat, comme envoyer les données au serveur
		}

		var blink = document.getElementById('blink');
		setInterval(function() {
			blink.style.opacity = (blink.style.opacity == 0 ? 1 : 0);
		}, 750);
	</script>
	<style>
		html {
			font-family: Arial;
			display: inline-block;
			margin: 0px auto;
			text-align: center;
		}

		ul.topnav {
			list-style-type: none;
			margin: auto;
			padding: 0;
			overflow: hidden;
			background-color: #4CAF50;
			width: 70%;
		}

		ul.topnav li {float: left;}

		ul.topnav li a {
			display: block;
			color: white;
			text-align: center;
			padding: 14px 16px;
			text-decoration: none;
		}

		ul.topnav li a:hover:not(.active) {background-color: #3e8e41;}

		ul.topnav li a.active {background-color: #333;}

		ul.topnav li.right {float: right;}

		@media screen and (max-width: 600px) {
			ul.topnav li.right,
			ul.topnav li {float: none;}
		}

		td.lf {
			padding-left: 15px;
			padding-top: 12px;
			padding-bottom: 12px;
		}
	</style>

	<title>Application IoT</title>
</head>

<body>
	<h2 align="center">Application IoT</h2>

	<h3 align="center" id="blink">Scanner votre Carte s'il vous plaît !</h3>

	<p id="getUID" hidden></p>

	<br>

	<div id="show_user_data">
		<form>
			<table width="452" border="1" bordercolor="#10a0c5" align="center" cellpadding="0" cellspacing="1" bgcolor="#000" style="padding: 2px">
				<tr>
					<td height="40" align="center" bgcolor="#10a0c5"><font color="#FFFFFF">
						<b>User Data</b>
					</font></td>
				</tr>
				<tr>
					<td bgcolor="#f9f9f9">
						<table width="452" border="0" align="center" cellpadding="5" cellspacing="0">
							<tr bgcolor="#f2f2f2">
								<td align="left" class="lf">Name</td>
								<td style="font-weight:bold">:</td>
								<td align="left">--------</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</form>
	</div>
</body>
</html>
