//
// inc/schedForms.js
//
// JavaScript Functions for schedule forms validation and submission
//
// Time-stamp: "2009-03-11 12:07:51 jantman"
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
// | $LastChangedRevision:: 155                                         $ |
// | $HeadURL:: http://svn.jasonantman.com/php-ems-tools/inc/schedForms#$ |
// +----------------------------------------------------------------------+


function memberCanSignOn(EMTid)
{
  if(memberIDs.indexOf(EMTid) > -1)
  {
    return true;
  }
  return false;
}

function showAdminLogin()
{
  if(document.getElementById("formAdminDiv").style.display == 'block')
  {
    document.getElementById("formAdminDiv").style.display = 'none';
  }
  else
  {
    document.getElementById("formAdminDiv").style.display = 'block';
  }
}

function resetSignonForm()
{
  document.getElementById("formAdminDiv").style.display = 'none';
  clearSignonErrors();
}

//
// SUBMISSION FUNCTIONS
//

function submitSignonForm(ts)
{
  // clear the error fields
  clearSignonErrors();
  
  if(validateSignonForm() == true)
  {
    // show the new info and hide the popup
    var formURL = makeSignonFormUrl(ts);
    submitURL(formURL);
  }
  // else do nothing, just show the errors
}

function submitSignonURL(url)
{
  http.open('get', url);
  http.onreadystatechange = handleSubmitSignonURL; 
  http.send(null);
}

function handleSubmitSignonURL()
{
  if(http.readyState == 4)
  {
    var response = http.responseText;
    if(response.substr(0, 6) == "ERROR:")
    {
      // TODO: handle error condition by triggering a popup or changing content of existing one
      var errorMessage = response.substr(6, (response.length - 6));
      document.getElementById("popuptitle").innerHTML = "ERROR";
      document.getElementById("popup").innerHTML = errorMessage;
    }
    else
    {  
      var ts = document.getElementById('temp_ts').value;
      reloadDay('inc/getDay.php?ts=' + ts + '&monthTS=' + ts);
    }
  }
}

function clearSignonErrors()
{
  document.getElementById("form_EMTid_error").innerHTML = "";
  document.getElementById("form_time_error").innerHTML = "";
}

function reloadDay(url)
{
  http.open('get', url);
  http.onreadystatechange = handleReloadDay; 
  http.send(null);
}

function handleReloadDay()
{
  if(http.readyState == 4)
  {
    
    var response = http.responseText;
    var ts = document.getElementById('temp_ts').value;
    var elemID = 'day_' + ts;
    document.getElementById(elemID).innerHTML = response;
    hidePopup("popup");
  }
}

function validateSignonForm()
{
  var valid = true;
  if(! isValidMember(document.getElementById("form_EMTid").value))
  {
    document.getElementById("form_EMTid_error").innerHTML = "This EMTid is not valid.<br />";
    valid = false;
  }

  var start = document.getElementById("form_start").value;
  var startH = start.substring(0, start.indexOf(":")) * 1;
  var end = document.getElementById("form_end").value;
  var endH = end.substring(0, end.indexOf(":")) * 1;

  if(document.getElementById("form_shift").value == "day")
  {
    if(startH > endH)
    {
      document.getElementById("form_time_error").innerHTML = "The start time must be before the end time <br />.";
      valid = false;
    }
  }
  else
  {
    // this is a night shift
    if(endH < startH && endH > 18 && startH < 18)
    {
      // this is an error, endH is between (18,23) and startH is between (0,6)
      document.getElementById("form_time_error").innerHTML = "The start time must be before the end time.<br /> Perhaps you wanted to sign on a daytime shift? <br />.";
      valid = false;
    }
    else if(endH < startH && endH > 18 && startH > 18)
    {
      // this is an error
      document.getElementById("form_time_error").innerHTML = "The start time must be before the end time.<br /> Perhaps you wanted to sign on a daytime shift? <br />.";
      valid = false;
    }
    else if(endH < startH && endH < 18 && startH < 18)
    {
      // this is an error.
      document.getElementById("form_time_error").innerHTML = "The start time must be before the end time.<br /> Perhaps you wanted to sign on a daytime shift? <br />.";
      valid = false;
    }
    // else OK 
  }

  if(startH == endH)
  {
    document.getElementById("form_time_error").innerHTML = "The start time must be before the end time.<br />.";
    valid = false;      
  }
  
  return valid;
}

function makeSignonFormURL()
{
  
}

function isValidMember(membID)
{
  for (x in memberIDs)
  {
    if( memberIDs[x].toString() == membID.toString() )
    {
      return true;
    }
  }
  return false;
}