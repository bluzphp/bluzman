#!/bin/sh
#############################################################################
#   Installation
#############################################################################

ROOT_DIR=$( cd "$( dirname $0 )" && pwd )
EXTEND_PATH="PATH=$ROOT_DIR:\$PATH";

chmod +x $ROOT_DIR/bluzman

if [ -f "${HOME}/.bashrc" ]
then
    echo "$EXTEND_PATH" >> ~/.bashrc
    echo "\n" >> ~/.bashrc

    echo "\nCongrats! Bluzman has been installed!";
    echo "\n     Start new session in your terminal or run this command in current session:"
    echo "\n     export PATH=\$PATH:$ROOT_DIR\n"
else
    echo "\nWARNING!\n\n     Missed ~/.bashrc, so you should add bluzman to \$PATH manually:
    \n     $EXTEND_PATH
    \n     Otherwise you must use fullpath to $ROOT_DIR/bluzman\n";
fi