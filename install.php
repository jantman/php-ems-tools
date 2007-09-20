<?php
// install.php
//
// Command-Line installation script. Handles all database stuff and
//   checks for dependencies. 
// FOR UNIX OR LINUX ONLY!
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

// this file will import the user's customization
require_once('./config/config.php');

// import ALL of the config files
require_once('./config/rigCheckData.php');
require_once('./config/extendedUserInfo.php');
require_once('./config/rosterConfig.php');
require_once('./config/scheduleConfig.php');

require_once('./admin/checkCustomConfig.php');

$dontMakeDB = false; // whether or not to make the DB.

// clear screen
for($temp = 0; $temp < 101; $temp++)
{
    fwrite(STDOUT, "\n");
}

// make SURE this script is running from CLI
if(php_sapi_name() != 'cli')
{
    die("You are using something other than the command-line interface. Exiting...");
}

fwrite(STDOUT, "PHP EMS Tools \n");
fwrite(STDOUT, "INSTALLATION SCRIPT \n");
fwrite(STDOUT, "------------------------------------------------------------- \n");
fwrite(STDOUT, "This script is for Linux/Unix/BSD systems ONLY! \n");
fwrite(STDOUT, "------------------------------------------------------------- \n\n");

fwrite(STDOUT, "Please be WARNED that PHP EMS Tools is not designed to be used\n");
fwrite(STDOUT, "on the internet. To do so, you must be sure to configure additioanl\n");
fwrite(STDOUT, "security. At the minimum, use .htaccess files!\n");
fwrite(STDOUT, "Until we are done with configuration, this directory (and the MySQL database)\n");
fwrite(STDOUT, "should probably NOT be visible from the Internet.\n");

fwrite(STDOUT, "If nothing is visible to the Internet, or you understand and choose to disregard\n");
fwrite(STDOUT, "this warning, please type yes. Otherwise, type no\n");
fwrite(STDOUT, "yes or no?\n");
$command = trim(fgets(STDIN));
if($command == "yes")
{
    fwrite(STDOUT, "Ok, good. We can continue.\n");
}
else
{
    fwrite(STDOUT, "Security is a major concern, especially with a database program such as this.\n");
    fwrite(STDOUT, "Please think about the possible risks, and develop a solution.\n");
    fwrite(STDOUT, "I'm exiting now...\n");
    die();
}
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");
fwrite(STDOUT, "I am about to display the PHP EMS Tools License....\n");
fwrite(STDOUT, "\n");
fwrite(STDOUT, "\n");
fwrite(STDOUT, "PHP EMS Tools (php-ems-tools) LICENSE\n");
fwrite(STDOUT, "\n");
fwrite(STDOUT, "Please refer to www.php-ems-tools.com for more information.\n");
fwrite(STDOUT, "\n");
fwrite(STDOUT, "This software is distributed under the GNU GPL License, with the following provisions:\n");
fwrite(STDOUT, "\n");
fwrite(STDOUT, "1) If you are intending to use PHP EMS Tools for your EMS organization, please fill out the user survey at http://jantman.dyndns.org:10011/php-ems-tools/survey.php This information is for my research purposes only, as I would like to keep the package up-to-date for the needs of the users.\n");
fwrite(STDOUT, "\n");
fwrite(STDOUT, "2) You are free to modify the code however you choose, provided that you include attribution to the original project (PHP EMS Tools available at http://www.php-ems-tools.com).\n");
fwrite(STDOUT, "\n");
fwrite(STDOUT, "3) If you choose to modify the code, I ask that you e-mail me with your additions, or a brief synopsis of what they do, so that I can incorporate the features into the next release.\n");
fwrite(STDOUT, "\n");
fwrite(STDOUT, "4) If you choose (purely voluntary) I would like to put a link to your organization's web site on the php-ems-tools webpage, under a list of current users.\n");
fwrite(STDOUT, "\n");
fwrite(STDOUT, "5) If you modify and redistribute this code, you must include this license agreement with it.\n");
fwrite(STDOUT, "\n");
fwrite(STDOUT, "6) I REQUEST that you do NOT redistribute this code without notifying me - I would like to try and have all downloads from SourceForge or my web site, in order to keep the distributed code up-to-date.\n");
fwrite(STDOUT, "\n");
fwrite(STDOUT, "7) If you feel that the software has helped your organization, recognize the savings that you have gained by using free software, and would like to help my development efforts, I accept donations to support the costs of running my web sites and development machines. Please contact me at jason@php-ems-tools.com\n");
fwrite(STDOUT, "\n");
fwrite(STDOUT, "8) I highly encourage all users to consider making a donation in return for the software, in any amount, to:\n");
fwrite(STDOUT, "\n");
fwrite(STDOUT, "   Midland Park Volunteer Ambulance Corps\n");
fwrite(STDOUT, "   PO Box 38\n");
fwrite(STDOUT, "   Midland Park, NJ\n");
fwrite(STDOUT, "   07432\n");
fwrite(STDOUT, "   http://www.midlandparkambulance.com\n");
fwrite(STDOUT, "\n");
fwrite(STDOUT, "   Checks Payable To: Midland Park Ambulance Corps\n");
fwrite(STDOUT, "\n");
fwrite(STDOUT, "   This donation is tax deductable (MPAC is a 501(c)(3) Non-Profit).\n");
fwrite(STDOUT, "\n");
fwrite(STDOUT, "\n");

fwrite(STDOUT, "Do you accept this license? yes or no\n");
$command = trim(fgets(STDIN));
if($command == "yes")
{
    fwrite(STDOUT, "Thank You.\n");
}
else
{
    fwrite(STDOUT, "You may NOT use this software until you accept the license terms!\n");
    die();
}

