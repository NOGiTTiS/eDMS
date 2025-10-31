<?php
class ProfileController extends Controller {
    public function __construct(){
        if(!isLoggedIn()){
            header('location: ' . URLROOT . '/user/login');
            exit();
        }
        $this->userModel = $this->model('User');
    }

    public function index(){
        $this->view('profile/index');
    }

    public function changePassword(){
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $data = [
                'current_password' => trim($_POST['current_password']),
                'new_password' => trim($_POST['new_password']),
                'confirm_password' => trim($_POST['confirm_password']),
                'current_password_err' => '', 'new_password_err' => '', 'confirm_password_err' => ''
            ];

            // Validation
            $user = $this->userModel->getUserById($_SESSION['user_id']);
            if(!password_verify($data['current_password'], $user->password)){
                $data['current_password_err'] = 'รหัสผ่านปัจจุบันไม่ถูกต้อง';
            }
            if(strlen($data['new_password']) < 6){
                $data['new_password_err'] = 'รหัสผ่านใหม่ต้องมีอย่างน้อย 6 ตัวอักษร';
            }
            if($data['new_password'] != $data['confirm_password']){
                $data['confirm_password_err'] = 'รหัสผ่านใหม่ไม่ตรงกัน';
            }

            if(empty($data['current_password_err']) && empty($data['new_password_err']) && empty($data['confirm_password_err'])){
                $newPasswordHash = password_hash($data['new_password'], PASSWORD_DEFAULT);
                if($this->userModel->updatePassword($_SESSION['user_id'], $newPasswordHash)){
                    flash('profile_action_success', 'เปลี่ยนรหัสผ่านเรียบร้อยแล้ว');
                    header('location: ' . URLROOT . '/profile');
                    exit();
                } else { die('Something went wrong'); }
            } else {
                $this->view('profile/index', $data);
            }
        } else {
            header('location: ' . URLROOT . '/profile');
            exit();
        }
    }
}
?>