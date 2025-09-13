#!/bin/bash

#menu  nothan
CHOICE=$(whiptail --title "nothan" --menu "Choisis une option :" 15 50 3 \
"1" "configuration" \
"2" "Option 2" \
"3" "Quitter" 3>&1 1>&2 2>&3)

# Gestion du choix
case $CHOICE in
    1) echo "lancement de la config nothan";;
    2) sudo apt install apache2 && sudo apt update sudo apt install php -y sudo && apt install avahi-daemon && systemctl enable avahi-daemon ;;
    3) echo "Fermeture du menu" ;;
    *) echo "Choix invalide" ;;
esac
clear