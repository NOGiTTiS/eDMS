<?php
// Database Credentials
define('DB_HOST', 'localhost'); // ชื่อ service ของ MySQL ใน docker-compose.yml
define('DB_USER', 'krusitti_db');
define('DB_PASS', 'HpXENgteAbC8CzDuXXrQ');
define('DB_NAME', 'krusitti_db_edms');

// App Root
define('APPROOT', dirname(dirname(__FILE__))); // ได้ Path ไปที่ /var/www/html/app

// URL Root
define('URLROOT', 'http://www.xn--12cf1de7cd9byec9b.xn--o3cw4h/edms');  // URL ของโปรเจกต์

// Site Name
define('SITENAME', 'EDMS - ระบบสารบรรณอิเล็กทรอนิกส์');

// Telegram Bot Token (รับมาจาก BotFather ใน Telegram)
define('TELEGRAM_BOT_TOKEN', '8261605253:AAGsh_Iu3r3wTfZ8mr8zxcsdB3IzSJG4jcU');

?>