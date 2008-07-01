//
// inc/scheduleDHTML.js
//
// JavaScript Functions for DHTML/Ajax functionality
//
// Time-stamp: "2008-07-01 16:48:25 jantman"
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

function showSignonForm($ts, $monthTS)
{
  // shows the form to add a new signon
  alert("showSignonForm ts="+$ts+" monthTS="+$monthTS);
  newSignonFormRequest($ts, $monthTS);
}

function showMessageForm($ts, $monthTS)
{
  // shows the form to edit a daily message
  alert("showMessageForm ts="+$ts+" monthTS="+$monthTS);
}

function showEditForm($year, $month, $shift, $date, $key)
{
  // shows the form to edit or remove a signon
  alert("showEditForm year="+$year+" month="+$month+" shift="+$shift+" date="+$date+" key="+$key);
}


//
// HTTPrequest senders and handlers
//

function newSignonFormRequest($ts, $monthTS)
{
	http.open('get', 'signOn.php?ts=' + $ts);
	// TODO: add an error var to reload the form if we have errors
	http.onreadystatechange =  handleNewSignonFormRequest; 
	http.send(null);
}

function handleNewSignonFormRequest()
{
	if(http.readyState == 4)
	{
	  var response = http.responseText;
	  document.getElementById('popupbody').innerHTML = response;
	  showPopup("popup");
	}
}

//
// POPUP STUFF
//

function showPopup(p)
{
        grayOut(true);
        document.getElementById(p).style.display = 'block';
}

function hidePopup(p)
{
        grayOut(false);
        document.getElementById(p).style.display = 'none';
}