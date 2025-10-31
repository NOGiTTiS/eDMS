<?php
// เริ่มต้น Session สำหรับการ Login
session_start();

// เรียกไฟล์ตั้งค่าหลัก
require_once '../app/config/config.php';

// เรียกไฟล์ Helper
require_once '../app/helpers/session_helper.php';
require_once '../app/helpers/status_helper.php';
require_once '../app/helpers/telegram_helper.php';
require_once '../app/helpers/role_helper.php';

// เรียกไฟล์ Core ทั้งหมด
require_once '../app/core/Database.php';
require_once '../app/core/Core.php';
require_once '../app/core/Controller.php';
require_once '../app/core/Model.php';

// Instantiate Core Class
$init = new Core();
?>