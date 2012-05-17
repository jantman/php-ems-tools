/*
 +--------------------------------------------------------------------------------------------------------+
 | PHP EMS Tools      http://www.php-ems-tools.com                                                        |
 +--------------------------------------------------------------------------------------------------------+
 | Time-stamp: "2009-12-21 11:21:52 jantman"                                                              |
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
 | $LastChangedRevision:: 12                                                                            $ |
 | $HeadURL:: http://svn.jasonantman.com/newcall/js/PCRlayout.js                                        $ |
 +--------------------------------------------------------------------------------------------------------+
*/

function load()
{
  doLayout();
}

function doLayout()
{
  var width = document.getElementById("Medications_td").offsetWidth;
  console.debug("td width: " + width);
  var width1 = document.getElementById("Medications_Label").offsetWidth;
  console.debug("label width: " + width1);
  console.debug("Clinical Table Width: " + document.getElementById("Table_Clinical").offsetWidth);
  
  console.debug("PatientHistory_td offsetWidth: " + document.getElementById("PatientHistory_td").offsetWidth);
  console.debug("PatientHistory_td style.width: " + document.getElementById("PatientHistory_td").style.width);
  console.debug("TD element: " + document.getElementById("PatientHistory_td"));
  console.debug("PatientHistory_td width: " + document.getElementById("PatientHistory_td").width);
  console.debug("PatientHistory_td clientWidth: " + document.getElementById("PatientHistory_td").clientWidth);
  console.debug("PatientHistory_td scrollWidth: " + document.getElementById("PatientHistory_td").scrollWidth);
  console.debug("PatientHistory_td innerWidth: " + document.getElementById("PatientHistory_td").innerWidth);

  console.debug("Table_Clinical offsetWidth: " + document.getElementById("Table_Clinical").offsetWidth);
  console.debug("Table_Clinical style.width: " + document.getElementById("Table_Clinical").style.width);
  console.debug("TD element: " + document.getElementById("Table_Clinical"));
  console.debug("Table_Clinical width: " + document.getElementById("Table_Clinical").width);
  console.debug("Table_Clinical clientWidth: " + document.getElementById("Table_Clinical").clientWidth);
  console.debug("Table_Clinical scrollWidth: " + document.getElementById("Table_Clinical").scrollWidth);
  
  console.debug("Clinical_td offsetWidth: " + document.getElementById("Clinical_td").offsetWidth);
  console.debug("Clinical_td style.width: " + document.getElementById("Clinical_td").style.width);
  console.debug("TD element: " + document.getElementById("Clinical_td"));
  console.debug("Clinical_td width: " + document.getElementById("Clinical_td").width);
  console.debug("Clinical_td clientWidth: " + document.getElementById("Clinical_td").clientWidth);
  console.debug("Clinical_td scrollWidth: " + document.getElementById("Clinical_td").scrollWidth);

  console.debug("ClinicalTitle_td offsetWidth: " + document.getElementById("ClinicalTitle_td").offsetWidth);
  console.debug("ClinicalTitle_td style.width: " + document.getElementById("ClinicalTitle_td").style.width);
  console.debug("TD element: " + document.getElementById("ClinicalTitle_td"));
  console.debug("ClinicalTitle_td width: " + document.getElementById("ClinicalTitle_td").width);
  console.debug("ClinicalTitle_td clientWidth: " + document.getElementById("ClinicalTitle_td").clientWidth);
  console.debug("ClinicalTitle_td scrollWidth: " + document.getElementById("ClinicalTitle_td").scrollWidth);
  
  console.debug("Medications_td offsetWidth: " + document.getElementById("Medications_td").offsetWidth);
  console.debug("Medications_td style.width: " + document.getElementById("Medications_td").style.width);
  console.debug("TD element: " + document.getElementById("Medications_td"));
  console.debug("Medications_td width: " + document.getElementById("Medications_td").width);
  console.debug("Medications_td clientWidth: " + document.getElementById("Medications_td").clientWidth);
  console.debug("Medications_td scrollWidth: " + document.getElementById("Medications_td").scrollWidth);
  
  console.debug("Medications style.width: " + document.getElementById("Medications").style.width);
  document.getElementById("Medications").style.width = ((width - width1)) + "px";
  console.debug("Medications style.width: " + document.getElementById("Medications").style.width);
  document.getElementById("Medications").style.width = "900px";
  console.debug("Medications style.width: " + document.getElementById("Medications").style.width);
  console.debug("Medications offsetWidth: " + document.getElementById("Medications").offsetWidth);
  console.debug("Medications clientWidth: " + document.getElementById("Medications").clientWidth);
  console.debug("Medications scrollWidth: " + document.getElementById("Medications").scrollWidth);
  console.debug("Medications size: " + document.getElementById("Medications").size);
}