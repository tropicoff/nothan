#!/bin/bash
while true; do
    clear
    echo "============================"
    echo "Machine : $(hostname).local"
    echo "============================"
    echo ""
    echo "Logs actions www-data :"
    tail -n 20 /var/log/nothan_actions.log
    sleep 2
done
