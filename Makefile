# makefile to update CVS and do anything else needed
# Time-stamp: "2008-06-30 21:34:18 jantman"
# $Id$

ifdef LOG
        LOGSTR = $(LOG)
else
        LOGSTR = just working on things
endif

cvsupdate:
	rm *~
	cvs import -m "$(LOGSTR)" php-ems-tools-trunk jantman r_3_1_dev