fwrite(STDOUT, "\n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");

fwrite(STDOUT, "NOTICE: \n");
fwrite(STDOUT, "This piece of free, open-source software relies on user feedback for development.\n");
fwrite(STDOUT, "It is highly important that if you have ANY problems or suggestions, you\n");
fwrite(STDOUT, "report them on the tracker at SourceForge.net or email me at:\n");
fwrite(STDOUT, "jason@php-ems-tools.com\n");
fwrite(STDOUT, "\n\n");

fwrite(STDOUT, "Do you understand this? yes or no\n");
$command = trim(fgets(STDIN));
if($command == "yes")
{
    fwrite(STDOUT, "Thank You.\n");
}
else
{
    fwrite(STDOUT, "Sorry!\n");
    die();
}


fwrite(STDOUT, "\n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");

fwrite(STDOUT, "Before we do anything, let's check to see that config.php is configured correctly.\n");
fwrite(STDOUT, "You can run the config.php check from the command line or via the web, using admin/checkCustomConfig.php\n");
fwrite(STDOUT, "It is recommended that you do this every time you change config.php\n");
fwrite(STDOUT, "This script only checks for MAJOR errors that will being everything to a grinding halt. Minor errors may effect functionality but slip by me right now...\n\n\n\n");
fwrite(STDOUT, "Checking config.php...\n");

$checked = checkCustom();

if($checked == true)
{
    fwrite(STDOUT, "Successful. I was unable to find any MAJOR errors.\n");
}
else
{
    fwrite(STDOUT, "I', sorry, but the above errors are fatal, and I cannot continue.\n Please corrent then and run me again.\n");
    die();
}
fwrite(STDOUT, "\n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");
// check which OS we are running
$uname = posix_uname();
$sysname = $uname['sysname'];
if((stristr($sysname, "win") == false) && (stristr($sysname, "mac") == false) && (stristr($sysname, "darwin") == false))
{
    fwrite(STDOUT, "Are you running this on a Unix, Linux, BSD, or orher *nix operating system? yes or no. \n");
    $command = trim(fgets(STDIN));
    if($command != "yes")
    {
	die("This script is only for *nix machines. Sorry. Please contact the developer for further info.\n");
    }   
}
else
{
    // Windoze or Mac. die.
    fwrite(STDOUT, "This script is not for Windows or Mac. Sorry. \n Please contact the developer for more information.\n");
    die();
}

fwrite(STDOUT, "\n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");

// are we running as root?
fwrite(STDOUT, "This script is about to see if you are running it as root. \n");
fwrite(STDOUT, "If you have already setup the database, you can simply type YES when prompted. \n");
fwrite(STDOUT, "Root permissions are only needed to initially setup the database.\n \n");

$euid = posix_geteuid();
if($euid != 0)
{
    fwrite(STDOUT, "Your user id (uid) does not appear to be 0, that of root. This script must be run as root. \n");
    fwrite(STDOUT, "If you are ABSOLUTELY sure that you ARE root (and you have root set to a uid other than 0??? \n");
    fwrite(STDOUT, "Type YES to continue, otherwise press any key. \n");
    $command = trim(fgets(STDIN));
    if($command != "YES")
    {
	die("Please su to root and run this script as root.");
    }
}
else
{
    fwrite(STDOUT, "Done. \n");
}

fwrite(STDOUT, "\n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");

// has config.php been edited?
fwrite(STDOUT, "Before we go any farther, please be sure that you have edited config.php to the correct values, especially the dbName value. \n\n");
fwrite(STDOUT, "Have you done so? yes or no.\n");
$command = trim(fgets(STDIN));
if($command != "yes")
{
    die("Please edit config.php and then re-run this script.");
}

fwrite(STDOUT, "\n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");

fwrite(STDOUT, "Good. \n\n");
fwrite(STDOUT, "Now let's check your system for some requirements... \n\n");
fwrite(STDOUT, "Obviously, you have PHP installed. The other requirements are: \n");
fwrite(STDOUT, "Web Server (Apache recommended). \n");
fwrite(STDOUT, "MySQL (with command-line client) and anonymous access for 'localhost'\n");
fwrite(STDOUT, "PEAR modules for PHP \n");
fwrite(STDOUT, "The HTML_QuickForm module for PEAR \n\n\n\n");

fwrite(STDOUT, "Do you have a web server installed? yes or no.\n");
$command = trim(fgets(STDIN));
if($command != "yes")
{
    die("Please install a web server and all other requirements in the documentation and then re-run this script. Apache is recommended.");
}

// ask if it's apache
fwrite(STDOUT, "Is your web server Apache? yes or no. \n");
$command = trim(fgets(STDIN));
if($command == "yes")
{
    fwrite(STDOUT, "Good choice. \n");
    fwrite(STDOUT, "Let's see if it's running... \n");
    $httpd = shell_exec("ps -A | grep httpd");
    if(stristr($httpd, "httpd") != false)
    {
	fwrite(STDOUT, "Good, Apache is running. \n");
    }
    else
    {
	fwrite(STDOUT, "Apache is not running. Please correct this problem before continuing. \n");
	die();
    }
}
else
{
    fwrite(STDOUT, "I only know how to test for Apache.\n I will take your word that you have installed and properly configured your webserver. \n");
}

fwrite(STDOUT, "\n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");

