<?php
class ReportController extends Controller {
    public function __construct(){
        if(!isLoggedIn()){
            header('location: ' . URLROOT . '/user/login');
            exit();
        }
        $this->documentModel = $this->model('Document');
    }

    // ฟังก์ชันสำหรับ Export ทะเบียนรับ
    public function exportRegister(){
        $searchTerm = isset($_GET['search']) ? trim($_GET['search']) : null;
        
        // --- ดึงข้อมูลทั้งหมดโดยไม่แบ่งหน้า ---
        // เราจะใช้ Logic เดียวกับใน DocumentController->index() แต่ไม่ส่ง limit/offset
        $userRole = $_SESSION['user_role'];
        $userId = $_SESSION['user_id'];

        $documents = [];
        if($userRole == 'director'){
            $documents = $this->documentModel->getDocumentsForUser($userId, $searchTerm);
        } elseif($userRole == 'dept_admin') {
            $documents = $this->documentModel->getDocumentsForDeptAdmin($userId, $searchTerm);
        } // ... (เพิ่ม else if สำหรับ Role อื่นๆ)
        else {
            $documents = $this->documentModel->getDocuments($searchTerm);
        }

        // --- เริ่มสร้างไฟล์ CSV ---
        $filename = "register_export_" . date('Y-m-d') . ".csv";

        // ตั้งค่า Header เพื่อบอกเบราว์เซอร์ให้ดาวน์โหลดไฟล์
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);

        // สร้าง output stream
        $output = fopen('php://output', 'w');
        
        // *** สำคัญมาก: บอก Excel ให้อ่านไฟล์นี้เป็น UTF-8 ***
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // เขียนหัวตาราง (Header)
        fputcsv($output, [
            'วันที่ลงรับ', 'เลขทะเบียนรับ', 'ที่', 'ลงวันที่', 'จาก', 'ถึง', 'เรื่อง', 'สถานะ', 'หมายเหตุ'
        ]);

        // เขียนข้อมูลแต่ละแถว
        foreach($documents as $doc){
            fputcsv($output, [
                date('d/m/Y', strtotime($doc->registration_date)),
                $doc->doc_registration_number,
                $doc->doc_incoming_number,
                $doc->doc_date ? date('d/m/Y', strtotime($doc->doc_date)) : '',
                $doc->doc_from,
                $doc->doc_to,
                $doc->doc_subject,
                translateRoleToThai($doc->status), // ใช้ Helper แปลสถานะ
                $doc->remarks
            ]);
        }
        
        fclose($output);
        exit(); // จบการทำงานทันที
    }

    // ฟังก์ชันสำหรับแสดงหน้ารายงานสรุป
    public function summary(){
        $this->reportModel = $this->model('Report'); // เราจะสร้าง Model ใหม่
        
        // ดึงข้อมูลสรุปจาก Model
        $avgDirectorApprovalTime = $this->reportModel->getAverageApprovalTime('forward_to_director', 'approve_by_director');
        $avgDeputyApprovalTime = $this->reportModel->getAverageApprovalTime('forward_to_deputy', 'approve_by_deputy');

        $data = [
            'avgDirectorApprovalTime' => $this->formatDuration($avgDirectorApprovalTime),
            'avgDeputyApprovalTime' => $this->formatDuration($avgDeputyApprovalTime)
        ];

        $this->view('reports/summary', $data);
    }

    // Helper function สำหรับแปลงวินาทีเป็นข้อความที่อ่านง่าย
    private function formatDuration($seconds){
        if($seconds === null) return 'N/A';
        $days = floor($seconds / 86400);
        $hours = floor(($seconds % 86400) / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        
        $result = '';
        if($days > 0) $result .= $days . ' วัน ';
        if($hours > 0) $result .= $hours . ' ชั่วโมง ';
        if($minutes > 0) $result .= $minutes . ' นาที';
        
        return $result == '' ? 'น้อยกว่า 1 นาที' : trim($result);
    }
}
?>