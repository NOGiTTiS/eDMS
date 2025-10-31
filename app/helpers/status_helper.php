<?php
/**
 * ฟังก์ชันกลางสำหรับเก็บรายการคำแปลสถานะทั้งหมด
 * @return array ['status_key' => 'Thai Text']
 */
function getStatusTranslations() {
    return [
        'received'                      => 'ลงรับแล้ว',
        'pending_director'              => 'รอ ผอ. เกษียณ',
        'approved_director'             => 'ผอ. เกษียณแล้ว',
        'forwarded_to_dept'             => 'ส่งฝ่ายแล้ว',
        'pending_deputy_approval'       => 'รอ รองฯ เกษียณ',
        'pending_dept_admin_action'     => 'รอธุรการฝ่ายดำเนินการ',
        'pending_section_head_action'   => 'รอหัวหน้างานปฏิบัติ',
        'completed'                     => 'เสร็จสิ้น'
    ];
}

/**
 * ฟังก์ชันสำหรับ "แปล" ค่าสถานะเป็นข้อความภาษาไทยธรรมดา
 * @param string $statusKey ค่าสถานะจากฐานข้อมูล (เช่น 'received')
 * @return string ข้อความภาษาไทย
 */
function translateStatus($statusKey) {
    $translations = getStatusTranslations();
    return $translations[$statusKey] ?? ucfirst(str_replace('_', ' ', $statusKey));
}

/**
 * ฟังก์ชันสำหรับ "จัดรูปแบบ" ค่าสถานะเป็น HTML Badge พร้อมสี
 * @param string $status ค่าสถานะจากฐานข้อมูล
 * @return string HTML ของ Badge
 */
function formatStatus($status) {
    $statusText = translateStatus($status); // เรียกใช้ฟังก์ชันแปล
    $bgColor = 'bg-gray-100';
    $textColor = 'text-gray-800';

    switch ($status) {
        case 'received':
            $bgColor = 'bg-blue-100'; $textColor = 'text-blue-800'; break;
        case 'pending_director':
            $bgColor = 'bg-yellow-100'; $textColor = 'text-yellow-800'; break;
        case 'approved_director':
            $bgColor = 'bg-green-100'; $textColor = 'text-green-800'; break;
        case 'forwarded_to_dept':
            $bgColor = 'bg-indigo-100'; $textColor = 'text-indigo-800'; break;
        case 'pending_deputy_approval':
            $bgColor = 'bg-orange-100'; $textColor = 'text-orange-800'; break;
        case 'pending_dept_admin_action':
            $bgColor = 'bg-pink-100'; $textColor = 'text-pink-800'; break;
        case 'pending_section_head_action':
            $bgColor = 'bg-purple-100'; $textColor = 'text-purple-800'; break;
        case 'completed':
            $bgColor = 'bg-gray-200'; $textColor = 'text-gray-800'; break;
    }

    return "<span class=\"px-2 py-1 font-semibold leading-tight {$textColor} {$bgColor} rounded-full\">{$statusText}</span>";
}
?>