// is mysql installed?
fwrite(STDOUT, "Do you have MySQL installed and running? yes or no. \n");
$command = trim(fgets(STDIN));
if($command == "yes")
{
    fwrite(STDOUT, "Let's see if it's running... \n");
    $mysqld = shell_exec("ps -A | grep mysqld");
    if(stristr($mysqld, "mysqld") != false)
    {
	fwrite(STDOUT, "Good, MySQL is running. \n");
    }
    else
    {
	fwrite(STDOUT, "MySQL. is not running. Please correct this problem before continuing. \n");
	die();
    }
    fwrite(STDOUT, "Now, let's see if we have a mysql client available... \n");
    $mysql = shell_exec("which mysql");
    if(stristr($mysql, "mysql") != false)
    {
	fwrite(STDOUT, "Good, the MySQL cilent is at ".$mysql.". \n");
    }
    else
    {
	fwrite(STDOUT, "The MySQL client is not available. Please correct this problem before continuing. \n");
	die();
    }
    fwrite(STDOUT, "Let's see if the mysqladmin program is available... \n");
    $mysqladmin = shell_exec("which mysqladmin");
    if(stristr($mysqladmin, "mysqladmin") != false)
    {
	fwrite(STDOUT, "Good, the MySQL Admin program is at ".$mysqladmin.". \n");
    }
    else
    {
	fwrite(STDOUT, "The mysqladmin program is not available.\n Have you already initialized the database? YES or no. \n");
	$command = trim(fgets(STDIN));
	if($command != "YES")
	{
	    $dontMakeDB = true;
	    fwrite(STDOUT, "Ok, we will proceed but not initialize the database. \n");
	}
	else
	{
	    fwrite(STDOUT, "To initialize the database, I need access to the mysqladmin program. Please correct the problem and then start this script again. \n");
	    die();
	}
    }
}
else
{
    fwrite(STDOUT, "Please install MySQL and have it running before continuing. \n");
    die();
}

fwrite(STDOUT, "\n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");

global $dbName;
// initialize database
fwrite(STDOUT, "Shall we create the database ('".$dbName."') now? \n");
fwrite(STDOUT, "Only type no if you have already done so, and have setup permissions for anonymous at localhost.\n");
fwrite(STDOUT, "yes or no. \n");
$command = trim(fgets(STDIN));
if($command == "yes" || ! $dontMakeDB)
{
    // initialize db as in install-db.sh
    // but don't put anything into it yet
    setupDatabase();
}
else
{
    fwrite(STDOUT, "We will assume you have already created the database called '".$dbName."'.");
}

// try to connect - anonymous at localhost
fwrite(STDOUT, "Trying to connect to MySQL as anonymous at localhost... \n");
$conn = mysql_connect();
if(! $conn)
{
    fwrite(STDOUT, "I'm sorry, but I can't connect to the MySQL server. Perhaps you haven't enabled anonymous permissions for localhost. Please correct this problem and then run this script again... \n");
    die();
}
fwrite(STDOUT, "Successful.\n");

fwrite(STDOUT, "I will now try and select the database called '".$dbName."'.\n");
mysql_select_db($dbName) or die ("I'm sorry, but I was unable to select the database! Perhaps you haven't setup permissions.\n");

fwrite(STDOUT, "\n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");

fwrite(STDOUT, "Great! I was able to select the database.\n");

fwrite(STDOUT, "Now, let's see if there's anything in it...\n");
$query = "SHOW TABLES LIKE 'schedule\_%';";
$failed = false;
$result = mysql_query($query) or ($failed = true);
$num = mysql_num_rows();

if($failed || ($num < 1))
{
    fwrite(STDOUT, "I could not find a table called 'schedule_template', which would be installed if you had setup the database.\n");
    fwrite(STDOUT, "This means that you have not setup the tables yet.\n");
    $setupTables = true;
}
elseif($num == 1)
{
    fwrite(STDOUT, "It looks to me like you have setup the tables already, but never vieded the schedule.\n");
    fwrite(STDOUT, "You should point a web browser to the schedule and view it, to populate a table for this month.\n");
    $setupTables = false;
}
else // we have month tables
{
    fwrite(STDOUT, "I found schedule tables setup for at least one month.\n");
    fwrite(STDOUT, "Since you have a current installation, you should not be running this script.\n");
    fwrite(STDOUT, "I'll exit now...\n");
    die();
}

fwrite(STDOUT, "\n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");

if($setupTables)
{
    fwrite(STDOUT, "\n");
    fwrite(STDOUT, "Ok, now I'll setup the tables for PHP EMS Tools.\n");
    doTableSetup();
}

fwrite(STDOUT, "\n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");

fwrite(STDOUT, "\nOk. We're done with the database work.\n");

// ask for web root directory, or directory we're installing in

fwrite(STDOUT, "Let's see what the current directory is...\n");
$cwd = posix_getcwd();

if($cwd == "/srv/www/htdocs")
{
    fwrite(STDOUT, "Good. The current working directory is ".$cwd."\n");
    fwrite(STDOUT, "This is the default web root directory for Apache.\n");
    fwrite(STDOUT, "If this is not the directory you are installing into, please exit me and run me where you are installing php-ems-tools.\n");
}
else
{
    fwrite(STDOUT, "The current directory is not /srv/www/htdocs, which is the Apache default directory.\n");
    fwrite(STDOUT, "I'm seeing that the current directory is ".$cwd.".\n");
    fwrite(STDOUT, "This should be the directory that you are installing php-ems-tools into, and should be served by your web server.\n");
    fwrite(STDOUT, "Is this correct? yes or no\n");
    $command = trim(fgets(STDIN));
    if($command == "yes")
    {
	fwrite(STDOUT, "Ok, I will use this as the installation directory.\n");
    }
    else
    {
	fwrite(STDOUT, "Please run this program in the directory that you installed php-ems-tools in, and make sure that it is served by your webserver.\n");
	fwrite(STDOUT, "I am exiting now. Please run me again when you have completed the above instructions.\n");
	die();
    }
}

