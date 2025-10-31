<?php
class AdminController extends Controller {
    public function __construct(){
        // ด่านแรก: ต้อง Login
        if(!isLoggedIn()){
            header('location: ' . URLROOT . '/user/login');
            exit();
        }
        // ด่านสอง: ต้องเป็น central_admin เท่านั้น
        if($_SESSION['user_role'] != 'central_admin'){
            flash('doc_action_fail', 'คุณไม่มีสิทธิ์เข้าถึงส่วนจัดการนี้', 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative');
            header('location: ' . URLROOT . '/dashboard');
            exit();
        }

        $this->userModel = $this->model('User');
        $this->documentModel = $this->model('Document'); // เราต้องใช้ DocumentModel เพื่อดึงรายชื่อฝ่าย
    }

    // หน้าหลัก: แสดงรายชื่อผู้ใช้ทั้งหมด
    public function index(){
        $users = $this->userModel->getAllUsers();
        $data = ['users' => $users];
        $this->view('admin/index', $data);
    }

    // หน้าเพิ่มผู้ใช้
    public function addUser(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            // Process form
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $data = [
                'full_name' => trim($_POST['full_name']),
                'username' => trim($_POST['username']),
                'password' => trim($_POST['password']),
                'role' => $_POST['role'],
                'department_id' => !empty($_POST['department_id']) ? $_POST['department_id'] : null,
                'telegram_chat_id' => trim($_POST['telegram_chat_id']),
                'full_name_err' => '', 'username_err' => '', 'password_err' => ''
            ];

            // Validation (สามารถเพิ่มได้ตามต้องการ)
            if(empty($data['full_name'])) $data['full_name_err'] = 'กรุณากรอกชื่อ-สกุล';
            if(empty($data['username'])) $data['username_err'] = 'กรุณากรอก Username';
            else if($this->userModel->findUserByUsername($data['username'])) $data['username_err'] = 'Username นี้ถูกใช้งานแล้ว';
            if(empty($data['password'])) $data['password_err'] = 'กรุณากรอกรหัสผ่าน';
            else if(strlen($data['password']) < 6) $data['password_err'] = 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร';

            if(empty($data['full_name_err']) && empty($data['username_err']) && empty($data['password_err'])){
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
                if($this->userModel->addUserByAdmin($data)){
                    flash('user_action_success', 'เพิ่มผู้ใช้ใหม่เรียบร้อยแล้ว');
                    header('location: ' . URLROOT . '/admin');
                    exit();
                } else { die('Something went wrong'); }
            } else {
                // โหลด View พร้อม Error
                $departments = $this->documentModel->getDepartments();
                $data['departments'] = $departments;
                $this->view('admin/add_user', $data);
            }
        } else {
            // โหลดฟอร์มเปล่า
            $departments = $this->documentModel->getDepartments();
            $data = [
                'full_name' => '', 'username' => '', 'password' => '', 'role' => '', 'department_id' => null, 'telegram_chat_id' => '',
                'departments' => $departments,
                'full_name_err' => '', 'username_err' => '', 'password_err' => ''
            ];
            $this->view('admin/add_user', $data);
        }
    }

    // หน้าแก้ไขผู้ใช้
    public function editUser($id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $data = [
                'id' => $id,
                'full_name' => trim($_POST['full_name']),
                'username' => trim($_POST['username']),
                'password' => trim($_POST['password']),
                'role' => $_POST['role'],
                'department_id' => !empty($_POST['department_id']) ? $_POST['department_id'] : null,
                'telegram_chat_id' => trim($_POST['telegram_chat_id']),
                'full_name_err' => '', 'username_err' => ''
            ];
            
            // Validation
            if(empty($data['full_name'])) $data['full_name_err'] = 'กรุณากรอกชื่อ-สกุล';
            if(empty($data['username'])) $data['username_err'] = 'กรุณากรอก Username';

            if(empty($data['full_name_err']) && empty($data['username_err'])){
                if(!empty($data['password'])){
                    $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
                }
                if($this->userModel->updateUser($data)){
                    flash('user_action_success', 'อัปเดตข้อมูลผู้ใช้เรียบร้อยแล้ว');
                    header('location: ' . URLROOT . '/admin');
                    exit();
                } else { die('Something went wrong'); }
            } else {
                $departments = $this->documentModel->getDepartments();
                $data['departments'] = $departments;
                $this->view('admin/edit_user', $data);
            }

        } else {
            // โหลดฟอร์มเปล่า
            $user = $this->userModel->getUserById($id);
            $departments = $this->documentModel->getDepartments();
            $data = [
                'id' => $id,
                'full_name' => $user->full_name, 
                'username' => $user->username, 
                'password' => '', 
                'role' => $user->role, 
                'department_id' => $user->department_id, 
                'telegram_chat_id' => $user->telegram_chat_id,
                'departments' => $departments,
                'full_name_err' => '', 
                'username_err' => '',
                'password_err' => '' // <-- เพิ่ม Key ที่ขาดหายไปตรงนี้
            ];
            $this->view('admin/edit_user', $data);
        }
    }

    // การลบผู้ใช้ (ใช้ POST เพื่อความปลอดภัย)
    public function deleteUser($id){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if($this->userModel->deleteUser($id)){
                flash('user_action_success', 'ลบผู้ใช้เรียบร้อยแล้ว');
                header('location: ' . URLROOT . '/admin');
                exit();
            } else { die('Something went wrong'); }
        } else {
            header('location: ' . URLROOT . '/admin');
            exit();
        }
    }
}
?>