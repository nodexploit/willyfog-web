#!/usr/bin/env bash

# Install Apache2
apt-get -y install apache2

# Configure virtualhosts
echo "<VirtualHost *:80>
    ServerName $1.com
    DocumentRoot /var/www/$1/public/
    ErrorLog  /var/www/$1/logs/projects-error.log
    CustomLog /var/www/$1/logs/projects-access.log combined
    <Directory '/var/www/$1/public/'>
            Options Indexes Followsymlinks
            AllowOverride All
            Require all granted
    </Directory>
</VirtualHost>" > /etc/apache2/sites-available/$1.conf

a2enmod rewrite
a2ensite $1
a2dissite 000-default.conf

# Install php7
add-apt-repository -y ppa:ondrej/php
apt-get update
apt-get -y install unzip imagemagick
apt-get -y install php7.0 php7.0-soap php7.0-curl php7.0-xml php7.0-zip php7.0-mysql libapache2-mod-php7.0 php7.0-mcrypt php7.0-mbstring php7.0-imagick

# Install and configure XDebug
apt-get -y install php-xdebug

read -r -d '' XDEBUG_CONF << ROTO
xdebug.remote_enable=1
xdebug.remote_handler=dbgp
xdebug.remote_mode=req
xdebug.remote_host=$2
xdebug.remote_port=9000
xdebug.remote_autostart=1
ROTO

echo "$XDEBUG_CONF" >> /etc/php/7.0/apache2/conf.d/20-xdebug.ini


# Install MySQL Server
# Credentials:
#   User: root
#   Password: root
# If you want a different password, change next two lines.
sudo debconf-set-selections <<< 'mysql-server mysql-server/root_password password root'
sudo debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password root'
apt-get -y install mysql-server-5.6

# Install composer
curl -OL https://getcomposer.org/composer.phar
mv composer.phar /usr/local/bin/composer
chmod +x /usr/local/bin/composer

# Install utils
curl -OL https://squizlabs.github.io/PHP_CodeSniffer/phpcs.phar
chmod +x phpcs.phar
mv phpcs.phar /usr/local/bin/phpcs
curl -OL https://squizlabs.github.io/PHP_CodeSniffer/phpcbf.phar
chmod +x phpcbf.phar
mv phpcbf.phar /usr/local/bin/phpcbf

service apache2 restart

echo "------------------------------------------------------"
echo "Installation finished."
echo "------------------------------------------------------"