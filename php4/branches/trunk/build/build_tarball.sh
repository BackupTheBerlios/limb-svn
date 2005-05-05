#!/bin/bash

# Builds project release.

RELEASE_NAME=limb-2.3.1

RELEASE_DIR=./release/

SVN=svn

TAR=$RELEASE_NAME.tar.gz
ZIP=$RELEASE_NAME.zip

#==================!!! don't edit below if not sure !!!==================

echo Cleanup...

rm -Rf $RELEASE_DIR
mkdir $RELEASE_DIR

echo Exporting from svn...

$SVN export ../ ${RELEASE_DIR}${RELEASE_NAME}

DIR=`pwd`

cd $RELEASE_DIR

echo Tarring...

tar -zcf $TAR $RELEASE_NAME

echo Zipping...

zip -q -r $ZIP $RELEASE_NAME

cd $DIR
