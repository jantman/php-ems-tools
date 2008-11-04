//
// inc/scheduleDHTML.js
//
// JavaScript Functions for DHTML/Ajax functionality
//
// Time-stamp: "2008-11-04 11:05:10 jantman"
// +----------------------------------------------------------------------+
// | PHP EMS Tools      http://www.php-ems-tools.com                      |
// +----------------------------------------------------------------------+
// | Copyright (c) 2006, 2007 Jason Antman.                               |
// |                                                                      |
// | This program is free software; you can redistribute it and/or modify |
// | it under the terms of the GNU General Public License as published by |
// | the Free Software Foundation; either version 3 of the License, or    |
// | (at your option) any later version.                                  |
// |                                                                      |
// | This program is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the        |
// | GNU General Public License for more details.                         |
// |                                                                      |
// | You should have received a copy of the GNU General Public License    |
// | along with this program; if not, write to:                           |
// |                                                                      |
// | Free Software Foundation, Inc.                                       |
// | 59 Temple Place - Suite 330                                          |
// | Boston, MA 02111-1307, USA.                                          |
// +----------------------------------------------------------------------+
// |Please use the above URL for bug reports and feature/support requests.|
// +----------------------------------------------------------------------+
// | Authors: Jason Antman <jason@jasonantman.com>                        |
// +----------------------------------------------------------------------+
//      $Id$

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

//
// SCHEDULE-SPECIFIC FUNCTIONS
//

function showSignonForm($ts, $shift)
{
  // shows the form to add a new signon

  // DEBUG
  var ffxVer = getFirefoxVersion();
  if(ffxVer > 0 && ffxVer < 3)
  {
    alert("These functions will throw an unhandled JS exception in Firefox " + ffxVer);
  }
  // END DEBUG

  var myDate = new Date($ts*1000);
  document.getElementById("popuptitle").innerHTML = "Sign On - " + myDate.toLocaleFormat("%a, %b %e %Y") + " " + $shift;
  
  newSignonFormRequest($ts, $shift);
}

function showMessageForm($ts, $shift)
{
  // shows the form to edit a daily message

  var myDate = new Date($ts*1000);
  document.getElementById("popuptitle").innerHTML = "Edit Daily Message - " + myDate.toLocaleFormat("%a, %b %e %Y") + " " + $shift;
  
  messageFormRequest($ts, $shift);
}

function showEditForm($year, $month, $shift, $date, $key, $ts)
{
  // shows the form to edit or remove a signon

  var myDate = new Date($ts*1000);
  document.getElementById("popuptitle").innerHTML = "Edit Sign On - " + myDate.toLocaleFormat("%a, %b %e %Y") + " " + $shift;
  
  editSignonFormRequest($year, $month, $shift, $date, $key, $ts);
}


//
// HTTPrequest senders and handlers
//

// TODO: are ts AND monthTS needed here? where is monthTS needed?
// TODO: only pass the minimum variables needed
// TODO: in PHP, have a central tsToYearMonthDateShift() that returns a string
// TODO: move all of these forms to inc/ named like form{type}.php

function newSignonFormRequest($ts, $shift)
{
  doHTTPrequest(('signOn.php?ts=' + $ts + '&shift=' + $shift), handleNewSignonFormRequest);
	// TODO: add an error var to reload the form if we have errors
}

function handleNewSignonFormRequest()
{
  if(http.readyState == 4)
  {
    var response = http.responseText;
    document.getElementById('popupbody').innerHTML = response;
    showPopup();
  }
}

function editSignonFormRequest($year, $month, $shift, $date, $key, $ts)
{
  doHTTPrequest(('signOn.php?action=edit&year=' + $year + '&month=' + $month + '&shift=' + $shift + '&date=' + $date + '&key=' + $key + '&ts=' + $ts), handleEditSignonFormRequest);
  // TODO: add an error var to reload the form if we have errors
}

function handleEditSignonFormRequest()
{
  if(http.readyState == 4)
  {
    var response = http.responseText;
    document.getElementById('popupbody').innerHTML = response;
    showPopup();
  }
}

function messageFormRequest($ts, $shift)
{
  // request the HTML for the message form
  doHTTPrequest(('dailyMessage.php?ts=' + $ts + '&shift=' + $shift), handleMessageFormRequest);
  // TODO: add an error var to reload the form if we have errors
}

function handleMessageFormRequest()
{
  if(http.readyState == 4)
  {
    var response = http.responseText;
    document.getElementById('popupbody').innerHTML = response;
    showPopup();
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

function showPopup()
{
        grayOut(true);
        document.getElementById("popup").style.display = 'block';
}

function hidePopup()
{
        grayOut(false);
        document.getElementById("popup").style.display = 'none';
}

// UTIITY FUNCTIONS

// DEVELOPMENT ONLY???
function getFirefoxVersion()
{
  var userAgentStr = navigator.userAgent;
  // returns the version number for firefox (as an integer)
  var startIndex = userAgentStr.indexOf("Firefox/");
  if(startIndex < 0)
  {
    // not Firefox
    return 0;    
  }
  
  var version = userAgentStr.substring(startIndex); // start from / next
  version = userAgentStr.substring(userAgentStr.lastIndexOf("/")+1);
  version = parseFloat(version);
  
  return version;
  
}