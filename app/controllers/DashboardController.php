<?php
class DashboardController extends Controller {
    public function __construct(){
        // บังคับให้ต้อง Login ก่อนเข้าหน้านี้
        if(!isLoggedIn()){
            header('location: ' . URLROOT . '/user/login');
            exit();
        }
        $this->dashboardModel = $this->model('Dashboard');
    }

    public function index(){
        $userId = $_SESSION['user_id'];
        $userRole = $_SESSION['user_role'];

        // ดึงข้อมูลจาก Model
        $pendingCount = $this->dashboardModel->getPendingTaskCount($userId, $userRole);
        $chartData = $this->dashboardModel->getDocumentStatusData();

        $data = [
            'pendingCount' => $pendingCount,
            'chartData' => $chartData
        ];

        $this->view('dashboard/index', $data);
    }
}
?>