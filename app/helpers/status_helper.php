<?php
/**
 * ฟังก์ชันสำหรับแปลงค่าสถานะเป็น HTML Badge ภาษาไทยพร้อมสี
 * @param string $status ค่าสถานะจากฐานข้อมูล (เช่น 'received')
 * @return string HTML ของ Badge
 */
function formatStatus($status) {
    $statusText = '';
    $bgColor = 'bg-gray-100';
    $textColor = 'text-gray-800';

    switch ($status) {
        case 'received':
            $statusText = 'ลงรับแล้ว';
            $bgColor = 'bg-blue-100';
            $textColor = 'text-blue-800';
            break;
        case 'pending_director':
            $statusText = 'รอ ผอ. เกษียณ';
            $bgColor = 'bg-yellow-100';
            $textColor = 'text-yellow-800';
            break;
        case 'approved_director':
            $statusText = 'ผอ. เกษียณแล้ว';
            $bgColor = 'bg-green-100';
            $textColor = 'text-green-800';
            break;
        case 'forwarded_to_dept':
            $statusText = 'ส่งฝ่ายแล้ว';
            $bgColor = 'bg-indigo-100';
            $textColor = 'text-indigo-800';
            break;
        case 'pending_deputy_approval':
            $statusText = 'รอ รองฯ เกษียณ';
            $bgColor = 'bg-orange-100';
            $textColor = 'text-orange-800';
            break;    
        case 'pending_section_head_action':
            $statusText = 'รอหัวหน้างานปฏิบัติ';
            $bgColor = 'bg-purple-100';
            $textColor = 'text-purple-800';
            break;
        case 'pending_dept_admin_action':
            $statusText = 'รอธุรการฝ่ายดำเนินการ';
            $bgColor = 'bg-pink-100';
            $textColor = 'text-pink-800';
            break;
        case 'completed':
            $statusText = 'เสร็จสิ้น';
            $bgColor = 'bg-gray-200';
            $textColor = 'text-gray-800';
            break;
        // สามารถเพิ่ม case อื่นๆ ได้ในอนาคต
        // case 'pending_deputy':
        //     $statusText = 'รอ รองฯ เกษียณ';
        //     $bgColor = 'bg-orange-100';
        //     $textColor = 'text-orange-800';
        //     break;
        default:
            $statusText = ucfirst(str_replace('_', ' ', $status)); // แสดงผลแบบปกติถ้าไม่ตรงกับ case ไหนเลย
            break;
    }

    return "<span class=\"px-2 py-1 font-semibold leading-tight {$textColor} {$bgColor} rounded-full\">{$statusText}</span>";
}
?>