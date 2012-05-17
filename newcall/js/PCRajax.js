/*
 +--------------------------------------------------------------------------------------------------------+
 | PHP EMS Tools      http://www.php-ems-tools.com                                                        |
 +--------------------------------------------------------------------------------------------------------+
 | Time-stamp: "2010-01-10 16:46:37 jantman"                                                              |
 +--------------------------------------------------------------------------------------------------------+
 | Copyright (c) 2009, 2010 Jason Antman. All rights reserved.                                            |
 |                                                                                                        |
 | This program is free software; you can redistribute it and/or modify                                   |
 | it under the terms of the GNU General Public License as published by                                   |
 | the Free Software Foundation; either version 3 of the License, or                                      |
 | (at your option) any later version.                                                                    |
 |                                                                                                        |
 | This program is distributed in the hope that it will be useful,                                        |
 | but WITHOUT ANY WARRANTY; without even the implied warranty of                                         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                                          |
 | GNU General Public License for more details.                                                           |
 |                                                                                                        |
 | You should have received a copy of the GNU General Public License                                      |
 | along with this program; if not, write to:                                                             |
 |                                                                                                        |
 | Free Software Foundation, Inc.                                                                         |
 | 59 Temple Place - Suite 330                                                                            |
 | Boston, MA 02111-1307, USA.                                                                            |
 +--------------------------------------------------------------------------------------------------------+
 |Please use the above URL for bug reports and feature/support requests.                                  |
 +--------------------------------------------------------------------------------------------------------+
 | Authors: Jason Antman <jason@jasonantman.com>                                                          |
 +--------------------------------------------------------------------------------------------------------+
 | $LastChangedRevision:: 48                                                                            $ |
 | $HeadURL:: http://svn.jasonantman.com/newcall/js/PCRajax.js                                          $ |
 +--------------------------------------------------------------------------------------------------------+
*/

var http = createRequestObject(); 

function createRequestObject()
{
	var request_o;
	var browser = navigator.appName;
	if(browser == "Microsoft Internet Explorer")
	{
		request_o = new ActiveXObject("Microsoft.XMLHTTP");
	}
	else
	{
		request_o = new XMLHttpRequest();
	}
	return request_o;
}

function update_state()
{
  var foo = document.getElementById("pt_AddressState").value;
  if(foo == "NJ")
    {
      document.getElementById("pt_city_other_span").style.display = "none";
      document.getElementById("pt_AddressCity").style.display = "inline";
    }
  else
    {
      document.getElementById("pt_city_other_span").style.display = "inline";
      document.getElementById("pt_AddressCity").value = "--Other";
      document.getElementById("pt_AddressCity").style.display = "none";
      document.getElementById("pt_AddressStreetMP").style.display = "none";
      document.getElementById("pt_AddressStreet").style.display = "inline";
    }
  
}

function update_city()
{
  var foo = document.getElementById("pt_AddressCity").value;
  if(foo == "--Other")
    {
      document.getElementById("pt_city_other_span").style.display = "inline";
    }
  else
    {
      document.getElementById("pt_city_other_span").style.display = "none";
    }
  
  if(foo == "Midland Park")
    {
      document.getElementById("pt_AddressStreet").style.display = "none";
      document.getElementById("pt_AddressStreetMP").style.display = "inline";
    }
  else
    {
      document.getElementById("pt_AddressStreet").style.display = "inline";
      document.getElementById("pt_AddressStreetMP").style.display = "none";
    }
}

function update_MA()
{
  var foo = document.getElementById("MAcheck").checked;
  if(foo == true)
    {
      document.getElementById("MA_town").style.display = "inline";
    }
  else
    {
      document.getElementById("MA_town").style.display = "none";
    }
}

function update_MA_town()
{
  var foo = document.getElementById("MA_town").value;
  if(foo == "--Other")
    {
      document.getElementById("MA_town_other_span").style.display = "inline";
    }
  else
    {
      document.getElementById("MA_town_other_span").style.display = "none";
    }
}

