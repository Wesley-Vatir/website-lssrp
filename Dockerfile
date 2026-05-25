FROM php:8.2-apache

# 1. Instal ekstensi PHP mysqli
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli


# 4. Pastikan file memiliki hak akses yang benar
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
