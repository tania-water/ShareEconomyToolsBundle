#!/bin/bash
BASEDIR=$(dirname $0)
FILES="$BASEDIR/../run/*"
for f in $FILES
do
    if [ -f "$f" ]; then
        read PID < "$f"
        kill -9 $PID > /dev/null 2> /dev/null &
        rm "$f"
    fi
done
echo "Successfully killed all the long running commands"
exit;