<?php
/**
 * ฟังก์ชันสำหรับบันทึก Log การกระทำต่างๆ
 * @param string $action ประเภทของการกระทำ
 * @param string $details รายละเอียด
 * @param int|null $userId ID ของผู้ใช้ (ถ้ามี)
 * @param string|null $username ชื่อผู้ใช้
 */
function log_activity($action, $details = '', $userId = null, $username = null) {
    // ใช้ DB instance ที่เราสร้างไว้แล้ว
    $db = Database::getInstance();

    // ดึงข้อมูลผู้ใช้จาก Session หากไม่ได้ระบุมา
    if ($userId === null && isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
    }
    if ($username === null && isset($_SESSION['user_username'])) {
        $username = $_SESSION['user_username'];
    }
    
    // ดึง IP Address
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';

    $db->query('INSERT INTO activity_logs (user_id, username, action, details, ip_address) VALUES (:user_id, :username, :action, :details, :ip_address)');
    $db->bind(':user_id', $userId);
    $db->bind(':username', $username ?? 'SYSTEM');
    $db->bind(':action', $action);
    $db->bind(':details', $details);
    $db->bind(':ip_address', $ip_address);
    
    $db->execute();
}
?>