function update_DOB()
{
  var foo = document.getElementById("pt_DOB").value;
  
  re = /^\d{2}\/\d{2}\/\d{4}$/; 
  if(!foo.match(re))
    {
      alert(i18n['ERROR'] + ": " + i18n['invalidDateFormat'] + " " + i18n['DOB'] + ".");
      return;
    }
  
  var t = Date.parse(foo);
  var now = new Date();
  
  if(t >= now)
    {
      alert(i18n['ERROR'] + ": " + i18n['invalidDOB'] + " - " + i18n['futureDOB']);
      return;
    }
  var diff = now - t; // milliseconds difference
  diff = Math.floor(diff / 1000); // seconds difference, integer
  diff = Math.floor(diff / 31556926); // years difference, floor
  document.getElementById("pt_age").value = diff;
}

function update_AidGiven()
{
  if(document.getElementById("AidGivenBy_Other").checked == true)
    {
      document.getElementById("AidGivenBy_Other_Text").style.display = "inline";
    }
  else
    {
      document.getElementById("AidGivenBy_Other_Text").style.display = "none";
    }
}

function update_OC()
{
  oc_undo();
  if(document.getElementById("OC_Refusal").checked == true)
  {
    // TODO - not implemented - Refusal
  }
  else if(document.getElementById("OC_DOA").checked == true)
  {
    // TODO - not implemented - DOA
  }
  else if(document.getElementById("OC_Canceled").checked == true)
  {
    // TODO - not implemented - Canceled
  }
  else if(document.getElementById("OC_NoCrew").checked == true)
  {
    oc_nocrew();
  }
  else if(document.getElementById("OC_Other").checked == true)
  {
    // TODO - not implemented - Other
  }
}

function formatTime(id)
{
  var foo = document.getElementById(id).value;
  if(foo.charAt(2) != ':')
  {
    var a = foo.substr(0,2);
    var b = foo.substr(2);
    document.getElementById(id).value = a + ":" + b;
  }
}

//
// Text area clicks that pull up selection boxes
//

//
// treatments form
//

function click_tx()
{
  doHTTPrequest("txForm.php?tx=" + escape(document.getElementById("tx").value), handleTxFormRequest);
}

function handleTxFormRequest()
{
  if(http.readyState == 4)
  {
    var response = http.responseText;
    document.getElementById('popupbody').innerHTML = response;
    showPopup("Treatments &amp; Interventions");
  }
}

function submitTxForm()
{
  var s = "";
  for(i=0; i<document.TxForm.elements.length; i++)
  {   
    if(document.TxForm.elements[i].type=="checkbox" && document.TxForm.elements[i].checked == true)
    {  
      if(s != ""){ s = s + ", ";}
      s = s + document.TxForm.elements[i].value;
    }
  }
  
  // oxygen handling
  var liters = document.getElementById("TxForm_O2L").value;
  if((document.getElementById("TxForm_O2_Nasal").checked == true || document.getElementById("TxForm_O2_NRB").checked == true || document.getElementById("TxForm_O2_BlowBy").checked == true) && document.getElementById("TxForm_O2L").value == "")
  {
    alert("You must enter a flow rate (LPM) if you select an oxygen delivery method.");
    return;
  }
  if(document.getElementById("TxForm_O2_Nasal").checked == true)
  {
    if(s != ""){ s = s + ", ";}
    s = s + "Oxygen - " + liters + "LPM Nasal Cannula";
  }
  if(document.getElementById("TxForm_O2_NRB").checked == true)
  {
    if(s != ""){ s = s + ", ";}
    s = s + "Oxygen - " + liters + "LPM Non-Rebreather";
  }
  if(document.getElementById("TxForm_O2_BlowBy").checked == true)
  {
    if(s != ""){ s = s + ", ";}
    s = s + "Oxygen - " + liters + "LPM Blow-By";
  }
  document.getElementById("tx").value = s;
  hidePopup();
}

//
// injured area form
//

function click_injarea()
{
  doHTTPrequest("injForm.php?inj=" + escape(document.getElementById("injured_area").value), handleInjFormRequest);
}

function handleInjFormRequest()
{
  if(http.readyState == 4)
  {
    var response = http.responseText;
    document.getElementById('popupbody').innerHTML = response;
    showPopup("Injured Area");
  }
}

