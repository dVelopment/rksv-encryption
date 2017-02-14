#! /bin/bash
export COPY_EXTENDED_ATTRIBUTES_DISABLE=true
export COPYFILE_DISABLE=true
tar -c \
    --exclude='._*' \
    --exclude='.svn' \
    --exclude='.git' \
    --exclude='.DS_Store' \
    --exclude='*.bak' \
    --exclude='vendor' \
    --exclude='*.tar.gz' \
    --exclude='*~' -vzf rksv.tar.gz .
