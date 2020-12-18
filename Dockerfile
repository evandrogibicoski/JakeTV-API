FROM debian:jessie

ARG ENV

# Use baseimage-docker's init system.
CMD ["/sbin/my_init"]


#RUN apt-key adv --keyserver hkp://pgp.mit.edu:80 --recv-keys 40976EAF437D05B5
#RUN apt-key adv --keyserver hkp://pgp.mit.edu:80 --recv-keys 16126D3A3E5C1192

RUN apt-get update
RUN apt-get -y install unzip php5-curl apache2 php5 php5-dev build-essential php-pear git php5-mysql supervisor

RUN curl -sL https://deb.nodesource.com/setup_7.x | bash -
RUN apt-get install -y nodejs build-essential git

RUN mkdir -p /var/lock/apache2 /var/run/apache2 /var/run/sshd /var/log/supervisor
COPY ./provision/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN "America/New_York" | tee /etc/timezone
RUN dpkg-reconfigure --frontend noninteractive tzdata
RUN mkdir -p /share
RUN echo "ServerName local.javetv.tv" | tee /etc/apache2/httpd.conf > /dev/null
RUN sed -i '/DocumentRoot \/var\/www\/html/c DocumentRoot \/share/public' /etc/apache2/sites-enabled/000-default.conf
RUN a2enmod rewrite
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf
RUN sed -i '/<Directory \/var\/www\/>/c <Directory \/share/public>' /etc/apache2/apache2.conf

RUN chown www-data /share

WORKDIR /share
COPY . /share

RUN chown www-data /share/public/uploads/
RUN chmod a+rw /share/public/uploads/


RUN chown www-data:www-data /share/storage/app/public/images
RUN chown -R www-data:www-data /share/storage/app/public/images

RUN chown -R www-data /share/storage
RUN chmod -R a+rw /share/storage
RUN chmod -R a+rw /share/bootstrap/cache

RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer
RUN cd /share; composer install

RUN export ENV=${ENV}
RUN if [ "$ENV" != "local" ]; then npm install; npm run build; fi

RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

EXPOSE 80
CMD ["/usr/bin/supervisord"]
