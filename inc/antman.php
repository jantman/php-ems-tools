<?php
// inc/antman.php
//
// A few simple functions commonly used by the developer.
//
// +----------------------------------------------------------------------+
// | PHP EMS Tools	http://www.php-ems-tools.com                      |
// +----------------------------------------------------------------------+
// | Copyright (c) 2006, 2007 Jason Antman.	                          |
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
// | $HeadURL:: http://svn.jasonantman.com/php-ems-tools/inc/antman.php $ |
// +----------------------------------------------------------------------+


function textMonth($number)
{
	//returns the string month name for the given month number 
	switch ($number)  
	{
	case 1:		
		return "January";
	   break;
	case 2:
	   return "February";
	   break;
	case 3:
	   return "March";
	   break;
	case 4:
	   return "April";
	   break;
	case 5:
	   return "May";
	   break;
	case 6:
	   return "June";
	   break;
	case 7:
	   return "July";
	   break;
	case 8:
	   return "August";
	   break;
	case 9:
	   return "September";
	   break;
	case 10:
	   return "October";
	   break;
	case 11:
	   return "November";
	   break; 
	case 12:
	   return "December";
	   break;
	}

}



?>