function submitInjForm()
{
  var s = "";
  for(i=0; i<document.InjForm.elements.length; i++)
  {   
    if(document.InjForm.elements[i].type=="checkbox" && document.InjForm.elements[i].checked == true)
    {  
      if(s != ""){ s = s + ", ";}
      s = s + document.InjForm.elements[i].value;
    }
  }
  
  document.getElementById("injured_area").value = s;
  hidePopup();
}

function update_call_loc(other)
{
  if(other == 0)
    {
      document.getElementById("CallLocDiv").style.display = "none";
    }
  else
    {
      document.getElementById("CallLocDiv").style.display = "inline";
    }
}

function addCrew()
{
  // adds a row to the crew table
  var num = parseInt(document.getElementById("nextCrewRow").value);
  var tblBody = document.getElementById("crewTable").tBodies[0];
  var newRow = tblBody.insertRow(-1);

  var newCell0 = newRow.insertCell(0);
  newCell0.innerHTML = '<input id="crew_id_' + num +  '" name="crew_id_' + num +  '" size="4" type="text" onblur="javascript:update_crew_member(' + num + ')" />';
  var newCell1 = newRow.insertCell(1);
  newCell1.innerHTML = '<input id="crew_driver_scene_' + num +  '" name="crew_driver_scene" value="' + num +  '" type="radio" />';
  var newCell2 = newRow.insertCell(2);
  newCell2.innerHTML = '<input id="crew_driver_hosp_' + num +  '" name="crew_driver_hosp" value="' + num +  '" type="radio" />';
  var newCell3 = newRow.insertCell(3);
  newCell3.innerHTML = '<input id="crew_driver_bldg_' + num +  '" name="crew_driver_bldg" value="' + num +  '" type="radio" />';
  var newCell4 = newRow.insertCell(4);
  newCell4.innerHTML = '<input type="checkbox" id="crew_onscene' + num +  '" name="crew_onscene' + num +  '" />';
  var newCell5 = newRow.insertCell(5);
  newCell5.innerHTML = '<input id="crew_genDuty_' + num +  '" name="crew_genDuty_' + num +  '" size="4" readonly="readonly" value="Gen" type="text" />';
  
  document.getElementById("nextCrewRow").value = num + 1;
}

function addVitalsRow()
{
  // adds a row to the crew table
  var num = parseInt(document.getElementById("nextVitalsRow").value);
  var tblBody = document.getElementById("vitalsTable").tBodies[0];
  var newRow = tblBody.insertRow(-1);

  var newCell0 = newRow.insertCell(0);
  newCell0.innerHTML = '<input type="text" id="Vitals_' + num +  '_time" name="Vitals_' + num +  '_time" value="" size="5" maxlength="5" />';
  var newCell1 = newRow.insertCell(1);
  newCell1.innerHTML = '<input type="text" id="Vitals_' + num +  '_bp" name="Vitals_' + num +  '_bp" value="" size="7" maxlength="9" />';
  var newCell2 = newRow.insertCell(2);
  newCell2.innerHTML = '<input type="text" id="Vitals_' + num +  '_pulse" name="Vitals_' + num +  '_pulse" value="" size="3" maxlength="6" />';
  var newCell3 = newRow.insertCell(3);
  newCell3.innerHTML = '<input type="text" id="Vitals_' + num +  '_resp" name="Vitals_' + num +  '_resp" value="" size="10" maxlength="10" />';
  var newCell4 = newRow.insertCell(4);
  newCell4.innerHTML = '<input type="text" id="Vitals_' + num +  '_lungSounds" name="Vitals_' + num +  '_lungSounds" value="" size="10" maxlength="10" />';
  var newCell5 = newRow.insertCell(5);
  newCell5.innerHTML = '<select name="Vitals_' + num +  '_consciousness" id="Vitals_' + num +  '_consciousness" ><option value="Alert" >Alert</option><option value="Verbal" >Verbal</option><option value="Painful" >Painful</option><option value="Unresponsive" >Unresponsive</option></select>';
  var newCell6 = newRow.insertCell(6);
  newCell6.innerHTML = '<select name="Vitals_' + num +  '_pupilL" id="Vitals_' + num +  '_pupilL" onChange="update_pupils(' + num + ', 0)" ><option value="NONE" >Left</option><option value="Equal" >Equal</option><option value="Dilated" >Dilated</option><option value="Constricted" >Constricted</option><option value="Unresponsive" >Unresponsive</option><option value="Responsive/Unequal" >Responsive/Unequal</option></select><select name="Vitals_' + num +  '_pupilR" id="Vitals_' + num +  '_pupilR" onChange="update_pupils(' + num + ', 1)" ><option value="NONE" >Right</option><option value="Equal" >Equal</option><option value="Dilated" >Dilated</option><option value="Constricted" >Constricted</option><option value="Unresponsive" >Unresponsive</option><option value="Responsive/Unequal" >Responsive/Unequal</option></select>';
  var newCell7 = newRow.insertCell(7);
  newCell7.innerHTML = '<select name="Vitals_' + num +  '_skinMoisture" id="Vitals_' + num +  '_skinMoisture" ><option value="" selected="selected" ></option><option value="Dry" >Dry</option><option value="Moist" >Moist</option></select> <select name="Vitals_' + num +  '_skinTemp" id="Vitals_' + num +  '_skinTemp" ><option value="" selected="selected" ></option><option value="Warm" >Warm</option><option value="Cool" >Cool</option></select> <select name="Vitals_' + num +  '_skinColor" id="Vitals_' + num +  '_skinColor" ><option value="" selected="selected" ></option><option value="Red" >Red</option><option value="Pale" >Pale</option><option value="Blue" >Blue</option><option value="Pink" >Pink</option><option value="Jaundice" >Jaundice</option></select>';
  var newCell8 = newRow.insertCell(8);
  newCell8.innerHTML = '<input type="text" id="Vitals_' + num +  '_spo2" name="Vitals_' + num +  '_spo2" value="" size="3" maxlength="3" />&#37;';
  
  document.getElementById("nextVitalsRow").value = num + 1;
}

