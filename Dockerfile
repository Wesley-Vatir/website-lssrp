FROM php:8.2-apache

# Menginstal ekstensi mysqli
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Menyalin file aplikasi ke dalam container
COPY . /var/www/html/

# Memberikan akses
RUN chown -R www-data:www-data /var/www/html
