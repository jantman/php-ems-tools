/*
 +--------------------------------------------------------------------------------------------------------+
 | PHP EMS Tools      http://www.php-ems-tools.com                                                        |
 +--------------------------------------------------------------------------------------------------------+
 | Time-stamp: "2010-01-07 21:26:00 jantman"                                                              |
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
 | $LastChangedRevision:: 46                                                                            $ |
 | $HeadURL:: http://svn.jasonantman.com/newcall/js/crew.js                                             $ |
 +--------------------------------------------------------------------------------------------------------+
*/


//
// GET DUTY CREW
//

function getDutyCrew()
{
  // populate form with duty crew
  var d = document.getElementById('Date').value;
  var t = document.getElementById('time_disp').value;
  if(t == "" || d == "")
  {
    alert("You cannot populate the duty crew until you have entered a date and dispatch time");
    return;
  }
  newGetDutyCrewRequest('getCrew.php?type=duty&date='+escape(d)+'&time='+escape(t));
}

function newGetDutyCrewRequest(url)
{
  doHTTPrequest(url, handleNewGetDutyCrewRequest);
}

function handleNewGetDutyCrewRequest()
{
  if(http.readyState == 4)
  {
    var response = http.responseText;
    // should be either empty or CSV list of EMTids
    if(response == "")
    {
      // nobody on duty
      alert("No members found on duty.");
    }
    else
    {
      // have a duty crew
    
      // else we have more than one member
      if(document.getElementById("crew_id_0").value != "")
      {
	// TODO - better way to do this.
	alert("You have already entered some crew members. They will be overwritten with the duty crew.");
      }

      if(response.indexOf(',') == -1)
      {
	// have only one member
	document.getElementById("crew_id_0").value = response;
	document.getElementById('crew_genDuty_0').value = "Duty";
	return;
      }
      
      var foo = response.split(',');
      var len = foo.length; // length of returned array

      // make sure we have enough crew rows
      while(parseInt(document.getElementById("nextCrewRow").value) < len)
      {
	addCrew();
      }
      
      // loop through the EMTids and add them
      for ( var i=0; i<len; ++i )
      {
	document.getElementById("crew_id_"+i).value = foo[i];
	document.getElementById('crew_genDuty_' + i).value = "Duty";
      }
      
    }
  }
}

// TODO - do we need this?
function foobarbaz()
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
	document.getElementById("ptpkey").value = JSONobject.pkey;
      }
    else
      {
	document.getElementById("ptpkey").value = "-1";
      }
    
    if(JSONobject && JSONobject.DOB)
      {
	document.getElementById("DOB").value = JSONobject.DOB;
      }
    else
      {
	document.getElementById("DOB").value = "";
      }
    
    if(JSONobject && JSONobject.Age)
      {
	document.getElementById("age").value = JSONobject.Age;
      }
    else
      {
	document.getElementById("age").value = "";
      }

    if(JSONobject && JSONobject.FirstName)
      {
	document.getElementById("NameFirst").value = JSONobject.FirstName;
      }
    else
      {
	document.getElementById("NameFirst").value = "";
      }
    
    if(JSONobject && JSONobject.LastName)
      {
	document.getElementById("NameLast").value = JSONobject.LastName;
      }
    else
      {
	document.getElementById("NameLast").value = "";
      }
    
    if(JSONobject && JSONobject.MiddleName)
      {
	document.getElementById("NameMiddle").value = JSONobject.MiddleName;
      }
    else
      {
	document.getElementById("NameMiddle").value = "";
      }
    
    if(JSONobject && JSONobject.DisplayAddress)
      {
	document.getElementById("Address").value = JSONobject.DisplayAddress;
      }
    else
      {
	document.getElementById("Address").value = "";
      }
    
    /*
    if(JSONobject && JSONobject.Town)
      {
	// TODO - select
	document.getElementById("AddressCity").value = JSONobject.Town;
      }
    */
    
    if(JSONobject && JSONobject.Sex)
      {
	// male or female
	if(JSONobject.Sex == "Female")
	  {
	    document.getElementById("sex").value = "Female";
	  }
	else if(JSONobject.Sex == "Male")
	{
	  document.getElementById("sex").value = "Male";
	}
      }
    
    hidePopup();
  }
}