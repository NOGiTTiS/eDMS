<?php
class ReportModel extends Model {
    public function __construct(){
        parent::__construct();
    }

    // ฟังก์ชันสำหรับคำนวณระยะเวลาอนุมัติเฉลี่ย (เป็นวินาที)
    public function getAverageApprovalTime($startAction, $endAction){
        $this->db->query("
            SELECT AVG(TIMESTAMPDIFF(SECOND, start_flow.created_at, end_flow.created_at)) as avg_seconds
            FROM document_flow as start_flow
            JOIN document_flow as end_flow 
                ON start_flow.document_id = end_flow.document_id 
                AND end_flow.created_at > start_flow.created_at
            WHERE start_flow.action = :startAction 
              AND end_flow.action = :endAction
        ");
        $this->db->bind(':startAction', $startAction);
        $this->db->bind(':endAction', $endAction);
        
        $row = $this->db->single();
        return $row ? $row->avg_seconds : null;
    }
}
?>