#!/usr/bin/perl

#
# PHP EMS Tools inc_lint.sh
# Finds all CSS, JS, image and HTML files in current directory, and attempts
# to identify ones that don't appear to be referenced anywhere.
#

use strict; 
use warnings;
use File::Find ();

# File::Find - for the convenience of &wanted calls, including -eval statements:
use vars qw/*name *dir *prune/;
*name   = *File::Find::name;
*dir    = *File::Find::dir;
*prune  = *File::Find::prune;

sub wanted;

# Traverse desired filesystems
File::Find::find({wanted => \&wanted}, './');

sub wanted {
    my ($dev,$ino,$mode,$nlink,$uid,$gid);

    (($dev,$ino,$mode,$nlink,$uid,$gid) = lstat($_));
#    /^.*\.htm.*\z/si
#    && print("$name\n");
    print "HTML: $name\n" if $name =~ /^.*\.htm.*\z/si; # need to ignore phpdoc, etc.
    print "CSS: $name\n" if $name =~ /^.*\.css\z/si;
    # need to add images
    # need to store the above in 'local' lists for later processing...
}

