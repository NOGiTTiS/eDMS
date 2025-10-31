<?php
class DocumentController extends Controller {

    private $documentModel;

    public function __construct(){
        // ป้องกันไม่ให้คนที่ไม่ login เข้าถึงหน้านี้ได้
        if(!isLoggedIn()){
            header('location: ' . URLROOT . '/user/login');
        }
        // โหลด Model ที่จะใช้งาน
        $this->documentModel = $this->model('Document');
        $this->userModel = $this->model('User');
    }

    // หน้าแสดงรายการเอกสารทั้งหมด (ฉบับแก้ไข)
    public function index(){
        $searchTerm = isset($_GET['search']) ? trim($_GET['search']) : null;
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $recordsPerPage = 15; // กำหนดจำนวนรายการต่อหน้า
        $offset = ($currentPage - 1) * $recordsPerPage;

        $userRole = $_SESSION['user_role'];
        $userId = $_SESSION['user_id'];
        
        $totalRecords = 0;
        
        if($userRole == 'director'){
            $totalRecords = $this->documentModel->countDocumentsForUser($userId, $searchTerm);
            $documents = $this->documentModel->getDocumentsForUser($userId, $searchTerm, $recordsPerPage, $offset);
        } 
        // ... (เพิ่ม else if สำหรับ Role อื่นๆ ในทำนองเดียวกัน) ...
        else { // central_admin
            $totalRecords = $this->documentModel->countAllDocuments($searchTerm);
            $documents = $this->documentModel->getDocuments($searchTerm, $recordsPerPage, $offset);
        }
        
        $totalPages = ceil($totalRecords / $recordsPerPage);

        $data = [ 
            'documents' => $documents,
            'searchTerm' => $searchTerm ?? '',
            'currentPage' => $currentPage,
            'totalPages' => $totalPages
        ];
        $this->view('documents/index', $data);
    }

