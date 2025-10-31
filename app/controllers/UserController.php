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
        // ตรวจสอบว่าเป็น POST request หรือไม่
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            $data = [
                'username' => trim($_POST['username']),
                'password' => trim($_POST['password']),
                'username_err' => '',
                'password_err' => '',
            ];

            // --- ตรรกะการตรวจสอบที่ถูกต้อง ---

            // 1. Validate Input
            if(empty($data['username'])) $data['username_err'] = 'กรุณากรอกชื่อผู้ใช้';
            if(empty($data['password'])) $data['password_err'] = 'กรุณากรอกรหัสผ่าน';
            
            // ถ้า Input ไม่ว่างเปล่า ถึงจะเริ่มตรวจสอบกับฐานข้อมูล
            if(empty($data['username_err']) && empty($data['password_err'])){
                // 2. ค้นหาผู้ใช้ด้วย Username
                if($loggedInUser = $this->userModel->findUserByUsername($data['username'])){
                    // ถ้าเจอผู้ใช้ -> 3. ตรวจสอบรหัสผ่าน
                    if(password_verify($data['password'], $loggedInUser->password)){
                        // รหัสผ่านถูกต้อง -> Login สำเร็จ
                        log_activity('LOGIN_SUCCESS', 'User logged in successfully.', $loggedInUser->id, $loggedInUser->username);
                        $this->createUserSession($loggedInUser);
                    } else {
                        // รหัสผ่านผิด
                        log_activity('LOGIN_FAILED', 'Failed login attempt with incorrect password.', null, $data['username']);
                        $data['password_err'] = 'รหัสผ่านไม่ถูกต้อง';
                        $this->view('user/login', $data);
                    }
                } else {
                    // ถ้าไม่เจอผู้ใช้
                    log_activity('LOGIN_FAILED', 'Failed login attempt with non-existent username.', null, $data['username']);
                    $data['username_err'] = 'ไม่พบชื่อผู้ใช้นี้ในระบบ';
                    $this->view('user/login', $data);
                }
            } else {
                // ถ้า Input ว่างเปล่า ก็แค่โหลดหน้าจอพร้อม Error
                $this->view('user/login', $data);
            }

        } else {
            // ถ้าเป็น GET request (เข้ามาดูหน้าฟอร์มปกติ)
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
        log_activity('LOGOUT', 'User logged out.');
        unset($_SESSION['user_id']);
        unset($_SESSION['user_username']);
        unset($_SESSION['user_full_name']);
        unset($_SESSION['user_role']);
        session_destroy();
        header('location: ' . URLROOT . '/user/login');
    }
}
?>