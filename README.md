# nothan

pour que nothan fonctionne il faut faire les istallation suivante
sudo apt update
sudo apt install php -y
sudo apt install avahi-daemon #install les config pour que le nom de l'appareil devienne le nom de domain .local
sudo systemctl enable avahi-daemon #permet de faire demarer le domaine local des le demarage de la machine
sudo systemctl start avahi-daemon  #permet de demarer le domaine local 
pour modifier le nom de domain local il faut aller mettre le meme nom dans le fichier et redemarer  /etc/hostname
