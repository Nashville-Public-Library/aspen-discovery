#!/bin/sh

git config --global --add safe.directory /usr/local/aspen-discovery

#This will need to be copied to the server manually to do the setup.
#Expects to be installed on CentOS9
#Run as sudo ./installer_centos9.sh
dnf check-update
dnf -y install wget
dnf -y install curl
dnf -y install httpd
dnf -y install http://rpms.remirepo.net/enterprise/remi-release-9.rpm
dnf -y install dnf-utils
dnf config-manager --enable remi-php83
dnf -y install php php-mcrypt php-gd php-curl php-mysql php-zip php-fileinfo php-soap
dnf -y install php-xml
dnf -y install bind-utils
dnf -y install php-intl
dnf -y install php-mbstring
dnf -y install php-pecl-ssh2
dnf -y install php-pgsql
dnf -y install php-imagick
dnf -y install php-ldap
service httpd start
chkconfig httpd on
# New PHP ini file
# - Change max_memory to 256M
# - Increase max file size to 75M
# - Increase max post size to 75M
mv /etc/php.ini /etc/php.ini.old
cp php.ini /etc/php.ini
php_ini="/etc/php.ini"
grep -q '^memory_limit = 256M' "$php_ini" || sed -Ei 's/^memory_limit = [0-9]+M/memory_limit = 256M/' "$php_ini"
grep -q '^post_max_size = 75M' "$php_ini" || sed -Ei 's/^post_max_size = [0-9]+M/post_max_size = 75M/' "$php_ini"
grep -q '^upload_max_filesize = 75M' "$php_ini" || sed -Ei 's/^upload_max_filesize = [0-9]+M/upload_max_filesize = 75M/' "$php_ini"
grep -q '^session.gc_probability = 0' "$php_ini" || sed -Ei 's/^session.gc_probability = 0/session.gc_probability = 1/' "$php_ini"

dnf -y install mariadb-server
mv /etc/my.cnf /etc/my.cnf.old
cp my.cnf /etc/my.cnf
systemctl start mariadb
systemctl enable mariadb
dnf -y install java-17-openjdk
dnf -y install unzip
dnf -y install strace
dnf -y install mytop
dnf -y install mysqltuner

#Create temp smarty directories
cd /usr/local/aspen-discovery
mkdir tmp
chown -R apache:apache tmp
chmod -R 755 tmp

#Increase entropy
dnf -y -q install rng-tools
cp install/limits.conf /etc/security/limits.conf
cp install/rngd.service /etc/systemd/system/multi-user.target.wants/rngd.service

systemctl daemon-reload
systemctl start rngd

dnf -y install epel-release
dnf -y install certbot python2-certbot-apache

echo "Generate new root password for mariadb at: https://passwordsgenerator.net/ and store in passbolt"
mariadb-secure-installation
#echo "Setting timezone to Mountain Time, update as necessary with timedatectl set-timezone timezone"
echo "Enter the timezone of the server"
read timezone
timedatectl set-timezone $timezone

#Setup LogRotate
cp install/logrotate.conf /etc/logrotate.d/aspen_discovery

cd /usr/local/aspen-discovery/install
bash ./setup_aspen_user.sh

# Disable apache server signature
echo -e "ServerSignature Off \nServerTokens Prod" >> /etc/httpd/conf/httpd.conf

# mod evasive is causing issues with sites that have lots of book covers on one page. Not installing.
# configure mod evasive
#dnf install mod_evasive -y
#cp mod_evasive.conf /etc/httpd/conf.d/mod_evasive.conf
#mdir /var/log/mod_evasive
dnf remove mod_evasive -y

# mod security is causing issues with file uploads.  Not installing.
#configure mod security
#dnf install mod_security -y
dnf remove mod_security -y
