<?php
session_start();
$_SESSION['views']=0;
?><html>
<head>
<link rel="stylesheet" href="base_css.php" />
<script language='javascript' type='text/javascript'>
<!--
var g_id_code;
g_xmlhttp = new XMLHttpRequest();
g_xmlhttp.onreadystatechange = function() {
	switch (g_xmlhttp.readyState) {
		case 3:
			var g_code = g_xmlhttp.responseText;
			document.getElementById("TEST").innerText = "#3# " + g_code;
			break;
		case 4:
			var g_code = g_xmlhttp.responseText;
			document.getElementById("TEST").innerText = "#4# " + g_code;
			break;
	}
}
function performTrans() {
	var l_message = 'test';
	g_xmlhttp.open('POST','CubeToolScripts.php',true);
	g_xmlhttp.responseType = "text";
	g_xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	g_xmlhttp.send(l_message);
}
-->
</script></head><body oncontextmenu="return false;" onbeforeunload="return parent.CheckChangePending()" ondrop="Drop(event)" ondragover="AllowDrop(event)">
<div><img src="icons/dot_large.bmp" /><span> EXECUTE</span></div>
<hr/>
<table>
<tr id="RowAtbName"><td><u><div>Name</div></u></td><td><div style="max-width:30em;"><input id="InputName" type="text" maxlength="30" style="width:100%" onchange="SetChangePending();ToUpperCase(this);ReplaceSpaces(this);"></input></div></td></tr>
<tr><td><br></td><td style="width:100%"></td></tr>
<tr><td/><td>
<button id="ButtonOK" type="button" onclick="performTrans()">OK</button>&nbsp;&nbsp;&nbsp;
<button id="ButtonCancel" type="button" disabled>Cancel</button></td></tr>
</table>
<span id="TEST" style="display:block;">script output</span
<input id="InputCubeId" type="hidden"></input>
</body>
</html>
