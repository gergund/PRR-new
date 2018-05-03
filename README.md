Performance Review Report Tool

>To build .phar file: 
```$vendor/bin/phar-builder package composer.json```

>Download app.phar by URL: 
```$wget https://github.com/gergund/PRR/raw/master/build/app.phar```

>Run Performance Review Report tool with UDS: 
```$php app.phar collect:data application --magento-dir=/var/www/html/ --php-fpm=unix:///var/run/php/php7.0-fpm.sock```

>Run Performance Review Report tool with TCP socket: 
```$php app.phar collect:data application --magento-dir=/var/www/html/ --php-fpm=127.0.0.1:9000```