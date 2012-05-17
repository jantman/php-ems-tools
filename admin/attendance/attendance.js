document.onkeyup = KeyCheck; 
var currRow = 0;
var numRows = 0;
var started = 0;

function startMe()
{
	updateBorders();
	numRows = document.getElementById("rowCount").value;
	started = 1;
}

function updateBorders()
{
	if(currRow > 0)
	{
		document.getElementById("name_"+(currRow-1)).style.border = "1px solid black";
		document.getElementById("id_"+(currRow-1)).style.border = "1px solid black";
		document.getElementById("check_"+(currRow-1)).style.border = "1px solid black";
	}

	document.getElementById("name_"+currRow).style.border = "3px solid red";
	document.getElementById("id_"+currRow).style.border = "3px solid red";
	document.getElementById("check_"+currRow).style.border = "3px solid red";
}

function finish()
{
	document.getElementById("name_"+(numRows-1)).style.border = "1px solid black";
	document.getElementById("id_"+(numRows-1)).style.border = "1px solid black";
	document.getElementById("check_"+(numRows-1)).style.border = "1px solid black";

	document.getElementById("submit").style.border = "3px solid red";
	document.getElementById("submit").focus();
}

function KeyCheck(e)
{
  
  if ( started == 0 ) { 
    return -1;
  }
  
   var KeyID = (window.event) ? event.keyCode : e.keyCode;
   pressedChar = String.fromCharCode(KeyID);

   if( pressedChar == "P" || pressedChar == "1" || pressedChar == "a")
   {
	document.getElementById(currRow + "_p").checked = true;
	currRow = currRow + 1;
	if(currRow == numRows)
	{
		finish();
		return;
	}
	updateBorders();
   }
   else if( pressedChar == "A" || pressedChar == "2" || pressedChar == "b")
   {
	document.getElementById(currRow + "_a").checked = true;
	currRow = currRow + 1;
	if(currRow == numRows)
	{
		finish();
		return;
	}
	updateBorders();
   }
   else if( pressedChar == "E" || pressedChar == "3" || pressedChar == "c")
   {
	document.getElementById(currRow + "_e").checked = true;
	currRow = currRow + 1;
	if(currRow == numRows)
	{
		finish();
		return;
	}
	updateBorders();
   }
   else if( pressedChar == "W" || pressedChar == "4" || pressedChar == "d")
   {
	document.getElementById(currRow + "_w").checked = true;
	currRow = currRow + 1;
	if(currRow == numRows)
	{
		finish();
		return;
	}
	updateBorders();
   }
   else if( pressedChar == "L" || pressedChar == "5" || pressedChar == "e")
   {
	document.getElementById(currRow + "_l").checked = true;
	currRow = currRow + 1;
	if(currRow == numRows)
	{
		finish();
		return;
	}
	updateBorders();
   }
   else if( pressedChar == "S" || pressedChar == "6" || pressedChar == "f")
   {
	document.getElementById(currRow + "_s").checked = true;
	currRow = currRow + 1;
	if(currRow == numRows)
	{
		finish();
		return;
	}
	updateBorders();
   }
  
}