/*
 +--------------------------------------------------------------------------------------------------------+
 | PHP EMS Tools      http://www.php-ems-tools.com                                                        |
 +--------------------------------------------------------------------------------------------------------+
 | Time-stamp: "2009-12-31 22:45:14 jantman"                                                              |
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
 | $LastChangedRevision:: 21                                                                            $ |
 | $HeadURL:: http://svn.jasonantman.com/newcall/js/callLoc.js                                          $ |
 +--------------------------------------------------------------------------------------------------------+
*/

function update_state_CL()
{
  var foo = document.getElementById("cl_AddressState").value;
  if(foo == "NJ")
    {
      document.getElementById("cl_city_other_span").style.display = "none";
      document.getElementById("cl_AddressCity").style.display = "inline";
    }
  else
    {
      document.getElementById("cl_city_other_span").style.display = "inline";
      document.getElementById("cl_AddressCity").value = "--Other";
      document.getElementById("cl_AddressCity").style.display = "none";
      document.getElementById("cl_AddressStreetMP").style.display = "none";
      document.getElementById("cl_AddressStreet").style.display = "inline";
      document.getElementById("cl_AddressIntsct1").style.display = "inline";
      document.getElementById("cl_AddressIntsctMP1").style.display = "none";
      document.getElementById("cl_AddressIntsct2").style.display = "inline";
      document.getElementById("cl_AddressIntsctMP2").style.display = "none";
    }
  
}

function updateCLintsct(foo)
{
  if(foo == 0)
  {
    document.getElementById("cl_AddressStreetDiv").style.display = "inline";
    document.getElementById("cl_AddressIntsctDiv").style.display = "none";      
  }
  else
  {
    document.getElementById("cl_AddressStreetDiv").style.display = "none";      
    document.getElementById("cl_AddressIntsctDiv").style.display = "inline";
  }
}

function update_city_CL()
{
  var foo = document.getElementById("cl_AddressCity").value;
  if(foo == "--Other")
    {
      document.getElementById("cl_city_other_span").style.display = "inline";
    }
  else
    {
      document.getElementById("cl_city_other_span").style.display = "none";
    }
  
  if(foo == "Midland Park")
    {
      document.getElementById("cl_AddressStreet").style.display = "none";
      document.getElementById("cl_AddressStreetMP").style.display = "inline";
      document.getElementById("cl_AddressIntsct1").style.display = "none";
      document.getElementById("cl_AddressIntsctMP1").style.display = "inline";
      document.getElementById("cl_AddressIntsct2").style.display = "none";
      document.getElementById("cl_AddressIntsctMP2").style.display = "inline";
    }
  else
    {
      document.getElementById("cl_AddressStreet").style.display = "inline";
      document.getElementById("cl_AddressStreetMP").style.display = "none";
      document.getElementById("cl_AddressIntsct1").style.display = "inline";
      document.getElementById("cl_AddressIntsctMP1").style.display = "none";
      document.getElementById("cl_AddressIntsct2").style.display = "inline";
      document.getElementById("cl_AddressIntsctMP2").style.display = "none";
    }
}

//
// FIND Call Location LINK
//

function findCallLoc()
{
  // shows the form to find a patient

  var foo = document.getElementById("popuptitle");
  foo.innerHTML = "Search for Call Location";
  
  newFindCallLocFormRequest('findCallLoc.php');
}

function newFindCallLocFormRequest(url)
{
  document.getElementById('popupbody').innerHTML = '<span style="text-align: center"><ing src="bigrotation2.gif" /></span>';
  doHTTPrequest(url, handleNewFindCallLocFormRequest);
  // TODO: add an error var to reload the form if we have errors
}

function handleNewFindCallLocFormRequest()
{
  if(http.readyState == 4)
  {
    var response = http.responseText;
    document.getElementById('popupbody').innerHTML = response;
    showPopup("Find Call Location");
  }
}

