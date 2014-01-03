// <?php
/*
 +--------------------------------------------------------------------------------------------------------+
 | PHP EMS Tools      http://www.php-ems-tools.com                                                        |
 +--------------------------------------------------------------------------------------------------------+
 | Time-stamp: "2010-08-25 21:28:59 jantman"                                                              |
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
 | $LastChangedRevision:: 67                                                                            $ |
 | $HeadURL:: http://svn.jasonantman.com/newcall/js/crew.js                                             $ |
 +--------------------------------------------------------------------------------------------------------+
*/

/**
 * JS Functions to get current duty crew from schedule.
 *
 * @package MPAC-NewCall-JS
 */


/**
 * Construct URL and call newGetDutyCrewRequest
 *
 * Called from link in {@link newcall.php}
 *
 * Checks that Date and time_disp have input, or alerts and returns
 */
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

/**
 * Fire HTTPRequest for get duty crew
 *
 * {@link handleNewGetDutyCrewRequest()} is handler
 * called by {@link getDutyCrew()}
 *
 * @param {String} url URL for request
 */
function newGetDutyCrewRequest(url)
{
  doHTTPrequest(url, handleNewGetDutyCrewRequest);
}

/**
 * Handler for {@link #newGetDutyCrewRequest}, populates form with duty crew
 *
 * 
 */
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