fwrite(STDOUT, "\n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");

fwrite(STDOUT, "Now we'll check for some requirements...\n");

fwrite(STDOUT, "Do you have PEAR Installed? yes or no\n");
$command = trim(fgets(STDIN));
if($command == "yes")
{
    fwrite(STDOUT, "Ok. I don't know how to check for it, so I'll take your word.\n");
}
else
{
    fwrite(STDOUT, "Please install PEAR and HTML_QuickForm before running me.\n");
    fwrite(STDOUT, "I'm exiting now...\n");
    die();
}

fwrite(STDOUT, "\n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");

fwrite(STDOUT, "I'll check to see if the HTML QuickForm package is available.\n");
fwrite(STDOUT, "PHP EMS Tools is coded to require the HTML directory in the current working directory.\n");
fwrite(STDOUT, "This should work with either an actual directory or a link.\n");


fwrite(STDOUT, "Checking for PEAR directory...\n");
if(file_exists("PEAR"))
{
    fwrite(STDOUT, "Ok, the PEAR directory seems to be at ".$cwd."/PEAR\n");
}
else
{
    fwrite(STDOUT, "I cannot find a PEAR directory at ".$cwd."/PEAR\n");
    fwrite(STDOUT, "This may be a problem in the future, but I will assume that there is a reason why I can't find it there.\n");
}

fwrite(STDOUT, "Checking for HTML directory...\n");

if(file_exists("HTML"))
{
    fwrite(STDOUT, "Ok, the HTML directory seems to be at ".$cwd."/HTML\n");
}
else
{
    fwrite(STDOUT, "I cannot find a HTML directory at ".$cwd."/HTML\n");
    fwrite(STDOUT, "PHP EMS Tools must have this directory in order to work.\n");
    fwrite(STDOUT, "I'm exiting now. Please fix this problem and run me again.\n");
    die();
}

fwrite(STDOUT, "\n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");

if(is_dir("HTML"))
{
    fwrite(STDOUT, "It also seems to be an actual directory. Good.\n");
}
elseif(is_link("HTML"))
{
    fwrite(STDOUT, "It seems to be a link. This may or may not work. If you experience problems, please make a hard link.\n");
}
else
{
    fwrite(STDOUT, "It seems to be neither a directory nor a link. This may be a problem...\n");
}

fwrite(STDOUT, "\n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");

fwrite(STDOUT, "Now I'll check for the QuickForm.php file...");
if(file_exists('HTML/QuickForm.php'))
{
    fwrite(STDOUT, "Good, I found it at ".$cwd."/HTML/QuickForm.php\n");
    require_once 'HTML/QuickForm.php';
}
else
{
    fwrite(STDOUT, "I could not find the QuickForm file at ".$cwd."/HTML/QuickForm.php\n");
    fwrite(STDOUT, "This will prevent PHP EMS Tools from running, as this is the location which it is told to find the file.\n");
    fwrite(STDOUT, "I am exiting now...\n");
    die();
}

fwrite(STDOUT, "\n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");

fwrite(STDOUT, "Checking for the file HTML/QuickForm/element.php...\n");
if(file_exists('HTML/QuickForm/element.php'))
{
    fwrite(STDOUT, "Good, I found it at ".$cwd."/HTML/QuickForm/element.php\n");
    require_once 'HTML/QuickForm/element.php';
}
else
{
    fwrite(STDOUT, "I could not find the element.php file at ".$cwd."/HTML/QuickForm/element.php\n");
    fwrite(STDOUT, "This will prevent PHP EMS Tools from running, as this is the location which it is told to find the file.\n");
    fwrite(STDOUT, "I am exiting now...\n");
    die();
}

fwrite(STDOUT, "\n");

fwrite(STDOUT, "\n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");

fwrite(STDOUT, "I'll try and create a form with QuickForm to make sure that it's working...\n");

$form = new HTML_QuickForm('firstForm') or die("I can't create a form. PHP EMS Tools will not run until this problem is corrected.");

if($form)
{
    fwrite(STDOUT, "Success!\n");
}

fwrite(STDOUT, "\n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");

fwrite(STDOUT, "Congratulations, configuration and installation seems to be complete!\n");
fwrite(STDOUT, "\n");

fwrite(STDOUT, "Now, I'll have you test the configration.\n");

fwrite(STDOUT, "You will need to know the URL or IP address to this server on your network.\n");
fwrite(STDOUT, "Unfortunately, I don't know how to detect that.\n");
fwrite(STDOUT, "What is the URL to your server on the local network (i.e. http://192.168.0.6 or http://hostname.domain.com) ?\n");
$URL = trim(fgets(STDIN));