function submitFindCallLocForm()
{
  var url = "findCallLoc.php?";
  if(document.getElementById("FindCallLoc_PlaceName").value != "")
    {
      url = url + "PlaceName=" + escape(document.getElementById("FindCallLoc_PlaceName").value);
    }
  if(document.getElementById("FindCallLoc_Street").value != "")
    {
      url = url + "Street=" + escape(document.getElementById("FindCallLoc_Street").value);
    }
  newFindCallLocFormRequest(url);
}


// 
// ADD CALL LOCATION
//

function addCallLoc()
{
  var foo = document.getElementById("popuptitle");
  foo.innerHTML = "Add New Call Location";
  newAddCallLocFormRequest('addCallLoc.php');
}

function newAddCallLocFormRequest(url)
{
  document.getElementById('popupbody').innerHTML = '<span style="text-align: center"><ing src="bigrotation2.gif" /></span>';
  doHTTPrequest(url, handleAddCallLocFormRequest);
  // TODO: add an error var to reload the form if we have errors
}

function handleAddCallLocFormRequest()
{
  if(http.readyState == 4)
  {
    var response = http.responseText;
    document.getElementById('popupbody').innerHTML = response;
    showPopup("Add Call Location");
  }
}

function validateCallLocForm()
{
  return 1; // if OK
}

function submitCallLocForm()
{
  // validate the input
  if(validateCallLocForm() != 1)
  {
      return;
  }
  
  var s = "addCallLocHandler.php?";
  for(i=0; i<document.addCallLocForm.elements.length; i++)
  {   
    if(document.addCallLocForm.elements[i].type=="radio" && document.addCallLocForm.elements[i].checked == false)
    {

    }
    else if(document.addCallLocForm.elements[i].type=="radio" && document.addCallLocForm.elements[i].checked == true)
    {
      if(s.charAt(s.length-1)!="?"){ s = s + "&";}
      s = s + escape(document.addCallLocForm.elements[i].name);
      s = s + "=";
      s = s + escape(document.addCallLocForm.elements[i].value);
    }
    else
    {
      if(s.charAt(s.length-1)!="?"){ s = s + "&";}
      s = s + escape(document.addCallLocForm.elements[i].name);
      s = s + "=";
      s = s + escape(document.addCallLocForm.elements[i].value);
    }
  }
  newSubmitCallLocFormRequest(s);
  //hidePopup();
}

function newSubmitCallLocFormRequest(url)
{
  doHTTPrequest(url, handleSubmitCallLocFormRequest);
}

function handleSubmitCallLocFormRequest()
{
  if(http.readyState == 4)
  {
    var response = http.responseText;
    if(response.substr(0, 5) == "ERROR")
    {
      alert(response);
      return;
    }
    
    // else update the patient
    setCallLocByPkey(response);
    hidePopup();
  }
}

function setCallLoc(id)
{
  doHTTPrequest(("getCallLoc.php?id=" + id), handleNewSetCallLocRequest);
}

function setCallLocByPkey(pkey)
{
  doHTTPrequest(("getCallLoc.php?pkey=" + pkey), handleNewSetCallLocRequest);
}

function handleNewSetCallLocRequest()
{
  if(http.readyState == 4)
  {
    var response = http.responseText;
    var JSONobject = JSON.parse(response);
    if(JSONobject && JSONobject.ERROR)
      {
	alert("ERROR: " + JSONobject.ERROR);
	return;
      }
    // if we get here, we have the object and no error
    //JSON: id, DOB, Age, FirstName, LastName, MiddleName, Address, Town, Sex
    if(JSONobject && JSONobject.id)
      {
	document.getElementById("calllocid").value = JSONobject.id;
      }
    else
      {
	document.getElementById("calllocid").value = "-1";
      }
    
    if(JSONobject && JSONobject.DisplayAddress)
      {
	document.getElementById("call_loc_other").value = JSONobject.DisplayAddress;
      }
    else
      {
	document.getElementById("CallLocOther").value = "";
      }
    
    hidePopup();
  }
}
