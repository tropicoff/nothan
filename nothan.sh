#!/bin/bash

#menu  nothan
CHOICE=$(whiptail --title "nothan" --menu "Choisis une option :" 15 50 3 \
"1" "configuration" \
"2" "instalation logiciel" \
"3" "mise a jour logiciel" \
"4" "Quitter" 3>&1 1>&2 2>&3)

# Gestion du choix
case $CHOICE in
    1) echo "lancement de la config nothan" && clear;;
    2) sudo apt install apache2 && sudo apt install php -y && sudo apt install avahi-daemon && systemctl enable avahi-daemon && clear ;;
    3) cd && cd $HOME/nothan/ sudo rm -r /var/www/html/* && git clone https://github.com/tropicoff/nothan.git && clear ;;
    4) exit
    *) echo "Choix invalide" ;;
esac
clear