// ask for the directory on the server - i.e. / or /php-ems-tools/
if($cwd == "/srv/www/htdocs")
{
    fwrite(STDOUT, "Since your working directory is ".$cwd.", I will assume that this is the top-level web directory.\n");
    fwrite(STDOUT, "Therefore, the URL for php-ems-tools should be ".$URL."/ \n");
    fwrite(STDOUT, "Is this correct? yes or no\n");
    $command = trim(fgets(STDIN));
    if($command == "yes")
    {
	fwrite(STDOUT, "Good.\n");
	$dir = "/";
    }
    else
    {
	fwrite(STDOUT, "Ok. That's a bit strange, but I should be able to handle it.\n");
	fwrite(STDOUT, "What comes after ".$URL." to view this directory from the web/LAN?\n");
	$dir = trim(fgets(STDIN));
	fwrite(STDOUT, "Ok, so the URL to PHP EMS Tools should be ".$URL.$dir."\n");
    }
}
else
{
    fwrite(STDOUT, "Ok, we need to know the full URL to view PHP EMS Tools from the web/LAN.");
    fwrite(STDOUT, "Is it ".$URL."/ ? yes or no\n");
    $command = trim(fgets(STDIN));
    if($command == "yes")
    {
	fwrite(STDOUT, "Good. That means the full URL should be ".$URL."/ \n");
	$dir = "/";
    }
    else
    {
	fwrite(STDOUT, "Please enter the part of the URL after ".$URL." \n");
	$dir = trim(fgets(STDIN));
	fwrite(STDOUT, "Ok, Our full URL should be ".$URL.$dir."\n");
    }
}

mysql_close($conn);

fwrite(STDOUT, "\n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");

fwrite(STDOUT, "Do you have a wbe browser (lynx is OK) on this machine? yes or no\n");
$command = trim(fgets(STDIN));
if($command == "yes")
{
    fwrite(STDOUT, "Good. Now, you can confirm your installation by pointing your browser to:\n");
    fwrite(STDOUT, "http://127.0.0.1".$dir."\n");
    fwrite(STDOUT, "Please try this.\n");
    fwrite(STDOUT, "Are you able to see the PHP EMS Tools main page? yes or no\n");
    $command = trim(fgets(STDIN));
    if($command == "yes")
    {
	fwrite(STDOUT, "Good. Now we'll try it from another machine.\n");
	fwrite(STDOUT, "Please go to another machine on your network, and point your browser to:\n");
	fwrite(STDOUT, $URL.$dir."\n");
	fwrite(STDOUT, "Does it work? yes or no\n");
	$command2 = trim(fgets(STDIN));
	if($command2 == "yes")
	{
	    fwrite(STDOUT, "GREAT! We're done. Thanks for using PHP EMS Tools.\n");
	    fwrite(STDOUT, "\n");
	    finishText();
	    exit(0);
	}

    }
}

fwrite(STDOUT, "\n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");

fwrite(STDOUT, " \n");
fwrite(STDOUT, "I'm sorry that you are having some problems.\n");
fwrite(STDOUT, "First, please make sure that Apache (or whatever web server you're using) is properly configured and serving pages.\n");
fwrite(STDOUT, "If that was not the problem, you have a few choices for support:\n");
fwrite(STDOUT, "\n");
fwrite(STDOUT, "You can look on our wbe page at\n");
fwrite(STDOUT, "http://www.php-ems-tools.com\n");
fwrite(STDOUT, "Or check out our SourceForge page at:\n");
fwrite(STDOUT, "http://sourceforge.net/projects/php-ems-tools/\n");
fwrite(STDOUT, "where you can look through previous known bugs.\n");
fwrite(STDOUT, "\n");
fwrite(STDOUT, "As a last resort, feel free to e-mail the developer at:\n");
fwrite(STDOUT, "jason@php-ems-tools.com\n");
fwrite(STDOUT, "\n");
fwrite(STDOUT, "\n");
fwrite(STDOUT, "These options are detailed in the documentation at www.php-ems-tools.com\n");
fwrite(STDOUT, "\n");

fwrite(STDOUT, "Please make note of these options, and press ENTER to continue...\n");
$command = trim(fgets(STDIN));
finishText();
exit(0);

function finishText()
{
	    fwrite(STDOUT, "If you have not yet done so, please fill out the user survey at:\n");
	    fwrite(STDOUT, "http://www.php-ems-tools.com/survey.php \n");
	    fwrite(STDOUT, "\n");
	    fwrite(STDOUT, "If you have any questions or comments, please feel free to e-mail the developer at:\n");
	    fwrite(STDOUT, "jason@php-ems-tools.com\n");
	    fwrite(STDOUT, "All feedback is always appreciated.\n");
	    fwrite(STDOUT, "\n");
	    fwrite(STDOUT, "But reports can be submitted via SourceForge at:\n");
	    fwrite(STDOUT, "http://sourceforge.net/projects/php-ems-tools/ \n");
	    fwrite(STDOUT, "Clock on 'Tracker' near the top, and then 'Bugs'\n");
	    fwrite(STDOUT, "\n");
	    fwrite(STDOUT, "If you think that this project is worth a donation, you may send one to:\n");
	    fwrite(STDOUT, "Midland Park Ambulance Corps\n");
	    fwrite(STDOUT, "PO Box 38\n");
	    fwrite(STDOUT, "Midland Park, NJ\n");
	    fwrite(STDOUT, "07432\n");
	    fwrite(STDOUT, "MPAC is a 501(c)(3) non-profit, and donations are tax-deductable.\n");
	    fwrite(STDOUT, "Be sure to include a note that this donation was suggested by PHP EMS Tools\n");
	    fwrite(STDOUT, "\n");
	    fwrite(STDOUT, "\n");
	    fwrite(STDOUT, "Please be sure to check www.php-ems-tools.com occasionally\n");
	    fwrite(STDOUT, "for new releases, patches, and other information\n");
	    fwrite(STDOUT, "\n");
	    fwrite(STDOUT, "\n");
	    fwrite(STDOUT, "Thank You for choosing PHP EMS Tools - software by volunteers for volunteers.\n");
}

