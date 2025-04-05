#!/bin/sh

#This will need to be copied to the server manually to do the setup.
#Expects to be installed on CentOS7
#Run as sudo ./installer_centos9.sh
yum check-update
yum -y install wget
yum -y install httpd
yum -y install http://rpms.remirepo.net/enterprise/remi-release-9.rpm
yum -y install yum-utils
yum-config-manager --enable remi-php83
yum -y install php php-mcrypt php-gd php-curl php-mysql php-zip php-fileinfo php-soap
yum -y install php-xml
yum -y install bind-utils
yum -y install php-intl
yum -y install php-mbstring
yum -y install php-pecl-ssh2
yum -y install php-pgsql
yum -y install php-imagick
yum -y install php-ldap
service httpd start
chkconfig httpd on
# New PHP ini file
# - Change max_memory to 256M (from 128M)
# - Increase max file size to 50M
# - Increase max post size to 50M
mv /etc/php.ini /etc/php.ini.old
cp php.ini /etc/php.ini
dnf -y install mariadb-server
mv /etc/my.cnf /etc/my.cnf.old
cp my.cnf /etc/my.cnf
systemctl start mariadb
systemctl enable mariadb
yum -y install java-11-openjdk
yum -y install unzip
yum -y install strace
yum -y install mytop
yum -y install mysqltuner

#Create temp smarty directories
cd /usr/local/aspen-discovery
mkdir tmp
chown -R apache:apache tmp
chmod -R 755 tmp

#Disable SELinux
setenforce 0
cp install/selinux.config /etc/selinux/config

#Increase entropy
yum -y -q install rng-tools
cp install/limits.conf /etc/security/limits.conf
cp install/rngd.service /etc/systemd/system/multi-user.target.wants/rngd.service

systemctl daemon-reload
systemctl start rngd

yum -y install epel-release
yum -y install certbot python2-certbot-apache

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
#yum install mod_evasive -y
#cp mod_evasive.conf /etc/httpd/conf.d/mod_evasive.conf
#mdir /var/log/mod_evasive
yum remove mod_evasive -y

# mod security is causing issues with file uploads.  Not installing.
#configure mod security
#yum install mod_security -y
yum remove mod_security -y
