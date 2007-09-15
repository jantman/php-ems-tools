// JavaScript include file for PHP EMS Tools
//
// php-ems-tools.js
//
// Master javascript file for PHP EMS Tools. Mainly just popups.
//
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

function signOn(int year, int month, int date, String shift, int slot)
{
    URL = "signOn.php?year="+year+"&month="+month+"&date="+date+"&shift="+shift+"&slot="+slot;
    eval("page" + id + " = window.open(URL, '" + id + "','toolbar=0,scrollbars=0,location=0,statusbar=1,menubar=0,resizable=1,width=450,height=400,left = 312,top = 84');");
}
function dailyMessage(int year, int month, int date, String shift)
{
    URL = "signOn.php?year="+year+"&month="+month+"&date="+date+"&shift="+shift;
    eval("page" + id + " = window.open(URL, '" + id + "','toolbar=0,scrollbars=0,location=0,statusbar=1,menubar=0,resizable=1,width=450,height=400,left = 312,top = 84');");
}
*/

function popUp(URL) 
{
day = new Date();
id = day.getTime();
eval("page" + id + " = window.open(URL, '" + id + "','toolbar=0,scrollbars=0,location=0,statusbar=1,menubar=0,resizable=0,width=400,height=400');");
}

function rosterPopUp(URL) 
{
day = new Date();
id = day.getTime();
eval("page" + id + " = window.open(URL, '" + id + "','toolbar=0,scrollbars=1,location=0,statusbar=1,menubar=0,resizable=0,width=430,height=600');");
}

function helpPopUp(URL) 
{
day = new Date();
id = day.getTime();
eval("page" + id + " = window.open(URL, '" + id + "','toolbar=0,scrollbars=1,location=0,statusbar=1,menubar=0,resizable=1,width=500,height=600');");
}