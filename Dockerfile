# ใช้ PHP 8 พร้อม Apache เป็น Image พื้นฐาน
FROM php:8.0-apache

# ติดตั้ง extensions ที่จำเป็นสำหรับ PHP
RUN apt-get update && apt-get install -y \
    libcurl4-openssl-dev \
    && docker-php-ext-install -j$(nproc) curl pdo pdo_mysql

# ตั้งค่า Apache ให้ชี้ไปที่โฟลเดอร์ /public ของเรา
RUN a2enmod rewrite
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf