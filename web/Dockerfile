FROM php:7.4-apache

# MariaDB(MySQL) PDO 드라이버 설치 추가
RUN docker-php-ext-install mysqli pdo pdo_mysql

COPY . /var/www/html/

# FROM php:7.4-apache
# RUN docker-php-ext-install mysqli
# COPY . /var/www/html/

