# makefile to update CVS and do anything else needed
# Time-stamp: "2009-03-11 11:43:19 jantman"
# $LastChangedRevision: 155 $
# $HeadURL: http://svn.jasonantman.com/php-ems-tools/Makefile $

ifdef LOGSTR
        LOG = $(LOGSTR)
else
        LOG = just working on things (issue: 161)
endif

ifdef TAG
	TAGSTR = $(TAG)
else
	TAGSTR = main
endif

cvsupdate:
	-rm *~
	-cd inc/ && rm *~
	-cd config/ && rm *~
	cvs import -m "$(LOG)" php-ems-tools-trunk jantman "$(TAGSTR)"
