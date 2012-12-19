#clone aviashelf portal repository
git clone https://github.com/kronik/aviashelf.git portal
cd portal
 
#create directory for file uploads
mkdir uploads
 
#set permissions
chmod a+w uploads cache temp log log/* cache/*
 
#clone koala framework
git clone git://github.com/vivid-planet/koala-framework.git kwf-lib

#update lang translations
cp trl.xml kwf-lib/

#set framework lang to RU
cp kwf_lib_config.ini kwf-lib/config.ini

#clone required libraries
git clone git://github.com/vivid-planet/library.git library
 
#set path to libraries
echo "library/zend/%version%" > kwf-lib/include_path
 
#set database connection
echo "[production]" > config.local.ini
echo "database.web.host = localhost" >> config.local.ini
echo "database.web.username = root" >> config.local.ini
echo "database.web.password = root" >> config.local.ini
echo "database.web.dbname = aviashelf-portal" >> config.local.ini

mkdir cache/assets
mkdir cache/config
mkdir cache/events
mkdir cache/model
mkdir cache/simple
mkdir cache/table


#setup: create initial database structure
#php bootstrap.php setup
