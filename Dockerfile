FROM php:8.2-apache

# 1. Instal ekstensi PHP mysqli
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# 2. Mengatasi konflik MPM dengan cara menonaktifkan semua MPM 
# lalu mengaktifkan kembali yang dibutuhkan (prefork)
RUN a2dismod mpm_event mpm_worker mpm_prefork && \
    a2enmod mpm_prefork

# 3. Copy file website Anda
COPY . /var/www/html/

# 4. Pastikan file memiliki hak akses yang benar
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
