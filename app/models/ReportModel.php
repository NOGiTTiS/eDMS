<?php
class ReportModel extends Model {
    public function __construct(){ parent::__construct(); }

    // ฟังก์ชันสำหรับสร้างเงื่อนไข WHERE ของวันที่
    private function buildDateWhereClause($month, $year){
        $where = "WHERE YEAR(d.registration_date) = :year";
        if ($month != 'all') {
            $where .= " AND MONTH(d.registration_date) = :month";
        }
        return $where;
    }

    // ฟังก์ชันสำหรับ bind ค่าวันที่
    private function bindDateValues($month, $year){
        $this->db->bind(':year', $year);
        if ($month != 'all') {
            $this->db->bind(':month', $month);
        }
    }

    // แก้ไขฟังก์ชันเดิมและสร้างฟังก์ชันใหม่
    public function getTotalDocuments($month, $year){
        $whereClause = $this->buildDateWhereClause($month, $year);
        $this->db->query("SELECT COUNT(d.id) as count FROM documents d " . $whereClause);
        $this->bindDateValues($month, $year);
        return $this->db->single()->count ?? 0;
    }

    public function getCompletedDocuments($month, $year){
        $whereClause = $this->buildDateWhereClause($month, $year);
        $this->db->query("SELECT COUNT(d.id) as count FROM documents d " . $whereClause . " AND d.status = 'completed'");
        $this->bindDateValues($month, $year);
        return $this->db->single()->count ?? 0;
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
    
    // (ฟังก์ชันสำหรับกราฟ)
    public function getDailyDocumentCount($month, $year){
        if ($month == 'all') return []; // กราฟรายวันจะแสดงเมื่อเลือกเดือนเท่านั้น
        $this->db->query("SELECT DAY(registration_date) as day, COUNT(id) as count 
                               FROM documents 
                               WHERE YEAR(registration_date) = :year AND MONTH(registration_date) = :month 
                               GROUP BY DAY(registration_date) 
                               ORDER BY day ASC");
        $this->db->bind(':year', $year);
        $this->db->bind(':month', $month);
        return $this->db->resultSet();
    }
}
?>