    /**
     * หน้าเพิ่มเอกสารใหม่ (ลงรับหนังสือ)
     */
    public function add(){
         // ===== เพิ่มส่วนควบคุมสิทธิ์การเข้าถึง =====
        if($_SESSION['user_role'] != 'central_admin'){
            // ถ้าไม่ใช่ธุรการกลาง ให้ส่งกลับไปหน้าหลักพร้อมข้อความแจ้งเตือน
            flash('doc_action_fail', 'คุณไม่มีสิทธิ์ในการลงรับหนังสือ', 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative');
            header('location: ' . URLROOT . '/document');
            exit();
        }
        // ===== จบส่วนควบคุมสิทธิ์ =====
        
        // ตรวจสอบว่าเป็นการส่งข้อมูลแบบ POST (กดปุ่มบันทึก) หรือ GET (เข้าหน้าฟอร์มครั้งแรก)
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            // --- Process Form (เมื่อมีการกดบันทึก) ---

            // 1. ทำความสะอาดข้อมูลที่รับมา
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            // 2. จัดการการอัปโหลดไฟล์
            $uploadedFile = $_FILES['document_file'];
            $fileName = '';
            $originalFileName = '';
            $filePath = '';

            if(isset($uploadedFile) && $uploadedFile['error'] == UPLOAD_ERR_OK){
                $uploadDir = 'uploads/';
                // สร้างชื่อไฟล์ใหม่ที่ไม่ซ้ำกัน โดยใช้ timestamp นำหน้า
                $fileName = time() . '_' . basename($uploadedFile['name']);
                $originalFileName = basename($uploadedFile['name']);
                $targetPath = $uploadDir . $fileName;
                
                // ย้ายไฟล์ไปยังโฟลเดอร์ uploads
                if(move_uploaded_file($uploadedFile['tmp_name'], $targetPath)){
                    $filePath = $targetPath;
                } else {
                    die('เกิดข้อผิดพลาดในการอัปโหลดไฟล์');
                }
            }
            
            // 3. รวบรวมข้อมูลทั้งหมดลงใน array $data
            $data = [
                'registration_date' => !empty(trim($_POST['registration_date'])) ? trim($_POST['registration_date']) : null,
                'doc_registration_number' => trim($_POST['doc_registration_number']),
                'doc_incoming_number' => trim($_POST['doc_incoming_number']),
                'doc_date' => !empty(trim($_POST['doc_date'])) ? trim($_POST['doc_date']) : null, // ถ้าว่างให้เป็น null
                'doc_from' => trim($_POST['doc_from']),
                'doc_to' => trim($_POST['doc_to']),
                'doc_subject' => trim($_POST['doc_subject']),
                'remarks' => trim($_POST['remarks']),
                'user_id' => $_SESSION['user_id'],
                'file_name' => $fileName,
                'original_file_name' => $originalFileName,
                'file_path' => $filePath,
                'doc_subject_err' => ''
            ];

            // 4. ตรวจสอบความถูกต้องของข้อมูล (Validation)
            if(empty($data['doc_subject'])){ 
                $data['doc_subject_err'] = 'กรุณากรอกเรื่อง'; 
            }

            // 5. ถ้าข้อมูลถูกต้องทั้งหมด ให้ทำการบันทึก
            if(empty($data['doc_subject_err'])){
                if($newDocId = $this->documentModel->addDocument($data)){
                    log_activity('CREATE_DOCUMENT', 'Created document ID: ' . $newDocId . ' with number ' . $data['doc_registration_number']);
                    // ถ้าบันทึกสำเร็จ สร้าง flash message แล้ว redirect ไปหน้าแรก
                    flash('doc_add_success', 'ลงรับหนังสือเรียบร้อยแล้ว');
                    header('location: ' . URLROOT . '/document');
                    exit();
                } else {
                    die('เกิดข้อผิดพลาดในการบันทึกข้อมูล');
                }
            } else {
                // ถ้าข้อมูลไม่ถูกต้อง ให้โหลดฟอร์มเดิมพร้อมแสดง error
                $this->view('documents/add', $data);
            }

        } else {
            // โหลดฟอร์มเปล่า (Logic ใหม่ที่ดึงค่าจาก Settings)
            
            // 1. ดึงรูปแบบการสร้างเลขทะเบียน (เช่น 'year_based' หรือ 'continuous')
            $format = get_setting('doc_number_format', 'year_based');

            // 2. ดึงเลขล่าสุดจากตาราง settings
            $latestNumber = (int)get_setting('doc_registration_counter', 0);
            $nextNumber = $latestNumber + 1; // เลขถัดไป
            
            $buddhistYear = date("Y") + 543;
            $registrationNumber = '';

            // 3. สร้างเลขทะเบียนตามรูปแบบที่ตั้งค่าไว้
            if ($format == 'year_based') {
                $registrationNumber = $nextNumber . '/' . $buddhistYear;
            } else { // 'continuous'
                $registrationNumber = $nextNumber;
            }

            $data = [
                'registration_date' => date('Y-m-d'),
                'doc_registration_number' => $registrationNumber,
                'doc_incoming_number' => '',
                'doc_date' => '',
                'doc_from' => '',
                'doc_to' => '',
                'doc_subject' => '',
                'remarks' => '',
                'doc_subject_err' => ''
            ];
            // 5. โหลด View ของฟอร์มพร้อมข้อมูลที่เตรียมไว้
            $this->view('documents/add', $data);
        }
    }

