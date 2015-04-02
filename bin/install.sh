#!/bin/sh
#############################################################################
#   Installation
#############################################################################

ROOT_DIR=$( cd "$( dirname $0 )" && pwd )
EXTEND_PATH="PATH=$ROOT_DIR:\$PATH";

chmod +x $ROOT_DIR/bluzman

if [[ -f "${HOME}/.bashrc" ]]
then
    echo "$EXTEND_PATH" >> ~/.bashrc
    echo -e "\n" >> ~/.bashrc

    echo -e "\nSUCCESS\n";
    echo "Bluzman has been successfully installed.";

    echo -e "\nStart new session in your terminal or run this command in current session:"
    echo -e "\n     export PATH=\$PATH:/var/www/bluzman/bin\n"
else
    echo -e "\n\e[00;31mWARNING!\n\nMissed ~/.bashrc,so you should add bluzman to \$PATH manually:
    \n     $EXTEND_PATH
    \nOtherwise you must use fullpath to bin\\\bluzman.\e[00m\n";
fi