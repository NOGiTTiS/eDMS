<?php
/*
 * Base Controller
 * Loads the models and views
 */
class Controller {
    // โหลด Model
    public function model($model){
        // สร้างชื่อไฟล์ Model ที่ถูกต้อง เช่น User -> UserModel.php
        $modelName = ucwords($model) . 'Model'; // <-- เปลี่ยนแปลง
        
        // Require model file
        require_once '../app/models/' . $modelName . '.php'; // <-- เปลี่ยนแปลง

        // Instantiate model
        return new $modelName(); // <-- เปลี่ยนแปลง
    }

    // โหลด View (ส่วนนี้เหมือนเดิม)
    public function view($view, $data = []){
        // เช็คว่าไฟล์ view มีอยู่จริงหรือไม่
        if(file_exists('../app/views/' . $view . '.php')){
            require_once '../app/views/' . $view . '.php';
        } else {
            // ถ้าไม่มีไฟล์ ให้หยุดทำงานและแสดง error
            die('View does not exist');
        }
    }
}
?>