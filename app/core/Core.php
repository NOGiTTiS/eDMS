<?php
/*
 * App Core Class
 * Creates URL & loads core controller
 * URL FORMAT - /controller/method/params
 */
class Core {
    // เปลี่ยนค่าเริ่มต้นให้สอดคล้องกับชื่อไฟล์ใหม่
    protected $currentController = 'DashboardController'; // <-- เปลี่ยนแปลง
    protected $currentMethod = 'index';
    protected $params = [];

    public function __construct(){
        $url = $this->getUrl();

        // มองหา Controller ในโฟลเดอร์ app/controllers
        // โดยจะมองหาไฟล์ชื่อ [ชื่อ Controller]Controller.php เช่น PageController.php
        $controllerName = '';
        if(isset($url[0])){
             $controllerName = ucwords($url[0]) . 'Controller';
        }

        if(!empty($controllerName) && file_exists('../app/controllers/' . $controllerName . '.php')){
            // ถ้าเจอ ก็ตั้งให้เป็น Controller ปัจจุบัน
            $this->currentController = $controllerName;
            // unset เพื่อให้เหลือแต่ params
            unset($url[0]);
        }

        // เรียก Controller ที่หาเจอ
        require_once '../app/controllers/'. $this->currentController . '.php';

        // สร้าง instance ของ controller นั้นๆ
        // ชื่อคลาสต้องตรงกับชื่อไฟล์ เช่น new PageController();
        $this->currentController = new $this->currentController;

        // เช็คหา method (ส่วนที่ 2 ของ URL)
        if(isset($url[1])){
            // เช็คว่ามี method นี้อยู่ใน controller หรือไม่
            if(method_exists($this->currentController, $url[1])){
                $this->currentMethod = $url[1];
                // unset เพื่อให้เหลือแต่ params
                unset($url[1]);
            }
        }

        // เก็บค่าที่เหลือใน URL เป็น params
        $this->params = $url ? array_values($url) : [];

        // เรียกใช้งาน method พร้อมส่ง params เข้าไป
        call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
    }

    public function getUrl(){
        if(isset($_GET['url'])){
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            return $url;
        }
        // ถ้าไม่มี url มา ให้ return array ที่มีค่าเริ่มต้นของ controller
        return ['dashboard']; // <-- เปลี่ยนแปลง: เพื่อให้ยังคงหา PageController เจอเมื่อเข้าหน้าแรก
    }
}
?>