    public function show($id){
        // ก่อนอื่น ต้องไปดึงรายชื่อ ผอ. มาก่อน
        // เราต้องเรียก UserModel มาใช้
        $this->userModel = $this->model('User');
        $this->documentModel = $this->model('Document');

        // ดึง "ฝ่าย" ทั้งหมดแทนที่จะดึง "ผู้ใช้"
        $departments = $this->documentModel->getDepartments();

        // ดึงข้อมูลหลักๆ ของเอกสาร
        $document = $this->documentModel->getDocumentById($id);
        $files = $this->documentModel->getDocumentFiles($id);
        $flow = $this->documentModel->getDocumentFlow($id);

        // --- ส่วนที่แก้ไข ---
        // ตรวจสอบก่อนว่า $flow เป็น array ที่มีข้อมูลหรือไม่
        $lastFlow = null;
        $forwardedToId = null;
        $currentDeptId = null;

        if (is_array($flow) && !empty($flow)) {
            $lastFlow = end($flow); // หา flow สุดท้าย
            $forwardedToId = $lastFlow->forward_to_id;
            
            // หา department ของเอกสารฉบับนี้ (จากผู้รับคนล่าสุด)
            if ($forwardedToId) {
                $lastRecipient = $this->userModel->getUserById($forwardedToId);
                $currentDeptId = $lastRecipient ? $lastRecipient->department_id : null;
            }
        }
        
        $forwardedToId = null;
        $currentDeptId = null;

        if (is_array($flow) && !empty($flow)) {
            // วนลูปย้อนหลังจาก flow ล่าสุดเพื่อหา forward_to_id ที่ไม่ใช่ค่าว่าง
            foreach (array_reverse($flow) as $flow_item) {
                if (!empty($flow_item->forward_to_id)) {
                    $forwardedToId = $flow_item->forward_to_id;
                    break; // เมื่อเจอแล้วให้ออกจากลูปทันที
                }
            }
            
            // ถ้าเจอ forward_to_id ให้หา department
            if ($forwardedToId) {
                $lastRecipient = $this->userModel->getUserById($forwardedToId);
                $currentDeptId = $lastRecipient ? $lastRecipient->department_id : null;
            }
        }

        $directors = $this->userModel->getUsersByRole('director');
        $deptAdmins = $this->userModel->getUsersByRole('dept_admin');
        
        // ดึงรายชื่อ รอง ผอ. ที่อยู่ในฝ่ายนี้เท่านั้น
        $deputyDirectors = [];
        if($currentDeptId){
            $deputyDirectors = $this->userModel->getUsersByRoleAndDept('deputy_director', $currentDeptId); // เราจะสร้างฟังก์ชันนี้
        }

        // ดึงรายชื่อ หัวหน้างาน ที่อยู่ในฝ่ายนี้เท่านั้น
        $sectionHeads = [];
        if($currentDeptId){
            $sectionHeads = $this->userModel->getUsersByRoleAndDept('section_head', $currentDeptId);
        }

        $data = [
            'document' => $document,
            'files' => $files,
            'flow' => $flow,
            'directors' => $directors,
            'deptAdmins' => $deptAdmins, 
            'departments' => $departments,
            'deputyDirectors' => $deputyDirectors,
            'sectionHeads' => $sectionHeads,
            'forwarded_to_id' => $forwardedToId // ส่ง ID ผู้รับล่าสุดไปให้ View
        ];

        $this->view('documents/show', $data);
    }

    public function forward($id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'document_id' => $id,
                'forward_to_id' => $_POST['forward_to_id'],
                'comment' => trim($_POST['comment']),
                'user_id' => $_SESSION['user_id']
            ];

            // TODO: Add validation here if needed

