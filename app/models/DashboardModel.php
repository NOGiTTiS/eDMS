<?php
class DashboardModel extends Model {
    public function __construct(){
        parent::__construct();
    }

    /**
     * ดึงจำนวนงานที่ค้างอยู่ของผู้ใช้ตาม Role
     */
    public function getPendingTaskCount($userId, $userRole){
        $sql = '';
        switch ($userRole) {
            case 'central_admin':
                $sql = "SELECT COUNT(id) as count FROM documents WHERE status IN ('received', 'approved_director')";
                break;
            case 'director':
                $sql = "SELECT COUNT(d.id) as count FROM documents d WHERE d.status = 'pending_director' AND d.id IN (SELECT document_id FROM document_flow WHERE forward_to_id = :user_id)";
                break;
            case 'dept_admin':
                // หา department_id ของ user คนนี้ก่อน
                $this->db->query("SELECT department_id FROM users WHERE id = :user_id");
                $this->db->bind(':user_id', $userId);
                $userDept = $this->db->single();
                if(!$userDept || is_null($userDept->department_id)) return 0;
                
                $sql = "SELECT COUNT(d.id) as count FROM documents d WHERE EXISTS (SELECT 1 FROM document_flow df JOIN users fu ON df.forward_to_id = fu.id WHERE df.document_id = d.id AND fu.department_id = :dept_id) AND d.status IN ('forwarded_to_dept', 'pending_dept_admin_action')";
                $this->db->query($sql);
                $this->db->bind(':dept_id', $userDept->department_id);
                $row = $this->db->single();
                return $row->count;

            case 'deputy_director':
                $sql = "SELECT COUNT(d.id) as count FROM documents d WHERE d.status = 'pending_deputy_approval' AND d.id IN (SELECT document_id FROM document_flow WHERE forward_to_id = :user_id)";
                break;
            case 'section_head':
                $sql = "SELECT COUNT(d.id) as count FROM documents d WHERE d.status = 'pending_section_head_action' AND d.id IN (SELECT document_id FROM document_flow WHERE forward_to_id = :user_id)";
                break;
            default:
                return 0;
        }

        $this->db->query($sql);
        if (strpos($sql, ':user_id') !== false) {
            $this->db->bind(':user_id', $userId);
        }
        $row = $this->db->single();
        return $row->count;
    }

    /**
     * ดึงข้อมูลจำนวนเอกสารในแต่ละสถานะสำหรับทำกราฟ
     */
    public function getDocumentStatusData(){
        $this->db->query("SELECT status, COUNT(id) as count FROM documents GROUP BY status");
        return $this->db->resultSet();
    }
}
?>