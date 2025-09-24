#!/bin/bash

# Menu nothan
CHOICE=$(whiptail --title "nothan" --menu "Choisis une option :" 15 50 4 \
"1" "Configuration" \
"2" "Installation logiciel" \
"3" "Mise à jour logiciel" \
"4" "Quitter" 3>&1 1>&2 2>&3)

# Gestion du choix
case $CHOICE in
    1) 
        echo "Lancement de la configuration nothan..."
        sleep 2
        clear
        ;;
    2) 
        echo "Installation des paquets nécessaires..."
        sudo apt update -y
        sudo apt install -y apache2 php avahi-daemon
        sudo systemctl enable avahi-daemon
        clear
        ;;
    3) 
        echo "Mise à jour de nothan..."
        sudo rm -rf /var/www/html/*
        git clone https://github.com/tropicoff/nothan.git /tmp/nothan
        sudo cp -r /tmp/nothan/* /var/www/html/
        rm -rf /tmp/nothan
        clear
        ;;
    4) 
        echo "Au revoir 👋"
        exit 0
        ;;
    *) 
        echo "Choix invalide" 
        ;;
esac

clear