function doTableSetup()
{
    global $conn;
  
    // define the queries
$addBk = "CREATE TABLE `addBk` (`pKey` int(10) NOT NULL auto_increment, `company` tinytext, `description` tinytext, `contact` tinytext, `address` tinytext, `phone1` tinytext, `phone2` tinytext, `fax` tinytext, `email` tinytext, `notes` blob, `web` tinytext, PRIMARY KEY  (`pKey`)) ENGINE=MyISAM DEFAULT CHARSET=latin1;";

$rigCheck = "CREATE TABLE `rigCheck` ( `pKey` int(11) NOT NULL auto_increment, `timeStamp` int(11) default NULL, `crew1` tinytext, `crew2` tinytext, `crew3` tinytext, `crew4` tinytext, `rig` tinytext, `comments` text, `stillBroken` text, `sigID` tinytext, `OK` text, `NG` text, `mileage` int(6) default NULL, PRIMARY KEY  (`pKey`)) ENGINE=MyISAM DEFAULT CHARSET=latin1;";

$roster = "CREATE TABLE `roster` ( `EMTid` tinytext NOT NULL, `LastName` tinytext NOT NULL, `FirstName` tinytext NOT NULL, `password` tinytext, `rightsLevel` tinyint(4) NOT NULL default '0', `status` text NOT NULL, `driver` tinyint(1) NOT NULL default '1', `Address` text, `HomePhone` tinytext, `CellPhone` tinytext, `Email` tinytext, `CPR` int(11) default NULL, `EMT` int(11) default NULL, `HazMat` int(11) default NULL, `BBP` int(11) default NULL, `ICS100` int(11) default NULL, `ICS200` int(11) default NULL, `NIMS` int(11) default NULL, `Pkey` int(11) NOT NULL default '0', `SpouseName` varchar(30) character set latin1 collate latin1_bin default NULL, `pwdMD5` tinytext, `shownAs` varchar(15) default NULL, `unitID` tinytext, `textEmail` tinytext, `position` tinytext, `comm1` tinytext, `comm1pos` tinytext, `comm2` tinytext, `comm2pos` tinytext, `officer` tinytext, `PHTLS` int(11) default NULL, `NREMT` int(11) default NULL, `FR` int(11) default NULL, `trustee` tinytext, `comm3` tinytext, `comm3pos` tinytext, `OtherPositions` text, `OtherCerts` text) ENGINE=MyISAM DEFAULT CHARSET=latin1;";

$sched_change_temp = "CREATE TABLE `schedule_change_template` ( `pKey` int(11) NOT NULL auto_increment, `timestamp` int(11) default NULL, `query` text, `EMTid` tinytext, `host` tinytext, `address` tinytext, `form` tinytext, PRIMARY KEY  (`pKey`)) ENGINE=MyISAM DEFAULT CHARSET=latin1;";

$schedule = "CREATE TABLE `schedule_template` ( `PKey` int(11) NOT NULL auto_increment, `Date` tinyint(4) NOT NULL default '0', `1ID` varchar(6) default NULL, `1Start` time default NULL, `1End` time default NULL, `2ID` varchar(6) default NULL, `2Start` time default NULL, `2End` time default NULL, `3ID` varchar(6) default NULL, `3Start` time default NULL, `3End` time default NULL, `4ID` varchar(6) default NULL, `4Start` time default NULL, `4End` time default NULL, `5ID` varchar(6) default NULL, `5Start` time default NULL, `5End` time default NULL, `6ID` varchar(6) default NULL, `6Start` time default NULL, `6End` time default NULL, `message` varchar(50) default NULL, PRIMARY KEY  (`PKey`)) ENGINE=MyISAM DEFAULT CHARSET=latin1;";

$schedule_data = "INSERT INTO `schedule_template` VALUES (1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(2,2,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(3,3,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4,4,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(5,5,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(6,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(7,7,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(8,8,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(9,9,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(10,10,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(11,11,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(12,12,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(13,13,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(14,14,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(15,15,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(16,16,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(17,17,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(18,18,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(19,19,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(20,20,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(21,21,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(22,22,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(23,23,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(24,24,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(25,25,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(26,26,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(27,27,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(28,28,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(29,29,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(30,30,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(31,31,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(32,32,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);";


fwrite(STDOUT, "\n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");

    // addBk
    fwrite(STDOUT, "Creating the addBk table...\n");
    mysql_query($addBk) or die("I'm sorry, but there was an error in creating the addBk table: ".mysql_error());
    fwrite(STDOUT, "Done.\n");
    fwrite(STDOUT, "Checking that it worked...\n");
    $result = mysql_query("SHOW TABLES LIKE addBk;");
    if(mysql_num_rows($result) > 0)
    {
	fwrite(STDOUT, "Ok.\n");
    }
    else
    {
	fwrite(STDOUT, "I'm sorry, but the table isn't there. I'll continue, but you will not be able to access some of the features.\n");
    }

    // rigCheck
    fwrite(STDOUT, "Creating the rigCheck table...\n");
    mysql_query($rigCheck) or die("I'm sorry, but there was an error in creating the rigCheck table: ".mysql_error());
    fwrite(STDOUT, "Done.\n");
    fwrite(STDOUT, "Checking that it worked...\n");
    $result = mysql_query("SHOW TABLES LIKE rigCheck;");
    if(mysql_num_rows($result) > 0)
    {
	fwrite(STDOUT, "Ok.\n");
    }
    else
    {
	fwrite(STDOUT, "I'm sorry, but the table isn't there. I'll continue, but you will not be able to access some of the features.\n");
    }
  
    // roster
    fwrite(STDOUT, "Creating the roster table...\n");
    mysql_query($roster) or die("I'm sorry, but there was an error in creating the roster table: ".mysql_error());
    fwrite(STDOUT, "Done.\n");
    fwrite(STDOUT, "Checking that it worked...\n");
    $result = mysql_query("SHOW TABLES LIKE roster;");
    if(mysql_num_rows($result) > 0)
    {
	fwrite(STDOUT, "Ok.\n");
    }
    else
    {
	fwrite(STDOUT, "I'm sorry, but the table isn't there. I'll continue, but you will not be able to access some of the features.\n");
    }

    // schedule template
    fwrite(STDOUT, "Creating the schedule_template table...\n");
    mysql_query($schedule) or die("I'm sorry, but there was an error in creating the table schedule_template: ".mysql_error());
    fwrite(STDOUT, "Done.\n");
    fwrite(STDOUT, "Checking that it worked...\n");
    $result = mysql_query("SHOW TABLES LIKE schedule_template;");
    if(mysql_num_rows($result) > 0)
    {
	fwrite(STDOUT, "Ok.\n");
    }
    else
    {
	fwrite(STDOUT, "I'm sorry, but the table isn't there. I'll continue, but you will not be able to access some of the features.\n");
    }

    // schedule data
    fwrite(STDOUT, "Adding the default data into the schedule_template table...\n");
    mysql_query($schedule_data) or die("I'm sorry, but there was an error in adding the schedule template data: ".mysql_error());
    fwrite(STDOUT, "Done.\n");
    fwrite(STDOUT, "Checking that it worked...\n");
    $result = mysql_query("SELECT * FROM schedule_template;");
    $row = mysql_fetch_array($result);

    if($row['Date'] == 1 || $row['Date'] == "1")
    {
	fwrite(STDOUT, "Ok.\n");
    }
    else
    {
	fwrite(STDOUT, "I'm sorry, but the table data isn't there. I'll continue, but you will not be able to access some of the features.\n");
    }

    // schedule change template
    fwrite(STDOUT, "Creating the schedule_change_template table...\n");
    mysql_query($sched_change_temp) or die("I'm sorry, but there was an error in creating the table schedule_change_template: ".mysql_error());
    fwrite(STDOUT, "Done.\n");
    fwrite(STDOUT, "Checking that it worked...\n");
    $result = mysql_query("SHOW TABLES LIKE schedule_change_template;");
    if(mysql_num_rows($result) > 0)
    {
	fwrite(STDOUT, "Ok.\n");
    }
    else
    {
	fwrite(STDOUT, "I'm sorry, but the table isn't there. I'll continue, but you will not be able to access some of the features.\n");
    }


    fwrite(STDOUT, "Table creation complete.\n");
    fwrite(STDOUT, "\n");

fwrite(STDOUT, "\n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");

    fwrite(STDOUT, "Now, we have to setup an initial administrative user for you, so that you can make changes and modify other users.\n");
    fwrite(STDOUT, "We will give you a rightsLevel of 2 - the highest.\n");

    $rightsLevel = 2;

    fwrite(STDOUT, "\n\n");
    fwrite(STDOUT, "Now, I will ask you for some information to setup your master administrator user...\n");
    fwrite(STDOUT, "If you want to stop this program at any point, you can type EXIT when prompted for input.\n");
    fwrite(STDOUT, "\n\n");

    // EMTid
    $correct = false;
    while($correct == false)
    {
	fwrite(STDOUT, "Please enter your ID# for the system...\n");
	$EMTid = trim(fgets(STDIN));
	if($EMTid == "EXIT")
	{
	    die("Exiting as per your request...");
	}
	fwrite(STDOUT, "You entered '".$EMTid."' as your ID. Is this correct? yes or no\n");
	$command = trim(fgets(STDIN));
	if($command == "yes")
	{
	    fwrite(STDOUT, "Ok.\n");
	    $correct = true;
	}
	else
	{
	    fwrite(STDOUT, "Let's try again...\n");
	}

    }

    // LastName
    $correct = false;
    while($correct == false)
    {
	fwrite(STDOUT, "Please enter your Last Name for the system...\n");
	$LastName = trim(fgets(STDIN));
	if($LastName == "EXIT")
	{
	    die("Exiting as per your request...");
	}
	fwrite(STDOUT, "You entered '".$LastName."' as your Last Name. Is this correct? yes or no\n");
	$command = trim(fgets(STDIN));
	if($command == "yes")
	{
	    fwrite(STDOUT, "Ok.\n");
	    $correct = true;
	}
	else
	{
	    fwrite(STDOUT, "Let's try again...\n");
	}

    }

    // FirstName
    $correct = false;
    while($correct == false)
    {
	fwrite(STDOUT, "Please enter your First Name for the system...\n");
	$FirstName = trim(fgets(STDIN));
	if($FirstName == "EXIT")
	{
	    die("Exiting as per your request...");
	}
	fwrite(STDOUT, "You entered '".$FirstName."' as your First Name. Is this correct? yes or no\n");
	$command = trim(fgets(STDIN));
	if($command == "yes")
	{
	    fwrite(STDOUT, "Ok.\n");
	    $correct = true;
	}
	else
	{
	    fwrite(STDOUT, "Let's try again...\n");
	}

    }

    // password
    $correct = false;
    while($correct == false)
    {
	fwrite(STDOUT, "Please enter your password for the system...\n");
	fwrite(STDOUT, "NOTICE: this is stored in plain text in the database!\n");
	$password = trim(fgets(STDIN));
	if($password == "EXIT")
	{
	    die("Exiting as per your request...");
	}
	fwrite(STDOUT, "You entered '".$password."' as your password. Is this correct? yes or no\n");
	$command = trim(fgets(STDIN));
	if($command == "yes")
	{
	    fwrite(STDOUT, "Ok.\n");
	    $correct = true;
	}
	else
	{
	    fwrite(STDOUT, "Let's try again...\n");
	}

    }
    $pwdMD5 = md5($password);

    // status
    $correct = false;
    while($correct == false)
    {
	fwrite(STDOUT, "You can now select your status/member type from the options set in config.php.\n");
	fwrite(STDOUT, "Please type one of the following numbers...\n");

	global $memberTypes;
	$types = array();
	for($i = 0; $i < count($memberTypes); $i++)
	{
	    $types[$i] = $memberTypes[$i]['name'];
	}

	for($i = 0; $i < count($types); $i++)
	{
	    fwrite(STDOUT, $i.")\t\t".$types[$i]."\n");
	}
	$status = trim(fgets(STDIN));
	if($status == "EXIT")
	{
	    die("Exiting as per your request...");
	}
	$status = $types[$status];
	fwrite(STDOUT, "You entered '".$status."' as your status. Is this correct? yes or no\n");
	$command = trim(fgets(STDIN));
	if($command == "yes")
	{
	    fwrite(STDOUT, "Ok.\n");
	    $correct = true;
	}
	else
	{
	    fwrite(STDOUT, "Let's try again...\n");
	}

    }

    fwrite(STDOUT, "Ok, we're done collecting your information. You can enter the rest with the web interface.\n");

fwrite(STDOUT, "\n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");

    fwrite(STDOUT, "\n\n");
    fwrite(STDOUT, "Our MySQL query is as follows:\n");

    $userInfo = 'INSERT INTO roster SET EMTid="'.$EMTid.'",LastName="'.$LastName.'",FirstName="'.$FirstName.'",password="'.$password.'",rightsLevel=2,status="'.$status.'",pwdMD5="'.$pwdMD5.'";';

    fwrite(STDOUT, $userInfo."\n");
    fwrite(STDOUT, "If all of the information you entered is correct, we can continue.\n");
    fwrite(STDOUT, "Or we can exit...\n");
    fwrite(STDOUT, "Continue? yes or no\n");
    $command = trim(fgets(STDIN));
    if($command != "yes")
    {
	die("Exiting...");
    }
    fwrite(STDOUT, "\n\n");
    fwrite(STDOUT, "Ok, adding your user to the roster...\n");
    mysql_query($userInfo)or die("I'm sorry, but there was an error in creating the table schedule_change_template: ".mysql_error());
    fwrite(STDOUT, "Done.\n");

fwrite(STDOUT, "\n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");

    fwrite(STDOUT, "Confirming that it was input correctly...\n");
    $result = mysql_query('SELECT * FROM roster WHERE EMTid="'.$EMTid.'";');
    if(mysql_num_rows($result) < 1)
    {
	fwrite(STDOUT, "FAILED.\n");
	fwrite(STDOUT, "I don't know how to recover from this problem. Exiting...\n");
	die();
    }
    fwrite(STDOUT, "Successful.\n");

    fwrite(STDOUT, "Tables and user data successfully added.\n");

fwrite(STDOUT, "\n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");


}

