#!/bin/bash
##################################################################################################################################
#                                                                                                                                #
# Name        : process_identifier_V1.6.sh                                                                                       #
#                                                                                                                                #
# Description : This script is used to read process id from file and search for it in the system                                 #
#               processes, if exist the script will halt without taking any action, if not the script                            #
#               will remove the lock file and start the process again.                                                           #
#                                                                                                                                #
# Usage       : ./process_identifier_V1.6.sh <"command to run"> <Path/to/PID/file> [path/to/out/file] [path/to/error/file]       #
#                                                                                                                                #
#                                                                                                                                #
# Author      : Shady Soliman                                                                                                    #
# Version     : 1.6                                                                                                              #
# Date        : 05/09/2015                                                                                                       #
#                                                                                                                                #
# Change Log:                                                                                                                    #
# Developer     Date            Comment                                                                                          #
# S.Soliman     12/25/2014      Initial version of the script.                                                                   #
# S.Soliman     12/26/2014      Passing arguments to the script instead defining them in config file.                            #
# S.Soliman     12/30/2014      Modifying the script to kill itself after running the desired command.                           #
# S.Soliman     01/02/2015      Add variables for output/error redirection instead of passing thim in                            #
#                               the script argument, and adding the Arguments Validation feature.                                #
# S.Soliman     01/04/2015      Passing path to error/output files as arguments to the script.                                   #
# S.Soliman     05/09/2015      adding feature to verify the process name.                                                       #
##################################################################################################################################

# Define the error and output log files paths, script will ignore it incase passing them to the script as arguments ##
ERROR_LOG_FILE=/dev/null
OUT_LOG_FILE=/dev/null
################################################

# Arguements validation #########################
if [ $# -lt 2 ]; then
        echo
        echo "Mandatory arguement is missing please use the script as following: "
        echo 'Usage : $0 <"Command to run"> <Path/to/pid.lock/file> [Path/to/output/file] [Path/to/error/file]'
        echo
        exit 1;

elif [ $# -eq 3 ]; then
        OUT_LOG_FILE=$3;

elif [ $# -eq 4 ]; then
        OUT_LOG_FILE=$3;
        ERROR_LOG_FILE=$4;

elif [ $# -gt 4 ]; then
        echo
        echo "Too many arguements, please use the script as following: "
        echo "Usage : $0 <Command to run> <Path to pid.lock file> [Path/to/output/file] [Path/to/error/file]"
        echo
        exit 1;
fi

# Variables ###############################################

export PATH=/usr/local/bin:$PATH
SCRIPT_PID=$$
COMMAND_TO_RUN="$1"
echo "Command to run is : $COMMAND_TO_RUN";
PATH_OF_PID_FILE=$2
echo "Path to PID/LOCk file is : $PATH_OF_PID_FILE";
KILL_AFTER=3

#kill the script after a specific time.

#sleep $KILL_AFTER && kill -9 $SCRIPT_PID >/dev/null 2>/dev/null &


#Start the main script

if [ -f $PATH_OF_PID_FILE ]
        then
                read PID   <  $PATH_OF_PID_FILE;
                echo "Process ID is : $PID";
                PROCESS_NAME_SYS="`ps -p $PID -o args= |cut -d' ' -f2-`"
                echo "Process name from system is : $PROCESS_NAME_SYS";

                if ps -p $PID  > /dev/null
                then

                        if  [ "$COMMAND_TO_RUN" = "$PROCESS_NAME_SYS"  ]
                        then
                                echo "Process # $PID is running on the system and process name $COMMAND_TO_RUN is matching the process running on the system $PROCESS_NAME_SYS";
                        else
                                echo "Process # $PID is running on the system but the process name $COMMAND_TO_RUN is not matching the running process on the system $PROCESS_NAME_SYS";
                                echo "Removing the lock file $PATH_OF_PID_FILE ";
                                rm $PATH_OF_PID_FILE;
                                echo "Rerunning the process again";
                                $COMMAND_TO_RUN > >(while read line; do echo "[$(date +"%Y-%m-%d %T")] ${line}"; done >> $OUT_LOG_FILE) 2> >(while read line; do echo "[$(date +"%Y-%m-%d %T")] ${line}"; done >> $ERROR_LOG_FILE)
                        fi
                else
                        echo "Process # $PID is not running on the system ";
                        echo "Removing the lock file $PATH_OF_PID_FILE ";
                        rm $PATH_OF_PID_FILE;
                        echo "Rerunning the process again";
                        $COMMAND_TO_RUN > >(while read line; do echo "[$(date +"%Y-%m-%d %T")] ${line}"; done >> $OUT_LOG_FILE) 2> >(while read line; do echo "[$(date +"%Y-%m-%d %T")] ${line}"; done >> $ERROR_LOG_FILE)
                fi

else
        echo "PID/LOCK file is not exist, Starting the process ......";
        $COMMAND_TO_RUN > >(while read line; do echo "[$(date +"%Y-%m-%d %T")] ${line}"; done >> $OUT_LOG_FILE) 2> >(while read line; do echo "[$(date +"%Y-%m-%d %T")] ${line}"; done >> $ERROR_LOG_FILE)
fi
exit;