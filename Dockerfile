FROM php:8.2-apache

# 1. Instal ekstensi PHP mysqli
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli


