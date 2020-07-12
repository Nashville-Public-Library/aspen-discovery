# Setting up an Aspen Windows development box

## Substitutions below!
{$CUSTOMER_ASPEN_DOMAIN} = your Library's production Aspen domain, e.g., catalog.library.nashville.org \
{$FAVICON} = your Library's favicon filename, e.g., faviconnashville_favicon.png \
{$LOGO} = your Library's logo filename, e.g., logoNamenashville_logo_responsive.png \
{$SITE} = local development Apen site, e.g., nashville.aspenlocal

## Windows filesystem: create required files and directories

```
> mkdir C:\tmp
> mkdir C:\var\log\aspen-discovery\{$SITE}
> echo.>C:\var\log\aspen-discovery\{$SITE}\error.log
> echo.>C:\var\log\aspen-discovery\{$SITE}\access.log
> mkdir C:\data\aspen-discovery\{$SITE}\covers\large
> mkdir C:\data\aspen-discovery\{$SITE}\covers\medium
> mkdir C:\data\aspen-discovery\{$SITE}\covers\original
> mkdir C:\data\aspen-discovery\{$SITE}\covers\small
```

Copy your Library's logo image file from `https://{$CUSTOMER_ASPEN_DOMAIN}/files/original/{$LOGO}` to `C:\web\aspen-discovery\code\web\files\original\{$LOGO}` \
Copy your Library's favicon from `https://{$CUSTOMER_ASPEN_DOMAIN}/files/original/{$FAVICON}` to `C:\web\aspen-discovery\code\web\files\original\{$FAVICON}` \
Copy your Library's site's config.pwd.ini file from the server `sites/{$SITE}/conf/config.pwd.ini` to `C:\web\aspen-discovery\sites\{$SITE}\conf\config.pwd.ini`

## Edit hosts file to include {$SITE} as 127.0.0.1
Launch Notepad or similar editor as administrator \
Open `C:\Windows\System32\drivers\etc\hosts` \
Insert `127.0.0.1 {$SITE}` \
Save

## Download and install the applications
7-zip: https://www.7-zip.org/download.html \
Filezilla: https://filezilla-project.org/download.php \
GitHub Desktop: https://desktop.github.com/ \
MySQL Workbench: https://dev.mysql.com/downloads/workbench/?os=src \
PhpStorm: https://www.jetbrains.com/phpstorm/download (30-day trial; ~$199/yr) \

### Bitnami WAMP stack installer download: https://bitnami.com/stack/wamp/installer
Run Bitnami WAMP stack installer as administrator \
Accept most defaults \
Set root mysql user password with database credentials in `sites/{$SITE}/conf/config.pwd.ini`

## Configure the applications

### apache
Use the Bitnami WAMP stack manager tool for apache and mysql start, stop, and configure \
Highlight Apache Web Server > Configure > Configure Apache Web Server > Open Conf File > OK > insert at last line:

```
Include "c:/web/aspen-discovery/sites/nashville.aspenlocal/httpd-nashville.aspenlocal.conf"`
```

### php.ini
Edit php.ini `C:\Bitnami\wampstack-{version}\php\php.ini` \
Enable php SOAP extension: remove semicolon comment at line 946 in front of `extension=soap` \
Turn off Opcache so php changes to code are quickly reflected ~l.1774 `opcache.enable=0`

### Aspen Database
In order for the development environment to work, it will need the occasional import from the production server 

#### Export server mariadb structure and content
NB: As of 2020 07 01, simply importing the server's nightly sqldump directly into the development box database will not work. aspen.cached_values is tripping up the import. Also, MySQL Workbench 8.17-19 (and beyond?) have a bug that prevents the MySQL Workbench GUI export. \
Copy the bash script from https://stackoverflow.com/questions/3669121/dump-all-mysql-tables-into-separate-files-automagically to ~/bin on the server

```
~/bin $ bash dump-tables-mysql.sh
Usage: dump-tables-mysql.sh <DB_HOST> <DB_USER> <DB_NAME> [<DIR>]
~/bin $ bash dump-tables-mysql.sh localhost root aspen ../data/
DB password:
Dumping tables into separate SQL command files for database 'aspen' into dir=../data/
DUMPING TABLE: aspen.accelerated_reading_isbn
...
DUMPING TABLE: aspen.website_pages
237 tables dumped from database 'aspen' into dir=../data/
```

#### Copy files to development box
Use Filezilla or another Windows sftp client to transfer the extracted files from the server to the development box \
Unzip files with 7-zip (right click on selected files > 7zip > Extract here) 

#### Import files into development box MySQL
MySQL Workbench > Administration > Data Import/Restore > Import from disk > Import from dump project folder : {$SAVEFILE} > Select Database Objects to Import : aspen, [all tables except cached_values]

### GitHub
Nashville Public Library's James Staub and Bryan Jones code against our institution GitHub account's fork of aspen-discovery; this allows us to make pull requests that Mark Noble can evaluate, comment, slice, dice, accept, and/or reject \
Clone your GitHub.com fork (e.g., Nashville-Public-Library/aspen-discovery) to C:\web\ \
GitHub Dektop > Current Repository > Add > Clone repository... > [highlight your target repository] > Local path: 'C:\web' > Clone

### Aspen solr connection
Aspen's solr index requires LOTS of RAM (e.g., Nashville Public Library =~ 20G). For a development environment that is not going to code solr indexing, James Staub recommends connect with your test or production Aspen server's solr index. To do this: \
Edit sites/{$SITE}/conf/config.pwd.ini (which supersedes Mark Noble's value maintained in config.ini) replace or insert the heading and argument/value pair below

```
[Index]
url = {$CUSTOMER_ASPEN_URL}:{$CUSTOMER_ASPEN_SOLR_PORT}/solr
```

e.g., for Nashville Public Library development boxes:

```
[Index]
url = http://catalogx.library.nashville.org:8080/solr
```
