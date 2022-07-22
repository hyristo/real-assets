FROM webdevops/php-apache:7.2

RUN apt-get update
RUN apt-get install -y apache2

RUN rm /opt/docker/etc/php/php.ini 
COPY php-config/php.ini-customized /opt/docker/etc/php/php.ini


