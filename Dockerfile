FROM php:8.2-apache

# Nonaktifkan MPM yang mungkin bentrok secara default (event/worker/prefork)
RUN a2dismod mpm_event mpm_worker mpm_prefork

# Aktifkan mpm_prefork (standar untuk PHP-Apache agar kompatibel)
RUN a2enmod mpm_prefork

# Instal dan aktifkan mysqli
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Copy file proyek
COPY . /var/www/html/

EXPOSE 80
