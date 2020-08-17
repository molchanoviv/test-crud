#!/bin/sh

if [ ! -e ".git/hooks/pre-commit" ]
then
    CUR_DIR=$(pwd)
    echo "Linking git pre-commit hook"
    mkdir -p .git/hooks
    ln -sf "${CUR_DIR}"/contrib/pre-commit .git/hooks/pre-commit
fi
