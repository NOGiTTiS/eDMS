<?php
class UserController extends Controller {
    public function __construct(){
        $this->userModel = $this->model('User');
    }

    public function register(){
        // ตรวจสอบว่าเป็น POST request หรือไม่
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            // ประมวลผลฟอร์ม
            
            // 1. Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            $data = [
                'full_name' => trim($_POST['full_name']),
                'username' => trim($_POST['username']),
                'password' => trim($_POST['password']),
                'confirm_password' => trim($_POST['confirm_password']),
                'full_name_err' => '',
                'username_err' => '',
                'password_err' => '',
                'confirm_password_err' => ''
            ];

            // 2. Validate Data
            if(empty($data['full_name'])){
                $data['full_name_err'] = 'กรุณากรอกชื่อ-สกุล';
            }

            if(empty($data['username'])){
                $data['username_err'] = 'กรุณากรอกชื่อผู้ใช้';
            } else {
                if($this->userModel->findUserByUsername($data['username'])){
                    $data['username_err'] = 'ชื่อผู้ใช้นี้ถูกใช้งานแล้ว';
                }
            }
            
            if(empty($data['password'])){
                $data['password_err'] = 'กรุณากรอกรหัสผ่าน';
            } elseif(strlen($data['password']) < 6){
                $data['password_err'] = 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร';
            }

            if(empty($data['confirm_password'])){
                $data['confirm_password_err'] = 'กรุณายืนยันรหัสผ่าน';
            } else {
                if($data['password'] != $data['confirm_password']){
                    $data['confirm_password_err'] = 'รหัสผ่านไม่ตรงกัน';
                }
            }

            // 3. ตรวจสอบว่าไม่มี error
            if(empty($data['full_name_err']) && empty($data['username_err']) && empty($data['password_err']) && empty($data['confirm_password_err'])){
                // Validated
                
                // 4. Hash Password
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

                // 5. Register User
                if($this->userModel->register($data)){
                    flash('register_success', 'สมัครสมาชิกสำเร็จ! กรุณาเข้าสู่ระบบ');
                    header('location: ' . URLROOT . '/user/login');
                } else {
                    die('Something went wrong');
                }

            } else {
                // โหลด view พร้อมกับ error
                $this->view('user/register', $data);
            }

        } else {
            // โหลดฟอร์มเปล่า
            $data = [
                'full_name' => '',
                'username' => '',
                'password' => '',
                'confirm_password' => '',
                'full_name_err' => '',
                'username_err' => '',
                'password_err' => '',
                'confirm_password_err' => ''
            ];
            $this->view('user/register', $data);
        }
    }

    public function login(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            $data = [
                'username' => trim($_POST['username']),
                'password' => trim($_POST['password']),
                'username_err' => '',
                'password_err' => '',
            ];

            if(empty($data['username'])){
                $data['username_err'] = 'กรุณากรอกชื่อผู้ใช้';
            }
            if(empty($data['password'])){
                $data['password_err'] = 'กรุณากรอกรหัสผ่าน';
            }

            // ตรวจสอบ username
            if($this->userModel->findUserByUsername($data['username'])){
                // User found
            } else {
                $data['username_err'] = 'ไม่พบชื่อผู้ใช้นี้ในระบบ';
            }
            
            if(empty($data['username_err']) && empty($data['password_err'])){
                // Validated
                $loggedInUser = $this->userModel->findUserByUsername($data['username']);
                if($loggedInUser && password_verify($data['password'], $loggedInUser->password)){
                    // Create Session
                    $this->createUserSession($loggedInUser);
                } else {
                    $data['password_err'] = 'รหัสผ่านไม่ถูกต้อง';
                    $this->view('user/login', $data);
                }
            } else {
                $this->view('user/login', $data);
            }

        } else {
            $data = [
                'username' => '',
                'password' => '',
                'username_err' => '',
                'password_err' => '',
            ];
            $this->view('user/login', $data);
        }
    }

    public function createUserSession($user){
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_username'] = $user->username;
        $_SESSION['user_full_name'] = $user->full_name;
        $_SESSION['user_role'] = $user->role;

        // --- Logic Redirect อัจฉริยะ ---
        // 1. เรียก DashboardModel
        $dashboardModel = $this->model('Dashboard');
        // 2. เช็คจำนวนงานค้าง
        $pendingCount = $dashboardModel->getPendingTaskCount($user->id, $user->role);

        // 3. ตัดสินใจว่าจะไปหน้าไหน
        if ($pendingCount > 0) {
            // ถ้ามีงานค้าง ให้ไปที่กล่องงาน (Inbox)
            flash('login_success', 'ยินดีต้อนรับ! คุณมี ' . $pendingCount . ' งานที่รออยู่');
            header('location: ' . URLROOT . '/document');
        } else {
            // ถ้าไม่มีงานค้าง ให้ไปที่ Dashboard
            header('location: ' . URLROOT . '/dashboard');
        }
        exit();
    }

    public function logout(){
        unset($_SESSION['user_id']);
        unset($_SESSION['user_username']);
        unset($_SESSION['user_full_name']);
        unset($_SESSION['user_role']);
        session_destroy();
        header('location: ' . URLROOT . '/user/login');
    }
}
?>