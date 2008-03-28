#!/bin/bash
##################################################
# BamBotoo setup script
# Author: pedromorgan@gmail.com
##################################################

shopt -s -o nounset

declare BOT_SCRIPT="./bambotoo.php"
declare BOT_INI_DEFAULT="./configs/bambotoo.ini.default"
declare BOT_INI="./configs/bambotoo.ini"
declare MODS_INI_DIR="./configs/modules/"
declare APP_NAME="Bambotoo"

declare prompt
declare retval
declare menuoption
declare TEMP_FILE="/tmp/bambotoo_tmp.$$"
declare WIDTH="80"
declare BACK_TITLE
BACK_TITLE="BamBotoo setup script"

##################################################
# Check script is executable
##################################################
echo "Checking $BOT_SCRIPT is executable"
if test -x $BOT_SCRIPT; then
	echo "OK"
else
	prompt="The '$BOT_SCRIPT' script is not executable.\n\nMake is executable?"
	dialog  --backtitle "$BACK_TITLE" --title "Permissions" --yesno "$prompt" 10 $WIDTH ; retval=$?
	if [ $retval = "0" ]; then
		echo "Making script executable"
		chmod +x $BOT_SCRIPT
	else
		echo "Cannot continue as $BOT_SCRIPT is not executable"
		exit 0
	fi
fi

##################################################
# Check bambotoo.ini config
##################################################
echo "Checking for $BOT_INI file"
if test -e $BOT_INI; then
	echo "OK"
else
	prompt="The required config file '$BOT_INI' does not exist.\n\nCopy the '$BOT_INI_DEFAULT' ? "
	dialog --backtitle "$BACK_TITLE" --title "bot configuration file" --yesno "$prompt" 10 $WIDTH; retval=$?
	if [ $retval = "0" ]; then
		echo "Copying default ini .file"
		cp $BOT_INI_DEFAULT $BOT_INI
	else
		echo "Cannot continue as $BOT_INI does not exist"
	fi
fi


##################################################
# Main Menu
##################################################
until [ "$menuoption" = "0" ] # loop until 0 ie Exit option
do
## Main loop
dialog --backtitle "$BACK_TITLE" --title "Main Menu"\
		--no-cancel --ok-label "Select option" \
		--menu "Please select an option" 10 $WIDTH 5 \
		"1" "Start $APP_NAME IRC bot" \
		"2" "Edit main bot config file '$BOT_INI'" \
		"3" "Edit module config files (Not yet)" \
		"0" "Exit" \
		2> $TEMP_FILE
        menuoption=`cat $TEMP_FILE`
        rm -f $TEMP_FILE

## start bot
# Run bot
if [ "$menuoption" = "1" ]; then
	clear
	dialog  --backtitle "$BACK_TITLE" --title "Start the bot (WIP - both in foreground at the moment)" \
		--ok-label "Foreground" --extra-button --extra-label "Background"\
		--yesno "\nSelect mode to start $APP_NAME\n" 8 $WIDTH \
		; retval=$?
	if [ "$retval" == "1" ]; then
		# cancel
		echo "cancel"
	else
		clear
		./bambotoo.php
	fi
fi

## edit babmotoo.ini
if [ "$menuoption" = "2" ]; then
	${EDITOR:-pico} $BOT_INI
fi

## edit modules.ini
if [ "$menuoption" = "3" ]; then
	dialog --backtitle "$BACK_TITLE" --title "Modules Config"
	${EDITOR:-pico} $MOD_INI
fi


## end loop
done


exit 0
