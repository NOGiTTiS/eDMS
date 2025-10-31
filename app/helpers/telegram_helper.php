<?php
/**
 * ฟังก์ชันสำหรับส่งข้อความแจ้งเตือนผ่าน Telegram
 * @param string $chatId ID ของผู้รับ
 * @param string $message ข้อความที่จะส่ง
 * @return bool
 */
function sendTelegramMessage($chatId, $message) {
    if (empty(TELEGRAM_BOT_TOKEN) || TELEGRAM_BOT_TOKEN == 'YOUR_TELEGRAM_BOT_TOKEN' || empty($chatId)) {
        return false;
    }

    $token = TELEGRAM_BOT_TOKEN;
    $encodedMessage = urlencode($message);
    $url = "https://api.telegram.org/bot{$token}/sendMessage?chat_id={$chatId}&text={$encodedMessage}&parse_mode=HTML";
    
    // ใช้ file_get_contents ในการยิง API (ง่ายที่สุด)
    // @ ปิดการแสดง error หาก API ยิงไม่สำเร็จ
    $result = @file_get_contents($url);

    return $result !== false;
}
?>