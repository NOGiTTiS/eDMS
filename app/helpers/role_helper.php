<?php

/**
 * ฟังก์ชันสำหรับดึงรายการบทบาททั้งหมดเป็นภาษาไทย (สำหรับ Dropdown)
 * @return array ['role_key' => 'Thai Name']
 */
function getRoleListThai() {
    return [
        'central_admin'   => 'ธุรการกลาง',
        'director'        => 'ผู้อำนวยการ (ผอ.)',
        'deputy_director' => 'รองผู้อำนวยการ (รองฯ ฝ่าย)',
        'dept_admin'      => 'ธุรการฝ่าย',
        'section_head'    => 'หัวหน้างาน'
    ];
}

/**
 * ฟังก์ชันสำหรับแปล Role Key ภาษาอังกฤษเป็นภาษาไทย
 * @param string $roleKey ค่า Role จากฐานข้อมูล
 * @return string ชื่อ Role ภาษาไทย
 */
function translateRoleToThai($roleKey) {
    $roles = getRoleListThai();
    // ถ้าหา key เจอ ให้คืนค่าภาษาไทย, ถ้าไม่เจอ ให้คืนค่าเดิมที่จัดรูปแบบสวยงาม
    return $roles[$roleKey] ?? ucfirst(str_replace('_', ' ', $roleKey));
}
?>