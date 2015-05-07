echo "Downloading composer"
curl -sS https://getcomposer.org/installer | php

echo "Installing dependencies"
php composer.phar install