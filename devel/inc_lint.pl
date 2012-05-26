#!/usr/bin/perl

#
# PHP EMS Tools inc_lint.sh
# Finds all CSS, JS, image and HTML files in current directory, and attempts
# to identify ones that don't appear to be referenced anywhere.
#
package IncLint;
use strict; 
use warnings;
use File::Find ();
use File::Basename;

# File::Find - for the convenience of &wanted calls, including -eval statements:
use vars qw/*name *dir *prune/;
*name   = *File::Find::name;
*dir    = *File::Find::dir;
*prune  = *File::Find::prune;

# prototypes
sub wanted;
sub process_html;
sub process_css;
sub process_js;
sub process_img;
sub has_match_abstract;
sub unshift_filename;
sub process_abstract;

# local variables for file types found
our @files_html = ();
our @files_css = ();
our @files_js = ();
our @files_img = ();

# Traverse desired filesystems
File::Find::find({wanted => \&wanted}, './');
process_html();
process_css();
process_img();
process_js();

#
# Subroutines
#

sub process_html {

}

sub process_css {
    my @ptn_ignore = ();
    my $ptn = 'link.*rel="stylesheet".*';

    print "## CSS ##\n";
    for my $fname (@files_css) {
	process_abstract($fname, $ptn, \@ptn_ignore)
    }
}

sub process_img {
    my @ptn_ignore = ();
    my $ptn = 'img.*src="';

    print "## Images ##\n";
    for my $fname (@files_img) {
	process_abstract($fname, $ptn, \@ptn_ignore)
    }
}

sub process_js {
    my @ptn_ignore = ();
    my $ptn = '<script.*type="text\/javascript".*';

    print "## JS ##\n";
    for my $fname (@files_js) {
	process_abstract($fname, $ptn, \@ptn_ignore)
    }
}

sub process_abstract {
    my $fname = $_[0];
    my $ptn = $_[1];
    my @ptn_ignore = @{ $_[2] };
    my $ptn_match = "";
    my $count = 0;
    my $total = 0;

    $fname = substr($fname, 2) if substr($fname, 0, 2) eq './';
    $ptn_match = quotemeta($fname);
    $ptn_match = $ptn.$ptn_match;
    $count = has_match_abstract($ptn_match, \@ptn_ignore);
    $total = $count;
    print "$count.....$fname\n";

    if($count == 0) {
	my $foo = $fname;
	# unshift directories one at a time until we get to the basename
	$foo = unshift_filename($foo);
	while ($count == 0 && $fname ne $foo) {
	    $ptn_match = quotemeta($foo);
	    $ptn_match = $ptn.$ptn_match;
	    $count = has_match_abstract($ptn_match, \@ptn_ignore);
	    $total = $total + $count;
	    print "\t$count.....$foo\n" if $count != 0;
	    $foo = unshift_filename($foo);
	    last if $foo eq basename($foo);
	}
	# basename
	$ptn_match = quotemeta($foo);
	$ptn_match = $ptn.$ptn_match;
	$count = has_match_abstract($ptn_match, \@ptn_ignore);
	$total = $total + $count;
	print "\t$count.....$foo\n" if $count != 0;
    }
    print "\t$fname APPEARS UNUSED\n" if $total == 0;
    return $total;
}


# abstract sub to process all file types, return the integer number of matches (that don't match one of the ignore patterns)
sub has_match_abstract {
    my $ptn_match = $_[0];
    my @ptn_ignore = @{ $_[1] };
    my $count = 0;

    for my $s (`grep -rin '$ptn_match' *`) { # @TODO - is there a better way to handle this?
	chomp $s;
	my $ignore = 0;
	for my $i (@ptn_ignore) {
	    $ignore = 1 if $s =~ $i;
	}
	if($ignore == 0) { $count ++;}
    }
    return $count;
}

# returns absolute or relative file path with first directory removed, or unaltered input if basename
sub unshift_filename {
    my ($fname) = @_;

    return $fname if $fname eq basename($fname);
    return $fname if index($fname, "/") == 0;

    return substr($fname, index($fname, "/")+1);

}

sub wanted {
    my ($dev,$ino,$mode,$nlink,$uid,$gid);

    (($dev,$ino,$mode,$nlink,$uid,$gid) = lstat($_));

    # exclude certain directories
    return if $name =~ /^.*\/phpdoc\/.*\z/si;
    return if $name =~ /^.*\/newcall\/.*\z/si;

    #print "HTML: $name\n" if $name =~ /^.*\.htm(l?)\z/si; # need to ignore phpdoc, etc.
    push(@files_html, $name) if $name =~ /^.*\.htm.*\z/si;
    #print "CSS: $name\n" if $name =~ /^.*\.css\z/si;
    push(@files_css, $name) if $name =~ /^.*\.css\z/si;
    #print "JS: $name\n" if $name =~ /^.*\.js\z/si;
    push(@files_js, $name) if $name =~ /^.*\.js\z/si;
    #print "IMG: $name\n" if $name =~ /^.*\.(gif|png|jpg|jpeg)\z/si;
    push(@files_img, $name) if $name =~ /^.*\.(gif|png|jpg|jpeg)\z/si;
}