function update_pupils(num, side)
{
  // side - which one is being updated - 0 for left, 1 for right
  if(side == 0 && document.getElementById("Vitals_" + num + "_pupilL").value == "Equal")
  {
    document.getElementById("Vitals_" + num + "_pupilR").value = "Equal";
  }
  else if(side == 1 && document.getElementById("Vitals_" + num + "_pupilR").value == "Equal")
  {
    document.getElementById("Vitals_" + num + "_pupilL").value = "Equal";
  }
}

//
// HTTPrequest stuff
//

function doHTTPrequest($url, $handler)
{
  // TODO - get this working with older Firefox, using abort()
  http.open('get', $url);
  http.onreadystatechange = $handler;
  http.send(null);
}

//
// POPUP STUFF
//

function showPopup(title)
{
  title = typeof(title) != 'undefined' ? title : "";
  centerPopup();
  grayOut(true);
  document.getElementById("popup").style.display = 'block';
  document.getElementById('popuptitle').innerHTML = title;
}

function hidePopup()
{
  grayOut(false);
  document.getElementById("popup").style.display = 'none';
}

function centerPopup()
{
  // center the top of the popup in the page vertically, no matter where we have scrolled to
  var scrolledY;
  if( self.pageYOffset ) {
    scrolledY = self.pageYOffset;
  } 
  else if( document.documentElement && document.documentElement.scrollTop )
  {  
    scrolledY = document.documentElement.scrollTop;
  } 
  else if( document.body )
  {  
    scrolledY = document.body.scrollTop;
  }
  
  // Determine the coordinates of the center of the page

  var centerY;
  if( self.innerHeight )
  {  
    centerY = self.innerHeight;
  }
  else if( document.documentElement && document.documentElement.clientHeight )
  {  
    centerY = document.documentElement.clientHeight;
  }
  else if( document.body )
  {  
    centerY = document.body.clientHeight;
  }

  var topOffset = (scrolledY + (centerY - 200) / 2) - 100;

  document.getElementById("popup").style.top = topOffset + "px";
}



function popUp(URL) 
{
  day = new Date();
  id = day.getTime();
  eval("page" + id + " = window.open(URL, '" + id + "','toolbar=0,scrollbars=1,location=0,statusbar=1,menubar=0,resizable=0,width=500,height=500');");
}

function resetForm()
{
  if (confirm("Are you sure you want to clear form? This will clear ALL data."))
  {
    document.PCR.reset();
  }
}