#!/bin/bash

#menu  nothan
CHOICE=$(whiptail --title "nothan" --menu "Choisis une option :" 15 50 3 \
"1" "" \
"2" "Option 2" \
"3" "Quitter" 3>&1 1>&2 2>&3)

# Gestion du choix
case $CHOICE in
    1) echo "lancement de la config nothan";;
    2) echo "Tu as choisi l'option 2" ;;
    3) echo "Fermeture du menu" ;;
    *) echo "Choix invalide" ;;
esac
