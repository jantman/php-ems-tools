PHP EMS Tools
Version 2.0 README
http://www.php-ems-tools.com

Time-stamp: "2007-09-14 12:04:26 jantman"


This software is licensed under GNU GPLv3. The full text of this license can
be found in the LICENSE.txt file.


Finally, Release 2.0 is out!

There are a few important notes:

1) This is NOT secured for a world-wide (i.e. Internet) installation. Doing so
   may put any data on your server and network at risk. It is recommended that
   you install in a way that is only visible to your local network.

2) This version is written for PHP5. If you're still runing PHP4, and can't
   upgrade (which is a security issue), ask me and I'll make a patch for PHP4.

3) At the moment, there are a few known bugs. The dispatchSchedule page
   doesn't work, and is hard-coded for two shifts per day, 0600-1800 and
   1800-0600. Also, there is a minor and intermittent issue with
   countHours.php. I will have a patch within a few weeks, please be patient.

4) There are still some parts of the software that are hard-coded for the two
   shifts (0600-1800). All of it is hard-coded for two shifts total, though
   for the schedule the times can be defined in custom.php. This will be fixed
   in a subsequent release.

5) This software has been tested for four months in a production environment
   at a working squad. However, that is only with one specific configuration,
   shift times, and server. If you experience ANY issues AT ALL, please notify
   me via the bug tracker on SourceForge, or via the email address provided at
   www.php-ems-tools.com. Development of open-source software is a community
   effort, and bugs will only get fixed if they are reported.

6) I love to hear back from users. Even if you take one look and hate it, drop
   me an email telling me why. My development is very specific to what is
   requested, so if you request a feature, I'll do my best to put it in the
   next release.

7) I've only used or tested this under Linux. For one thing, the install
   script expects an environment that follows the Unix conventions. Linux,
   BSD, Solaris, etc. *should* all be ok. It has not been tested under
   Windows, but if you're familiar with PHP and web servers and want to give
   it a try, please e-mail me the results. However, I am unable to provide
   support for Non-Unix installations. Please consider running PHP EMS Tools
   on a virtual machine inside of a virtualization program such as VMWare, or
   whatever others may be available for Windows.

HOWTO, INSTALLING, ALL OF THAT FUN STUFF:

There is extensive documentation in the docs/ subdirectory. It is HTML
formatted. If you're working from the command line (like me), you may want to
view it with a text-mode browser like Lynx/Links, or view the on-line
documentation available at http://www.php-ems-tools.com.

Essentially, the process is simple: 
1) Extract the archive in a directory visible to your web server. For
installation, you'll want to allow your user write access to the
directory. Also, make sure that your user can create databases in MySQL.
2) Make sure that your system has all of the prerequisites shown in the
documentation.
3) Run the PHP-based installation script (install.php) - it should handle
almost everything, including setting up the database.



Thanks,
Jason Antman
PHP EMS Tools
Midland Park (Vol.) Amb. Corps, Midland Park, NJ

PS - If you like the software and think it's worth money, or want to support
development efforts, please send a donation to:
Midland Park Ambulance Corps
PO Box 38
Midland Park, NJ
07432

Or donate online via Pay Pal at:
http://www.midlandparkambulance.com

(we are a 501(c)(3) non-profit).