            if($this->documentModel->forwardDocument($data)){
                // --- เพิ่มส่วนแจ้งเตือน ---
                $this->userModel = $this->model('User');
                $document = $this->documentModel->getDocumentById($id);
                $director = $this->userModel->getUserById($data['forward_to_id']);

                if($director && !empty($director->telegram_chat_id)){
                    $message = "<b>มีเอกสารใหม่ส่งถึงท่าน!</b>\n\n";
                    $message .= "<b>เรื่อง:</b> " . $document->doc_subject . "\n";
                    $message .= "<b>จาก:</b> " . $_SESSION['user_full_name'] . "\n\n";
                    $message .= "กรุณาตรวจสอบที่: " . URLROOT . "/document/show/" . $id;
                    sendTelegramMessage($director->telegram_chat_id, $message);
                }
                // --- จบส่วนแจ้งเตือน ---

                flash('doc_action_success', 'ส่งหนังสือต่อเรียบร้อยแล้ว', 'bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative');
                header('location: ' . URLROOT . '/document/show/' . $id);
            } else {
                die('Something went wrong');
            }

        } else {
            // If not a POST request, redirect
            header('location: ' . URLROOT . '/document');
        }
    }

    // เมธอดใหม่: สำหรับรับค่าการอนุมัติและลายเซ็นจาก ผอ.
    public function approve($id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            // --- ส่วนจัดการลายเซ็น ---
            $signatureImage = $_POST['signature']; 
            $signatureImage = str_replace('data:image/png;base64,', '', $signatureImage);
            $signatureImage = str_replace(' ', '+', $signatureImage);
            $imageData = base64_decode($signatureImage);
            
            $uploadDir = 'uploads/signatures/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $fileName = 'sig_' . $id . '_' . time() . '.png';
            $filePath = $uploadDir . $fileName;

            file_put_contents($filePath, $imageData);
            // --- จบส่วนจัดการลายเซ็น ---

            $data = [
                'document_id' => $id,
                'comment' => trim($_POST['comment']),
                'signature_path' => $filePath,
                'user_id' => $_SESSION['user_id']
            ];

            if($this->documentModel->approveDocumentByDirector($data)){
                // --- เพิ่มส่วนแจ้งเตือน (ฉบับแก้ไขที่ถูกต้อง) ---
                $this->userModel = $this->model('User');
                $document = $this->documentModel->getDocumentById($id);
                $creator = $this->userModel->getUserById($document->created_by_id); // หาผู้สร้างเอกสารดั้งเดิม

                // ตรวจสอบว่าหาผู้สร้างเจอ และผู้สร้างมี Telegram Chat ID
                if($creator && !empty($creator->telegram_chat_id)){
                    $message = "<b>เอกสารได้รับการอนุมัติแล้ว</b>\n\n";
                    $message .= "<b>เรื่อง:</b> " . htmlspecialchars($document->doc_subject) . "\n";
                    $message .= "<b>โดย:</b> " . htmlspecialchars($_SESSION['user_full_name']) . "\n\n";
                    $message .= "ตรวจสอบได้ที่: " . URLROOT . "/document/show/" . $id;
                    sendTelegramMessage($creator->telegram_chat_id, $message);
                }
                // --- จบส่วนแจ้งเตือน ---

                flash('doc_action_success', 'เกษียณหนังสือเรียบร้อยแล้ว');
                header('location: ' . URLROOT . '/document/show/' . $id);
                exit(); // เพิ่ม exit() เพื่อให้แน่ใจว่าสคริปต์หยุดทำงานหลัง redirect
            } else {
                die('Something went wrong');
            }
        } else {
            header('location: ' . URLROOT . '/document');
            exit();
        }
    }

    // เมธอดใหม่: สำหรับธุรการกลางส่งเรื่องให้ธุรการฝ่าย
    public function forwardToDept($id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            // ตอนนี้ค่าที่ส่งมาคือ department_id ไม่ใช่ user_id
            $departmentId = $_POST['department_id'];

            // ค้นหา user ที่เป็น dept_admin ของฝ่ายนั้น
            $deptAdmin = $this->userModel->getDeptAdminByDepartment($departmentId); // เราจะสร้างฟังก์ชันนี้
            if(!$deptAdmin){ die('ไม่พบธุรการสำหรับฝ่ายที่เลือก'); }

            $data = [
                'document_id' => $id,
                'forward_to_id' => $deptAdmin->id,
                'comment' => trim($_POST['comment']),
                'user_id' => $_SESSION['user_id']
            ];

            if($this->documentModel->forwardToDeptAdmin($data)){
                // --- ส่งการแจ้งเตือน ---
                $this->userModel = $this->model('User');
                $document = $this->documentModel->getDocumentById($id);
                $deptAdmin = $this->userModel->getUserById($data['forward_to_id']);

                if($deptAdmin && !empty($deptAdmin->telegram_chat_id)){
                    $message = "<b>มีเอกสารส่งถึงฝ่ายของท่าน</b>\n\n";
                    $message .= "<b>เรื่อง:</b> " . htmlspecialchars($document->doc_subject) . "\n";
                    $message .= "<b>จาก:</b> ธุรการกลาง (" . $_SESSION['user_full_name'] . ")\n\n";
                    $message .= "กรุณาตรวจสอบที่: " . URLROOT . "/document/show/" . $id;
                    sendTelegramMessage($deptAdmin->telegram_chat_id, $message);
                }
                // --- จบการแจ้งเตือน ---

                flash('doc_action_success', 'ส่งหนังสือต่อไปยังฝ่ายเรียบร้อยแล้ว');
                header('location: ' . URLROOT . '/document/show/' . $id);
                exit();
            } else {
                die('Something went wrong');
            }
        } else {
            header('location: ' . URLROOT . '/document');
            exit();
        }
    }

    // เมธอดใหม่: สำหรับธุรการฝ่ายส่งเรื่องให้ รอง ผอ. ฝ่าย
    public function forwardToDeputy($id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'document_id' => $id,
                'forward_to_id' => $_POST['forward_to_id'],
                'comment' => trim($_POST['comment']),
                'user_id' => $_SESSION['user_id']
            ];

            if($this->documentModel->forwardToDeputyDirector($data)){
                // --- ส่งการแจ้งเตือน ---
                $this->userModel = $this->model('User');
                $document = $this->documentModel->getDocumentById($id);
                $deputy = $this->userModel->getUserById($data['forward_to_id']);

                if($deputy && !empty($deputy->telegram_chat_id)){
                    $message = "<b>มีเอกสารส่งถึงท่าน (รอง ผอ. ฝ่าย)</b>\n\n";
                    $message .= "<b>เรื่อง:</b> " . htmlspecialchars($document->doc_subject) . "\n\n";
                    $message .= "กรุณาตรวจสอบที่: " . URLROOT . "/document/show/" . $id;
                    sendTelegramMessage($deputy->telegram_chat_id, $message);
                }

                flash('doc_action_success', 'ส่งหนังสือต่อไปยัง รอง ผอ. ฝ่ายเรียบร้อยแล้ว');
                header('location: ' . URLROOT . '/document/show/' . $id);
                exit();
            } else {
                die('Something went wrong');
            }
        }
    }

    // เปลี่ยนกลับมาเป็นชื่อ approveDeputy และใช้ Logic ที่ถูกต้อง
    public function approveDeputy($id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            
            // --- ส่วนจัดการลายเซ็น (ถูกต้องแล้ว) ---
            $signatureImage = $_POST['signature']; 
            $signatureImage = str_replace('data:image/png;base64,', '', $signatureImage);
            $signatureImage = str_replace(' ', '+', $signatureImage);
            $imageData = base64_decode($signatureImage);
            
            $uploadDir = 'uploads/signatures/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $fileName = 'sig_' . $id . '_' . time() . '.png';
            $filePath = $uploadDir . $fileName;
            file_put_contents($filePath, $imageData);
            // --- จบส่วนจัดการลายเซ็น ---

            $data = [
                'document_id' => $id,
                'comment' => trim($_POST['comment']),
                'signature_path' => $filePath,
                'user_id' => $_SESSION['user_id']
                // ไม่มี forward_to_id
            ];

            // เรียกใช้ฟังก์ชัน approveByDeputy (ที่ไม่มีการส่งต่อ)
            if($this->documentModel->approveByDeputy($data)){
                // TODO: แจ้งเตือนกลับหาธุรการฝ่าย
                
                flash('doc_action_success', 'เกษียณหนังสือเรียบร้อยแล้ว');
                header('location: ' . URLROOT . '/document/show/' . $id);
                exit();
            } else {
                die('Something went wrong');
            }
        }
    }

    // เมธอดใหม่: สำหรับธุรการฝ่ายส่งให้หัวหน้างาน
    public function assignToSectionHead($id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'document_id' => $id,
                'forward_to_id' => $_POST['forward_to_id'],
                'comment' => trim($_POST['comment']),
                'user_id' => $_SESSION['user_id']
            ];

            if($this->documentModel->forwardToSectionHead($data)){
                
                // --- เพิ่มโค้ดส่วนแจ้งเตือนที่ขาดหายไป ---
                $this->userModel = $this->model('User');
                $document = $this->documentModel->getDocumentById($id);
                $sectionHead = $this->userModel->getUserById($data['forward_to_id']);

                if($sectionHead && !empty($sectionHead->telegram_chat_id)){
                    $message = "<b>มีงานมอบหมายถึงท่าน (หัวหน้างาน)</b>\n\n";
                    $message .= "<b>เรื่อง:</b> " . htmlspecialchars($document->doc_subject) . "\n";
                    $message .= "<b>จาก:</b> " . htmlspecialchars($_SESSION['user_full_name']) . "\n\n";
                    $message .= "กรุณาตรวจสอบที่: " . URLROOT . "/document/show/" . $id;
                    sendTelegramMessage($sectionHead->telegram_chat_id, $message);
                }
                // --- จบส่วนแจ้งเตือน ---

                flash('doc_action_success', 'มอบหมายงานให้หัวหน้างานเรียบร้อยแล้ว');
                header('location: ' . URLROOT . '/document/show/' . $id);
                exit();
            } else {
                die('Something went wrong');
            }
        }
    }

    // เมธอดใหม่: สำหรับธุรการฝ่ายปิดงาน
    public function closeAndExport($id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $data = [
                'document_id' => $id,
                'comment' => trim($_POST['comment']),
                'user_id' => $_SESSION['user_id']
            ];

            if($this->documentModel->completeDocument($data)){
                flash('doc_action_success', 'ปิดงานและส่งออกเอกสารเรียบร้อยแล้ว', 'bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative');
                header('location: ' . URLROOT . '/document/show/' . $id);
                exit();
            }
        }
    }

    // เมธอดใหม่: สำหรับหัวหน้างานรับทราบ
    public function acknowledgeTask($id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'document_id' => $id,
                'comment' => trim($_POST['comment']),
                'user_id' => $_SESSION['user_id']
            ];

            if($this->documentModel->acknowledgeBySectionHead($data)){
                // --- ส่งการแจ้งเตือนกลับหา "ธุรการฝ่าย" ---
                $this->userModel = $this->model('User');
                // หาว่าใครส่งเรื่องมาให้เรา (ธุรการฝ่าย)
                $senderId = $this->documentModel->findUserWhoForwardedTo($id, $_SESSION['user_id']);
                
                if($senderId){
                    $document = $this->documentModel->getDocumentById($id);
                    $deptAdmin = $this->userModel->getUserById($senderId);

                    if($deptAdmin && !empty($deptAdmin->telegram_chat_id)){
                        $message = "<b>หัวหน้างานรับทราบงานแล้ว</b>\n\n";
                        $message .= "<b>เรื่อง:</b> " . htmlspecialchars($document->doc_subject) . "\n";
                        $message .= "<b>โดย:</b> " . htmlspecialchars($_SESSION['user_full_name']) . "\n";
                        $message .= "<b>สถานะ:</b> เสร็จสิ้น\n\n";
                        $message .= "ตรวจสอบได้ที่: " . URLROOT . "/document/show/" . $id;
                        sendTelegramMessage($deptAdmin->telegram_chat_id, $message);
                    }
                }
                // --- จบการแจ้งเตือน ---

                flash('doc_action_success', 'รับทราบและดำเนินการเรียบร้อยแล้ว');
                header('location: ' . URLROOT . '/document/show/' . $id);
                exit();
            } else {
                die('Something went wrong');
            }
        }
    }

    // เมธอดใหม่: หน้าแก้ไขเอกสาร
    public function edit($id){
        // ดึงข้อมูลเอกสารเพื่อตรวจสอบสิทธิ์ก่อน
        $document = $this->documentModel->getDocumentById($id);

        // --- Security Check (บังคับใช้กฎเหล็ก) ---
        if(!$document || $document->created_by_id != $_SESSION['user_id'] || $document->status != 'received'){
            flash('doc_action_fail', 'คุณไม่มีสิทธิ์แก้ไขเอกสารนี้ หรือเอกสารไม่อยู่ในสถานะที่สามารถแก้ไขได้', 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative');
            header('location: ' . URLROOT . '/document');
            exit();
        }

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $data = [
                'id' => $id,
                'registration_date' => !empty(trim($_POST['registration_date'])) ? trim($_POST['registration_date']) : null,
                'doc_registration_number' => trim($_POST['doc_registration_number']),
                'doc_incoming_number' => trim($_POST['doc_incoming_number']),
                'doc_date' => !empty(trim($_POST['doc_date'])) ? trim($_POST['doc_date']) : null,
                'doc_from' => trim($_POST['doc_from']),
                'doc_to' => trim($_POST['doc_to']),
                'doc_subject' => trim($_POST['doc_subject']),
                'remarks' => trim($_POST['remarks']),
                'doc_subject_err' => ''
            ];

            if(empty($data['doc_subject'])){ $data['doc_subject_err'] = 'กรุณากรอกเรื่อง'; }

            if(empty($data['doc_subject_err'])){
                if($this->documentModel->updateDocument($data)){
                    log_activity('UPDATE_DOCUMENT', 'Updated document ID: ' . $id);
                    flash('doc_action_success', 'แก้ไขข้อมูลเอกสารเรียบร้อยแล้ว');
                    header('location: ' . URLROOT . '/document/show/' . $id);
                    exit();
                } else { die('Something went wrong'); }
            } else {
                $this->view('documents/edit', $data);
            }
        } else {
            // โหลดฟอร์มพร้อมข้อมูลเดิม
            $data = [
                'id' => $id,
                'registration_date' => $document->registration_date,
                'doc_registration_number' => $document->doc_registration_number,
                'doc_incoming_number' => $document->doc_incoming_number,
                'doc_date' => $document->doc_date,
                'doc_from' => $document->doc_from,
                'doc_to' => $document->doc_to,
                'doc_subject' => $document->doc_subject,
                'remarks' => $document->remarks,
                'doc_subject_err' => ''
            ];
            $this->view('documents/edit', $data);
        }
    }

    // เมธอดใหม่: การลบเอกสาร
    public function delete($id){
        // ใช้ POST method เท่านั้นเพื่อความปลอดภัย
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $document = $this->documentModel->getDocumentById($id);

            // --- Security Check (บังคับใช้กฎเหล็ก) ---
            if(!$document || $document->created_by_id != $_SESSION['user_id'] || $document->status != 'received'){
                flash('doc_action_fail', 'คุณไม่มีสิทธิ์ลบเอกสารนี้ หรือเอกสารไม่อยู่ในสถานะที่สามารถลบได้', 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative');
                header('location: ' . URLROOT . '/document');
                exit();
            }

            if($this->documentModel->deleteDocument($id)){
                log_activity('DELETE_DOCUMENT', 'Deleted document ID: ' . $id);
                flash('doc_action_success', 'ลบเอกสารเรียบร้อยแล้ว');
                header('location: ' . URLROOT . '/document');
                exit();
            } else { die('Something went wrong'); }
        } else {
            header('location: ' . URLROOT . '/document');
            exit();
        }
    }
}
?>