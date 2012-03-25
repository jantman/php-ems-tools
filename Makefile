# makefile to update CVS and do anything else needed
# Time-stamp: "2008-11-02 18:09:11 jantman"
# $Id: Makefile 118 2008-11-02 23:09:37Z jantman $

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
