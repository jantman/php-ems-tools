// <?php
/*
 +--------------------------------------------------------------------------------------------------------+
 | PHP EMS Tools      http://www.php-ems-tools.com                                                        |
 +--------------------------------------------------------------------------------------------------------+
 | Time-stamp: "2010-08-25 21:26:02 jantman"                                                              |
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
 | $HeadURL:: http://svn.jasonantman.com/newcall/js/mileage.js                                          $ |
 +--------------------------------------------------------------------------------------------------------+
*/

/**
 * Functions related to rig mileage.
 *
 * @package MPAC-NewCall-JS
 */

function checkMileage()
{
  // populate form with duty crew
  var m = document.getElementById('mileage').value;
  var u = document.getElementById('unit').value;
  if(u == "" || m == "" || u == "N/A")
  {
    return;
  }
  newCheckMileageRequest('checkMileage.php?unit='+escape(u)+'&mileage='+escape(m));
}

function newCheckMileageRequest(url)
{
  doHTTPrequest(url, handleNewCheckMileageRequest);
}

function handleNewCheckMileageRequest()
{
  if(http.readyState == 4)
  {
    var response = http.responseText;

    if(response == "")
    {
      return;
    }
    else if(response.substring(0, 6) == "TOOLOW")
    {
      alert("The mileage entered is less than the mileage for the last call for that rig. Please either correct the mileage or select the correct rig.");
    }
    else if(response.substring(0, 7) == "TOOHIGH")
    {
      alert("The mileage entered is 30+ miles more than the mileage for the last call for that rig. Please either correct the mileage or select the correct rig.");
    }
    return;
  }
}
