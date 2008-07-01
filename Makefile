# makefile to update CVS and do anything else needed
# Time-stamp: "2008-07-01 00:41:31 jantman"
# $Id$

ifdef LOG
        LOGSTR = $(LOG)
else
        LOGSTR = just working on things
endif

cvsupdate:
	cvs import -m "$(LOGSTR)" php-ems-tools-trunk jantman r_3_1_dev
