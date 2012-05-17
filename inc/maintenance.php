<?php
//
// inc/maintenance.php
//
// File for maintenance warning on pages
//  if $maint_warn_html is not "", it will be put in a div at the top of all pages
//
// Time-stamp: "2009-11-20 12:08:24 jantman"
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
//      $Id: sched.php,v 1.9 2008/11/23 06:12:44 jantman Exp $

// TODO: implement this on all logical pages

$maint_warn_html = "";

if(1 == 0)
{
    $maint_warn_html = "<h2>Downtime Monday October 18, 2009</h2><p>Cablevision will be working on the MPAC Internet connection Monday, October 18, 2009 between 0800 and 1200.<br />All Internet-based systems will be unavailable outside of the building during this time.<br />Please call the desk if you are responding to a call.</p>";
}

//$maint_warn_html = "<h2>Testing Currently in Progress...</h2>";


?>