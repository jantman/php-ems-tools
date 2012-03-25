PHP EMS Tools
Version 3.0 README
http://www.php-ems-tools.com

$LastChangedRevision: 155 $
$HeadURL: http://svn.jasonantman.com/php-ems-tools/README.txt $


This software is licensed under GNU GPLv3. The full text of this license can
be found in the LICENSE.txt file.

WARNING about downloads:
Please be advised that the most recent version will always be available at
http://www.php-ems-tools.com, as will any patches and bug fixes. Please use
this site as your primary download point. If you want to redistribute this
software, I highly encourage you to link to php-ems-tools.com so that users
can download the newest version. ALSO - I highly encourage you to join the
"announce" mailing list linked to from the homepage. The traffic is very low,
but it will notify you of any updates, some of which may be quite critical.


There are a few important notes:

1) This is NOT secured for a world-wide (i.e. Internet) installation. Doing so
   may put any data on your server and network at risk. It is recommended that
   you install in a way that is only visible to your local network.

2) This version is written for PHP5. If you're still runing PHP4, and can't
   upgrade (which is a security issue), ask me and I'll make a patch for PHP4.

3) There are still some parts of the software that are hard-coded for the two
   shifts (0600-1800). All of it is hard-coded for two shifts total, though
   for the schedule the times can be defined in custom.php. This will be fixed
   in a subsequent release.

4) This software has been tested for four months in a production environment
   at a working squad. However, that is only with one specific configuration,
   shift times, and server. If you experience ANY issues AT ALL, please notify
   me via the Bug Report link at www.php-ems-tools.com. Development of open-source 
   software is a community effort, and bugs will only get fixed if they are reported.
   NOTE - you can also use this bug report link for feature requests and
   support requests.

5) I love to hear back from users. Even if you take one look and hate it, drop
   me an email telling me why. My development is very specific to what is
   requested, so if you request a feature, I'll do my best to put it in the
   next release.

6) I've only used or tested this under Linux. For one thing, the install
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

Installation instructions can be found in the INSTALL.txt file.

FOR BUG REPORTS, feature requests, and support requests, please visit 
www.php-ems-tools.com and use the "Bug Report" link. Feature requests will be
taken seriously an acted on, but may take some time.

If you modify the code for your own use, add features, or fix bugs, please
email me a patch or the modified file so that others can benifit from your
changes.

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