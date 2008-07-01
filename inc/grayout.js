//
// inc/grayout.js
//
// JavaScript Functions for graying out the page (to put a popup over it)
//
// Time-stamp: "2008-07-01 16:42:25 jantman"
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

function grayOut(vis)
{
  // based on the script from http://www.hunlock.com/blogs/Snippets:_Howto_Grey-Out_The_Screen
  // Pass true to gray out screen, false to ungray
  // options are optional.  This is a JSON object with the following (optional) properties
  // opacity:0-100         // Lower number = less grayout higher = more of a blackout 
  // zindex: #             // HTML elements with a higher zindex appear on top of the gray out
  // bgcolor: (#xxxxxx)    // Standard RGB Hex color code
  // grayOut(true, {'zindex':'50', 'bgcolor':'#0000FF', 'opacity':'70'});
  // Because options is JSON opacity/zindex/bgcolor are all optional and can appear
  // in any order.  Pass only the properties you need to set.
  var options = options || {}; 
  var zindex = options.zindex || 50;
  var opacity = options.opacity || 70;
  var opaque = (opacity / 100);
  var bgcolor = options.bgcolor || '#000000';
  var dark=document.getElementById('darkenScreenObject');
  if (!dark)
  {
    // The dark layer doesn't exist, it's never been created.  So we'll
    // create it here and apply some basic styles.
    // If you are getting errors in IE see:  http://support.microsoft.com/default.aspx/kb/927917
    var tbody = document.getElementsByTagName("body")[0];
    var tnode = document.createElement('div');           // Create the layer.
    tnode.style.position='absolute';                 // Position  absolutely
    tnode.style.top='0px';                           // In the top
    tnode.style.left='0px';                          // Left corner of the   page
    tnode.style.overflow='hidden';                   // Try to avoid  making scroll bars            
    tnode.style.display='none';                      // Start out Hidden
    tnode.id='darkenScreenObject';                   // Name it so we can  find it later
    tbody.appendChild(tnode);                            // Add it to the web  page
    dark=document.getElementById('darkenScreenObject');  // Get the object.
  }
  if (vis)
  {
    // so far, this has only been tested with FireFox 3 on Linux. 
    var pageWidth = document.documentElement.clientWidth+'px';
    if(document.documentElement.clientHeight > document.body.scrollHeight)
    {
      // our page doesn't fill the whole window
      var pageHeight = document.documentElement.clientHeight+'px';
    }
    else
    {
      var pageHeight = document.body.scrollHeight+'px';
    }

    //set the shader to cover the entire page and make it visible.
    dark.style.opacity=opaque;                      
    dark.style.MozOpacity=opaque;                   
    dark.style.filter='alpha(opacity='+opacity+')'; 
    dark.style.zIndex=zindex;        
    dark.style.backgroundColor=bgcolor;  
    dark.style.width= pageWidth;
    dark.style.height= pageHeight;
    dark.style.display='block';                          
  }
  else
  {
     dark.style.display='none';
  }
}