function setupDatabase()
{
    global $conn;
    global $dbName;
    $grants = "CREATE, DELETE, INSERT, SHOW DATABASES, SELECT, UPDATE";
    $query = "FLUSH PRIVILEGES; USE \`php-ems-tools-GVAC\`; GRANT ".$grants." ON \`php-ems-tools-GVAC\`.* TO ''@'localhost'; FLUSH PRIVILEGES;";

fwrite(STDOUT, "\n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");

    fwrite(STDOUT, "Creating the database using the command-line utilities through a shell escape...\n");
    fwrite(STDOUT, "sending 'mysqladmin CREATE ".$dbName."'...\n");
    $res = shell_exec("mysqladmin CREATE ".$dbName);
    fwrite(STDOUT, "Done. \n");
    fwrite(STDOUT, "Testing...\n");

    mysql_select_db($dbName) or die("Database not created, or can't be selected. Exiting...");

    fwrite(STDOUT, "Ok, I was able to select the database.\n");

    fwrite(STDOUT, "PHP EMS Tools needs the following permissions for its' database: \n"); // finish this
    fwrite(STDOUT, $grants."\n");
    fwrite(STDOUT, "I will do this through the MySQL client program.\n");

    fwrite(STDOUT, "\n");
    fwrite(STDOUT, "Shall I set them up now? yes or no\n");
    $command = trim(fgets(STDIN));
    if($command == "yes")
    {
	fwrite(STDOUT, "Ok. Executing the program...\n");
	// PROBLEM WITH THIS
	$shellCmd = 'echo "'.$query.'" | mysql --user=root';
	$res = shell_exec($shellCmd);
	
	// DEBUG
	fwrite(STDOUT, "Shell Command:\n".$shellCmd."\n");
	fwrite(STDOUT, "Result:\n".$res."\n");
	// END DEBUG

	if(strstr($res, "ERROR"))
	{
	    fwrite(STDOUT, "I'm sorry, but for some reason, I could not set the permissions.\n The error message was:\n".$res);
	}
    }
    else
    {
	fwrite(STDOUT, "Ok, I won't do that.\n");
	fwrite(STDOUT, "Please be aware that PHP EMS Tools will NOT work until these permissions are set.\n");
    }

    fwrite(STDOUT, "Database creation complete.\n");

fwrite(STDOUT, "\n\n");
fwrite(STDOUT, "----------------------------------------------------------------------------------- \n\n");
}

?>