<?php
// Database Credentials
define('DB_HOST', 'db'); // ชื่อ service ของ MySQL ใน docker-compose.yml
define('DB_USER', 'user');
define('DB_PASS', 'password');
define('DB_NAME', 'db_edms');

// App Root
define('APPROOT', dirname(dirname(__FILE__))); // ได้ Path ไปที่ /var/www/html/app

// URL Root
define('URLROOT', 'http://localhost:8080'); // URL ของโปรเจกต์

// Site Name
define('SITENAME', 'EDMS - ระบบสารบรรณอิเล็กทรอนิกส์');

// Telegram Bot Token (รับมาจาก BotFather ใน Telegram)
define('TELEGRAM_BOT_TOKEN', '8261605253:AAGsh_Iu3r3wTfZ8mr8zxcsdB3IzSJG4jcU');

?>