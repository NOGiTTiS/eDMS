<?php
class LogModel extends Model {
    public function __construct(){
        parent::__construct();
    }

    public function getAllLogs(){
        $this->db->query("SELECT * FROM activity_logs ORDER BY created_at DESC");
        return $this->db->resultSet();
    }
}
?>