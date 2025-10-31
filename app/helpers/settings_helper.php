<?php
// ฟังก์ชันสำหรับดึงการตั้งค่าทั้งหมดมาเก็บไว้ในตัวแปร static เพื่อไม่ให้ query ซ้ำซ้อน
function app_settings() {
    static $settings = null;
    if ($settings === null) {
        // ใช้ DB instance ที่เราสร้างไว้แล้ว
        $db = Database::getInstance();
        $db->query("SELECT * FROM settings");
        $results = $db->resultSet();
        $settings = [];
        foreach($results as $row){
            $settings[$row->setting_key] = $row->setting_value;
        }
    }
    return $settings;
}

// ฟังก์ชันสำหรับดึงค่า setting ทีละตัว
function get_setting($key, $default = '') {
    $settings = app_settings();
    return $settings[$key] ?? $default;
}
?>