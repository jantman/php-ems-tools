<?php
// Time-stamp: "2009-11-07 12:19:49 jantman"
// +----------------------------------------------------------------------+
// | MPAC/PHP EMS Tools Finance Component - http://www.php-ems-tools.com  |
// +----------------------------------------------------------------------+
// | Copyright (c) 2009 Jason Antman.                                     |
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
// | $LastChangedRevision:: 3                                           $ |
// | $HeadURL:: http://svn.jasonantman.com/mpac-finance/config/smarty.p#$ |
// +----------------------------------------------------------------------+


require_once('/usr/share/php5/Smarty/Smarty.class.php');
$smarty = new Smarty();
$smarty->template_dir = '/srv/www/htdocs/admin/smarty/templates';
$smarty->compile_dir = '/srv/www/htdocs/admin/smarty/templates_c';
$smarty->cache_dir = '/srv/www/htdocs/admin/smarty/cache';
$smarty->config_dir = '/srv/www/htdocs/admin/smarty/configs';
$smarty->register_modifier('moneycolor', 'smarty_money_format_color');

// for PHP EMS Tools variables
getPHPemsVars();

$SMARTY_config = array();
$SMARTY_config['date'] = "%Y-%m-%d";
$SMARTY_config['datetime'] = "%Y-%m-%d %H:%M:%S";
$SMARTY_config['time'] = "%H:%M:%S";
$smarty->assign('config', $SMARTY_config);


function getPHPemsVars()
{
    global $smarty;
    require_once('/srv/www/htdocs/config/config.php');
    global $orgName, $shortName;
    $smarty->assign('shortName', $shortName);
    $smarty->assign('orgName', $orgName);
}

function smarty_money_format_color($d)
{
    if($d >= 0)
    {
	return '$'.money_format('%n', $d);
    }
    else
    {
	return '<span style="color: red;">$'.money_format('%n', $d).'</span>';
    }
}

?>