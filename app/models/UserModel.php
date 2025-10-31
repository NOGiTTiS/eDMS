<?php
class UserModel extends Model {
    public function __construct(){
        // เรียก constructor ของคลาสแม่ (Model) เพื่อให้ $this->db พร้อมใช้งาน
        parent::__construct();
    }

    // ฟังก์ชันสำหรับค้นหาผู้ใช้ด้วย Username
    public function findUserByUsername($username){
        $this->db->query('SELECT * FROM users WHERE username = :username');
        // Bind value
        $this->db->bind(':username', $username);

        $row = $this->db->single();

        // เช็คว่ามี record หรือไม่
        if($this->db->rowCount() > 0){
            return $row; // คืนค่าข้อมูลผู้ใช้ (object)
        } else {
            return false;
        }
    }

    // ฟังก์ชันสำหรับลงทะเบียนผู้ใช้ใหม่
    public function register($data){
        $this->db->query('INSERT INTO users (username, password, full_name, role) VALUES (:username, :password, :full_name, :role)');
        // Bind values
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':password', $data['password']);
        $this->db->bind(':full_name', $data['full_name']);
        $this->db->bind(':role', 'central_admin'); // สมมติว่าผู้ใช้ที่สมัครตอนนี้เป็น ธุรการกลาง

        // Execute
        if($this->db->execute()){
            return true;
        } else {
            return false;
        }
    }

    // ฟังก์ชันใหม่: สำหรับค้นหาผู้ใช้ด้วย Role
    public function getUsersByRole($role){
        $this->db->query('SELECT id, full_name FROM users WHERE role = :role ORDER BY full_name ASC');
        $this->db->bind(':role', $role);
        $results = $this->db->resultSet();
        return $results;
    }

    // ฟังก์ชันใหม่: ดึงข้อมูลผู้ใช้ 1 คนด้วย ID
    public function getUserById($id){
        $this->db->query("SELECT * FROM users WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function getDeptAdminByDepartment($departmentId){
        $this->db->query("SELECT * FROM users WHERE department_id = :department_id AND role = 'dept_admin' LIMIT 1");
        $this->db->bind(':department_id', $departmentId);
        return $this->db->single();
    }

    public function getUsersByRoleAndDept($role, $departmentId){
        $this->db->query("SELECT id, full_name FROM users WHERE role = :role AND department_id = :department_id ORDER BY full_name ASC");
        $this->db->bind(':role', $role);
        $this->db->bind(':department_id', $departmentId);
        return $this->db->resultSet();
    }

    // ฟังก์ชันใหม่: ดึงผู้ใช้ทั้งหมดพร้อมชื่อฝ่าย (สำหรับหน้า Admin)
    public function getAllUsers(){
        $this->db->query("SELECT u.*, d.name as department_name 
                                FROM users u 
                                LEFT JOIN departments d ON u.department_id = d.id 
                                ORDER BY u.created_at DESC");
        return $this->db->resultSet();
    }

    // ฟังก์ชันใหม่: เพิ่มผู้ใช้โดย Admin (คล้าย register แต่ยืดหยุ่นกว่า)
    public function addUserByAdmin($data){
        $this->db->query('INSERT INTO users (username, password, full_name, role, department_id, telegram_chat_id) VALUES (:username, :password, :full_name, :role, :department_id, :telegram_chat_id)');
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':password', $data['password']);
        $this->db->bind(':full_name', $data['full_name']);
        $this->db->bind(':role', $data['role']);
        $this->db->bind(':department_id', $data['department_id']);
        $this->db->bind(':telegram_chat_id', $data['telegram_chat_id']);
        
        return $this->db->execute();
    }

    // ฟังก์ชันใหม่: อัปเดตข้อมูลผู้ใช้โดย Admin
    public function updateUser($data){
        // ตรวจสอบว่ามีการส่งรหัสผ่านใหม่มาหรือไม่
        if(!empty($data['password'])){
            $this->db->query('UPDATE users SET username = :username, password = :password, full_name = :full_name, role = :role, department_id = :department_id, telegram_chat_id = :telegram_chat_id WHERE id = :id');
            $this->db->bind(':password', $data['password']);
        } else {
            $this->db->query('UPDATE users SET username = :username, full_name = :full_name, role = :role, department_id = :department_id, telegram_chat_id = :telegram_chat_id WHERE id = :id');
        }
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':full_name', $data['full_name']);
        $this->db->bind(':role', $data['role']);
        $this->db->bind(':department_id', $data['department_id']);
        $this->db->bind(':telegram_chat_id', $data['telegram_chat_id']);

        return $this->db->execute();
    }

    // ฟังก์ชันใหม่: ลบผู้ใช้
    public function deleteUser($id){
        $this->db->query('DELETE FROM users WHERE id = :id');
        $this->db->bind(':id', $id);
        
        return $this->db->execute();
    }

    public function updatePassword($id, $newPassword){
        $this->db->query('UPDATE users SET password = :password WHERE id = :id');
        $this->db->bind(':id', $id);
        $this->db->bind(':password', $newPassword);
        
        // Execute
        if($this->db->execute()){
            return true;
        } else {
            return false;
        }
    }

    // ฟังก์ชันใหม่: สำหรับอัปเดตข้อมูลโปรไฟล์โดยผู้ใช้เอง
    public function updateProfile($data){
        // ตรวจสอบว่า username ที่แก้ไขใหม่ ซ้ำกับคนอื่นหรือไม่ (ยกเว้นตัวเอง)
        $this->db->query("SELECT id FROM users WHERE username = :username AND id != :id");
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':id', $data['id']);
        $this->db->single();
        if($this->db->rowCount() > 0){
            return 'username_exists'; // คืนค่า error message
        }

        // ถ้าไม่ซ้ำ ก็ทำการอัปเดต
        $this->db->query('UPDATE users SET full_name = :full_name, username = :username, telegram_chat_id = :telegram_chat_id WHERE id = :id');
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':full_name', $data['full_name']);
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':telegram_chat_id', $data['telegram_chat_id']);

        if($this->db->execute()){
            return true;
        } else {
            return false;
        }
    }
}
?>