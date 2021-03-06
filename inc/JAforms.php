<?php
// script to handle form generation, population, verification

/*
 +--------------------------------------------------------------------------------------------------------+
 | PHP EMS Tools      http://www.php-ems-tools.com                                                        |
 +--------------------------------------------------------------------------------------------------------+
 | Time-stamp: "2011-02-08 10:46:46 jantman"                                                              |
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
 | $LastChangedRevision:: 75                                                                            $ |
 | $HeadURL:: http://svn.jasonantman.com/newcall/inc/JAforms.php                                        $ |
 +--------------------------------------------------------------------------------------------------------+
*/

/**
 * JasonAntman form generation functions.
 *
 * @todo are these all used in place of the SimpleForm_ stuff? -jantman 2010-08-23
 * @package MPAC-NewCall
 */

function ja_text($name, $arr, $values)
{
    global $errors, $warnings;
    $foo = '<input type="text" ';
    $foo .= 'id="'.$name.'" name="'.$name.'" ';

    if(isset($errors[$name]))
    {
	$foo .= 'style="border: 2px solid red; background: #F5A9A9;" ';
    }
    elseif(isset($warnings[$name]))
    {
	$foo .= 'style="border: 2px solid #D7DF01; background: #F3F781;" ';
    }

    if($values == null || ! isset($values[$name]))
    {
	if(isset($arr['value'])){ $foo .= 'value="'.$arr['value'].'" ';}
    }
    if(isset($values[$name])){ $foo .= 'value="'.$values[$name].'" ';}
    if(is_array($arr))
    {
	foreach($arr as $key => $val)
	{
	    if($key == "value"){ continue;}
	    if(substr($key, 0, 1) == "_"){ continue;}
	    $foo .= $key.'="'.$val.'" ';
	}
    }
    $foo .= '/>';
    return $foo;
}

function ja_hidden($name, $arr, $values)
{
    $foo = '<input type="hidden" ';
    $foo .= 'id="'.$name.'" name="'.$name.'" ';

    if($values == null || ! isset($values[$name]))
    {
	if(isset($arr['value'])){ $foo .= 'value="'.$arr['value'].'" ';}
    }
    if(isset($values[$name])){ $foo .= 'value="'.$values[$name].'" ';}
    foreach($arr as $key => $val)
    {
	if($key == "value"){ continue;}
	if(substr($key, 0, 1) == "_"){ continue;}
	$foo .= $key.'="'.$val.'" ';
    }
    $foo .= '/>';
    return $foo;
}

function ja_select($name, $arr, $options, $values)
{
    global $errors, $warnings;
    $foo = "";
    $foo .= '<select name="'.$name.'" id="'.$name.'" ';

    if(isset($errors[$name]))
    {
	$foo .= 'style="border: 2px solid red; background: #F5A9A9;" ';
    }
    elseif(isset($warnings[$name]))
    {
	$foo .= 'style="border: 2px solid #D7DF01; background: #F3F781;" ';
    }

    foreach($arr as $key => $val)
    {
	if($key == "value"){ continue;}
	if(substr($key, 0, 1) == "_"){ continue;}
	$foo .= $key.'="'.$val.'" ';
    }
    $foo .= '>';
    $haveValue = false;
    foreach($options as $key => $val)
    {
	if(substr($key, 0, 1) == "_"){ continue;}

	$foo .= '<option value="'.$key.'" ';
	if(isset($values[$name]) && $values[$name] == $key && ! $haveValue)
	{
	    $foo .= 'selected="selected" ';
	    $haveValue = true;
	}
	elseif(isset($arr['value']) && $arr['value'] == $key && ! $haveValue)
	{
	    $foo .= 'selected="selected" ';
	}
	$foo .= '>'.$val.'</option>';
    }
    $foo .= '</select>'."\n";
    return $foo; 
}

function ja_check($name, $arr, $values)
{
    global $errors, $warnings;
    $foo = "";

    if(isset($errors[$name]))
    {
	$foo .= '<span style="border: 2px solid red; background: #F5A9A9; padding: 2px;">';
    }
    elseif(isset($warnings[$name]))
    {
	$foo .= '<span style="border: 2px solid #D7DF01; background: #F3F781; padding: 2px;">';
    }

    $foo .= '<input type="checkbox" ';
    $foo .= 'id="'.$name.'" name="'.$name.'" ';

    foreach($arr as $key => $val)
    {
	if($key == "value"){ continue;}
	if(substr($key, 0, 1) == "_"){ continue;}
	$foo .= $key.'="'.$val.'" ';
    }

    if(isset($values[$name]))
    {
	$foo .= 'checked="checked" ';
    }

    $foo .= '/>';

    if(isset($errors[$name]) || isset($warnings[$name]))
    {
	$foo .= '</span>';
    }

    return $foo;
}

function ja_radio($name, $id, $value, $defaults, $arr = null)
{
    global $errors, $warnings, $vals;
    $foo = "";

    if(isset($errors[$name]) || isset($errors[$id]))
    {
	$foo .= '<span style="border: 2px solid red; background: #F5A9A9; padding: 2px;">';
    }
    elseif(isset($warnings[$name]) || isset($warnings[$id]))
    {
	$foo .= '<span style="border: 2px solid #D7DF01; background: #F3F781; padding: 2px;">';
    }

    $foo .= '<input type="radio" ';
    $foo .= 'id="'.$id.'" name="'.$name.'" value="'.$value.'" ';

    if(is_array($arr))
    {
	foreach($arr as $key => $val)
	{
	    if($key == "value"){ continue;}
	    if(substr($key, 0, 1) == "_"){ continue;}
	    $foo .= $key.'="'.$val.'" ';
	}
    }

    if(isset($defaults[$name]) && $defaults[$name] == $value)
    {
	$foo .= 'checked="checked" ';
    }

    $foo .= '/>';

    if(isset($errors[$name]) || isset($errors[$id]) || isset($warnings[$name]) || isset($warnings[$id]))
    {
	$foo .= '</span>';
    }

    return $foo;
}

function ja_textarea($name, $arr, $values)
{
    global $errors, $warnings;
    $foo = '<textarea ';
    $foo .= 'id="'.$name.'" name="'.$name.'" ';

    if(isset($errors[$name]))
    {
	$foo .= 'style="border: 2px solid red; background: #F5A9A9;" ';
    }
    elseif(isset($warnings[$name]))
    {
	$foo .= 'style="border: 2px solid #D7DF01; background: #F3F781;" ';
    }

    foreach($arr as $key => $val)
    {
	if($key == "value"){ continue;}
	if(substr($key, 0, 1) == "_"){ continue;}
	$foo .= $key.'="'.$val.'" ';
    }
    $foo .= '>';

    if($values != null && isset($values[$name]))
    {
	$foo .= $values[$name];
    }

    $foo .= '</textarea>';
    return $foo;
}

?>