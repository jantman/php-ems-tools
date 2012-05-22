<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>{$title}</title>
<link rel="stylesheet" type="text/css" href="signout.css" />
<script language="javascript" type="text/javascript" src="inc/equipSignout.js"></script>
<script language="javascript" type="text/javascript" src="inc/ajax.js"></script>
</head>

<body>
<h1>{$title}</h1>


<form name="equipSignout" method="POST">

<div>
<label for="date">Date: </label>
<input type="text" name="date" id="date" size="12" value="{$smarty.now|date_format:$config.date}" />
</div>

<div>
<label for="type">Type: </label>
<select name="type" id="type" onchange="javascript:updateType()" >
	<option value="-1">Select a Type:</option>
	<option value="1">uniform</option>
	<option value="2">pager</option>
	<option value="3">radio</option>
</select>
</div>

<div id="byType">

</div> <!-- end byType div -->

<div>
<label for="officer">Authorizing Officer: </label>
<input type="text" name="officer" id="officer" size="4" /> <em>(ID#)</em>
</div>

</form>

</body>

</html>