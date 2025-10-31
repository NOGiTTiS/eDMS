<?php
class SettingModel extends Model {
    public function __construct(){
        parent::__construct();
    }

    // ดึงการตั้งค่าทั้งหมดมาเป็น array
    public function getAllSettings(){
        $this->db->query("SELECT * FROM settings");
        $results = $this->db->resultSet();
        
        // แปลง array of objects ให้เป็น associative array (key => value)
        $settingsArray = [];
        foreach($results as $row){
            $settingsArray[$row->setting_key] = $row->setting_value;
        }
        return $settingsArray;
    }

    // อัปเดตการตั้งค่าทีละหลายๆ ค่า
    public function updateSettings($settings){
        $this->db->query("INSERT INTO settings (setting_key, setting_value) VALUES (:key, :value) ON DUPLICATE KEY UPDATE setting_value = :value");

        foreach($settings as $key => $value){
            $this->db->bind(':key', $key);
            $this->db->bind(':value', $value);
            $this->db->execute();
        }
        return true;
    }
}
?>