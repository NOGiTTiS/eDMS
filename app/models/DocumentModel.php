<?php
class DocumentModel extends Model {
    public function __construct(){
        parent::__construct();
    }

    // ฟังก์ชันสำหรับดึงเอกสารทั้งหมด (ปรับปรุงเล็กน้อย)
    public function getDocuments($searchTerm = null, $limit = 0, $offset = 0){
        $sql = "SELECT d.*, u.full_name, d.id as documentId, u.id as userId FROM documents d INNER JOIN users u ON d.created_by_id = u.id";
        if (!empty($searchTerm)) {
            $sql .= " WHERE d.doc_subject LIKE :searchTerm OR d.doc_registration_number LIKE :searchTerm OR d.doc_incoming_number LIKE :searchTerm";
        }
        $sql .= " ORDER BY d.registration_date DESC, d.id DESC";
        if ($limit > 0) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        $this->db->query($sql);
        if (!empty($searchTerm)) {
            $this->db->bind(':searchTerm', '%' . $searchTerm . '%');
        }
        if ($limit > 0) {
            $this->db->bind(':limit', $limit, PDO::PARAM_INT);
            $this->db->bind(':offset', $offset, PDO::PARAM_INT);
        }
        return $this->db->resultSet();
    }

    // ฟังก์ชันใหม่: สำหรับหา ID ล่าสุดเพื่อสร้างเลขทะเบียนรับ
    public function getLatestDocumentId(){
        $this->db->query("SELECT MAX(id) as max_id FROM documents");
        $row = $this->db->single();
        return $row ? $row->max_id : 0;
    }


    // ฟังก์ชันสำหรับเพิ่มเอกสารใหม่ (ปรับปรุงใหญ่)
    public function addDocument($data){
        $this->db->query('INSERT INTO documents (registration_date, doc_registration_number, doc_incoming_number, doc_date, doc_from, doc_to, doc_subject, remarks, status, created_by_id) VALUES (:registration_date, :doc_registration_number, :doc_incoming_number, :doc_date, :doc_from, :doc_to, :doc_subject, :remarks, :status, :created_by_id)');
        
        // Bind values
        $this->db->bind(':registration_date', $data['registration_date']);
        $this->db->bind(':doc_registration_number', $data['doc_registration_number']);
        $this->db->bind(':doc_incoming_number', $data['doc_incoming_number']);
        $this->db->bind(':doc_date', $data['doc_date']);
        $this->db->bind(':doc_from', $data['doc_from']);
        $this->db->bind(':doc_to', $data['doc_to']);
        $this->db->bind(':doc_subject', $data['doc_subject']);
        $this->db->bind(':remarks', $data['remarks']);
        $this->db->bind(':status', 'received');
        $this->db->bind(':created_by_id', $data['user_id']);

        if($this->db->execute()){
            $documentId = $this->db->lastInsertId();

            // เมื่อบันทึกสำเร็จ ให้เพิ่มค่าตัวนับ
            $this->incrementSettingValue('doc_registration_counter');

            if(!empty($data['file_name'])){
                $this->db->query('INSERT INTO document_files (document_id, file_name, original_file_name, file_path) VALUES (:document_id, :file_name, :original_file_name, :file_path)');
                $this->db->bind(':document_id', $documentId);
                $this->db->bind(':file_name', $data['file_name']);
                $this->db->bind(':original_file_name', $data['original_file_name']);
                $this->db->bind(':file_path', $data['file_path']);
                $this->db->execute();
            }

            $this->db->query('INSERT INTO document_flow (document_id, action_by_id, action, comment) VALUES (:document_id, :action_by_id, :action, :comment)');
            $this->db->bind(':document_id', $documentId);
            $this->db->bind(':action_by_id', $data['user_id']);
            $this->db->bind(':action', 'receive');
            $this->db->bind(':comment', 'ธุรการกลางลงรับหนังสือ');
            
            if($this->db->execute()){
                return $documentId;
            }
        }
        
        return false;
    }

    // ฟังก์ชันสำหรับดึงข้อมูลเอกสาร 1 ฉบับด้วย ID
    public function getDocumentById($id){
        $this->db->query("SELECT * FROM documents WHERE id = :id");
        $this->db->bind(':id', $id);
        $row = $this->db->single();
        return $row;
    }

    // ฟังก์ชันสำหรับดึงไฟล์แนบทั้งหมดของเอกสาร
    public function getDocumentFiles($id){
        $this->db->query("SELECT * FROM document_files WHERE document_id = :id");
        $this->db->bind(':id', $id);
        $results = $this->db->resultSet();
        return $results;
    }

    // ฟังก์ชันสำหรับดึงประวัติการเดินทางของเอกสาร
    public function getDocumentFlow($id){
        $this->db->query("SELECT df.*, u.full_name 
                                FROM document_flow as df
                                JOIN users as u ON df.action_by_id = u.id
                                WHERE df.document_id = :id 
                                ORDER BY df.created_at ASC");
        $this->db->bind(':id', $id);
        $results = $this->db->resultSet();
        return $results;
    }

    // ฟังก์ชันสำหรับส่งต่อเอกสารและอัปเดตสถานะ
    public function forwardDocument($data){
        // 1. เพิ่มรายการใหม่ใน document_flow
        $this->db->query('INSERT INTO document_flow (document_id, action_by_id, action, comment, forward_to_id) VALUES (:document_id, :action_by_id, :action, :comment, :forward_to_id)');
        $this->db->bind(':document_id', $data['document_id']);
        $this->db->bind(':action_by_id', $data['user_id']);
        $this->db->bind(':action', 'forward_to_director');
        $this->db->bind(':comment', $data['comment']);
        $this->db->bind(':forward_to_id', $data['forward_to_id']);
        
        if(!$this->db->execute()){
            return false;
        }

        // 2. อัปเดตสถานะของเอกสารในตาราง documents
        $this->db->query('UPDATE documents SET status = :status WHERE id = :id');
        $this->db->bind(':status', 'pending_director'); // สถานะใหม่
        $this->db->bind(':id', $data['document_id']);

        if($this->db->execute()){
            return true;
        } else {
            return false;
        }
    }

    // ฟังก์ชันใหม่: ดึงเอกสารที่ส่งมาถึงผู้ใช้คนนั้นๆ (สำหรับ Inbox)
    public function getDocumentsForUser($userId, $searchTerm = null, $limit = 0, $offset = 0){
        $sql = "SELECT d.*, u.full_name, d.id as documentId, u.id as userId FROM documents d JOIN users u ON d.created_by_id = u.id WHERE d.status = 'pending_director' AND d.id IN (SELECT document_id FROM document_flow WHERE forward_to_id = :user_id AND id = (SELECT MAX(id) FROM document_flow WHERE document_id = d.id))";
        if (!empty($searchTerm)) {
            $sql .= " AND (d.doc_subject LIKE :searchTerm OR d.doc_registration_number LIKE :searchTerm OR d.doc_incoming_number LIKE :searchTerm)";
        }
        $sql .= " ORDER BY d.registration_date DESC";
        if ($limit > 0) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        $this->db->query($sql);
        $this->db->bind(':user_id', $userId);
        if (!empty($searchTerm)) {
            $this->db->bind(':searchTerm', '%' . $searchTerm . '%');
        }
        if ($limit > 0) {
            $this->db->bind(':limit', $limit, PDO::PARAM_INT);
            $this->db->bind(':offset', $offset, PDO::PARAM_INT);
        }
        return $this->db->resultSet();
    }

    // ฟังก์ชันใหม่: สำหรับการอนุมัติโดย ผอ.
    public function approveDocumentByDirector($data){
        // 1. เพิ่มรายการใหม่ใน document_flow พร้อมลายเซ็น
        $this->db->query('INSERT INTO document_flow (document_id, action_by_id, action, comment, signature_path) VALUES (:document_id, :action_by_id, :action, :comment, :signature_path)');
        $this->db->bind(':document_id', $data['document_id']);
        $this->db->bind(':action_by_id', $data['user_id']);
        $this->db->bind(':action', 'approve_by_director');
        $this->db->bind(':comment', $data['comment']);
        $this->db->bind(':signature_path', $data['signature_path']);
        
        if(!$this->db->execute()){
            return false;
        }

        // 2. อัปเดตสถานะของเอกสารในตาราง documents
        $this->db->query('UPDATE documents SET status = :status WHERE id = :id');
        $this->db->bind(':status', 'approved_director'); // สถานะใหม่
        $this->db->bind(':id', $data['document_id']);

        return $this->db->execute();
    }

    // ฟังก์ชันใหม่: สำหรับการส่งต่อไปยังธุรการฝ่าย
    public function forwardToDeptAdmin($data){
        // 1. เพิ่มรายการใหม่ใน document_flow
        $this->db->query('INSERT INTO document_flow (document_id, action_by_id, action, comment, forward_to_id) VALUES (:document_id, :action_by_id, :action, :comment, :forward_to_id)');
        $this->db->bind(':document_id', $data['document_id']);
        $this->db->bind(':action_by_id', $data['user_id']);
        $this->db->bind(':action', 'forward_to_dept');
        $this->db->bind(':comment', $data['comment']);
        $this->db->bind(':forward_to_id', $data['forward_to_id']);
        
        if(!$this->db->execute()){
            return false;
        }

        // 2. อัปเดตสถานะของเอกสาร
        $this->db->query('UPDATE documents SET status = :status WHERE id = :id');
        $this->db->bind(':status', 'forwarded_to_dept'); // สถานะใหม่
        $this->db->bind(':id', $data['document_id']);

        return $this->db->execute();
    }

    public function getDepartments(){
        $this->db->query("SELECT * FROM departments ORDER BY name ASC");
        return $this->db->resultSet();
    }

    // ฟังก์ชันใหม่: ดึงเอกสารที่ส่งมาถึงฝ่าย (สำหรับ Inbox ของ dept_admin)
    public function getDocumentsForDeptAdmin($userId, $searchTerm = null, $limit = 0, $offset = 0){
        $this->db->query("SELECT department_id FROM users WHERE id = :user_id");
        $this->db->bind(':user_id', $userId);
        $userDept = $this->db->single();
        if(!$userDept || is_null($userDept->department_id)) return [];
        $departmentId = $userDept->department_id;

        $sql = "SELECT d.*, u.full_name, d.id as documentId, u.id as userId FROM documents d JOIN users u ON d.created_by_id = u.id WHERE EXISTS (SELECT 1 FROM document_flow df JOIN users fu ON df.forward_to_id = fu.id WHERE df.document_id = d.id AND fu.department_id = :department_id) AND d.status IN ('forwarded_to_dept', 'pending_deputy_approval', 'pending_dept_admin_action', 'pending_section_head_action')";
        if (!empty($searchTerm)) {
            $sql .= " AND (d.doc_subject LIKE :searchTerm OR d.doc_registration_number LIKE :searchTerm OR d.doc_incoming_number LIKE :searchTerm)";
        }
        $sql .= " ORDER BY d.registration_date DESC";
        if ($limit > 0) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        $this->db->query($sql);
        $this->db->bind(':department_id', $departmentId);
        if (!empty($searchTerm)) {
            $this->db->bind(':searchTerm', '%' . $searchTerm . '%');
        }
        if ($limit > 0) {
            $this->db->bind(':limit', $limit, PDO::PARAM_INT);
            $this->db->bind(':offset', $offset, PDO::PARAM_INT);
        }
        return $this->db->resultSet();
    }

    // ฟังก์ชันใหม่: สำหรับการส่งต่อไปยัง รอง ผอ. ฝ่าย
    public function forwardToDeputyDirector($data){
        // 1. เพิ่มรายการใหม่ใน document_flow
        $this->db->query('INSERT INTO document_flow (document_id, action_by_id, action, comment, forward_to_id) VALUES (:document_id, :action_by_id, :action, :comment, :forward_to_id)');
        $this->db->bind(':document_id', $data['document_id']);
        $this->db->bind(':action_by_id', $data['user_id']);
        $this->db->bind(':action', 'forward_to_deputy');
        $this->db->bind(':comment', $data['comment']);
        $this->db->bind(':forward_to_id', $data['forward_to_id']);
        
        if(!$this->db->execute()){
            return false;
        }

        // 2. อัปเดตสถานะของเอกสาร
        $this->db->query('UPDATE documents SET status = :status WHERE id = :id');
        $this->db->bind(':status', 'pending_deputy_approval'); // สถานะใหม่
        $this->db->bind(':id', $data['document_id']);

        return $this->db->execute();
    }

    // ฟังก์ชันใหม่: ดึงเอกสารที่ส่งมาถึง รอง ผอ. ฝ่าย
    public function getDocumentsForDeputy($userId, $searchTerm = null, $limit = 0, $offset = 0){
        $sql = "SELECT d.*, u.full_name, d.id as documentId, u.id as userId FROM documents d JOIN users u ON d.created_by_id = u.id WHERE d.status = 'pending_deputy_approval' AND d.id IN (SELECT document_id FROM document_flow WHERE forward_to_id = :user_id AND id = (SELECT MAX(id) FROM document_flow WHERE document_id = d.id))";
        if (!empty($searchTerm)) {
            $sql .= " AND (d.doc_subject LIKE :searchTerm OR d.doc_registration_number LIKE :searchTerm OR d.doc_incoming_number LIKE :searchTerm)";
        }
        $sql .= " ORDER BY d.registration_date DESC";
        if ($limit > 0) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        $this->db->query($sql);
        $this->db->bind(':user_id', $userId);
        if (!empty($searchTerm)) {
            $this->db->bind(':searchTerm', '%' . $searchTerm . '%');
        }
        if ($limit > 0) {
            $this->db->bind(':limit', $limit, PDO::PARAM_INT);
            $this->db->bind(':offset', $offset, PDO::PARAM_INT);
        }
        return $this->db->resultSet();
    }

    // แก้ไขจาก approveAndForwardByDeputy
    public function approveByDeputy($data){
        // 1. เพิ่มรายการใหม่ใน document_flow พร้อมลายเซ็น (ไม่มี forward_to_id)
        $this->db->query('INSERT INTO document_flow (document_id, action_by_id, action, comment, signature_path) VALUES (:document_id, :action_by_id, :action, :comment, :signature_path)');
        $this->db->bind(':document_id', $data['document_id']);
        $this->db->bind(':action_by_id', $data['user_id']);
        $this->db->bind(':action', 'approve_by_deputy');
        $this->db->bind(':comment', $data['comment']);
        $this->db->bind(':signature_path', $data['signature_path']);
        
        if(!$this->db->execute()){
            return false;
        }

        // 2. อัปเดตสถานะของเอกสาร (สถานะใหม่)
        $this->db->query('UPDATE documents SET status = :status WHERE id = :id');
        $this->db->bind(':status', 'pending_dept_admin_action'); // สถานะ: รอธุรการฝ่ายดำเนินการ
        $this->db->bind(':id', $data['document_id']);

        return $this->db->execute();
    }

    public function forwardToSectionHead($data){
        // 1. เพิ่ม Flow การส่งต่อ
        $this->db->query('INSERT INTO document_flow (document_id, action_by_id, action, comment, forward_to_id) VALUES (:document_id, :action_by_id, :action, :comment, :forward_to_id)');
        $this->db->bind(':document_id', $data['document_id']);
        $this->db->bind(':action_by_id', $data['user_id']);
        $this->db->bind(':action', 'forward_to_section_head');
        $this->db->bind(':comment', $data['comment']);
        $this->db->bind(':forward_to_id', $data['forward_to_id']);
        if(!$this->db->execute()) return false;

        // 2. อัปเดตสถานะเอกสาร
        $this->db->query('UPDATE documents SET status = :status WHERE id = :id');
        $this->db->bind(':status', 'pending_section_head_action');
        $this->db->bind(':id', $data['document_id']);
        return $this->db->execute();
    }

    public function completeDocument($data){
        // 1. เพิ่ม Flow ปิดงาน
        $this->db->query('INSERT INTO document_flow (document_id, action_by_id, action, comment) VALUES (:document_id, :action_by_id, :action, :comment)');
        $this->db->bind(':document_id', $data['document_id']);
        $this->db->bind(':action_by_id', $data['user_id']);
        $this->db->bind(':action', 'complete_and_export');
        $this->db->bind(':comment', $data['comment']);
        if(!$this->db->execute()) return false;

        // 2. อัปเดตสถานะเอกสารเป็น "เสร็จสิ้น"
        $this->db->query('UPDATE documents SET status = :status WHERE id = :id');
        $this->db->bind(':status', 'completed');
        $this->db->bind(':id', $data['document_id']);
        return $this->db->execute();
    }

    // ฟังก์ชันใหม่: ดึงเอกสารที่ส่งมาถึงหัวหน้างาน
    public function getDocumentsForSectionHead($userId, $searchTerm = null, $limit = 0, $offset = 0){
        $sql = "SELECT d.*, u.full_name, d.id as documentId, u.id as userId FROM documents d JOIN users u ON d.created_by_id = u.id WHERE d.status = 'pending_section_head_action' AND d.id IN (SELECT document_id FROM document_flow WHERE forward_to_id = :user_id AND id = (SELECT MAX(id) FROM document_flow WHERE document_id = d.id))";
        if (!empty($searchTerm)) {
            $sql .= " AND (d.doc_subject LIKE :searchTerm OR d.doc_registration_number LIKE :searchTerm OR d.doc_incoming_number LIKE :searchTerm)";
        }
        $sql .= " ORDER BY d.registration_date DESC";
        if ($limit > 0) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        $this->db->query($sql);
        $this->db->bind(':user_id', $userId);
        if (!empty($searchTerm)) {
            $this->db->bind(':searchTerm', '%' . $searchTerm . '%');
        }
        if ($limit > 0) {
            $this->db->bind(':limit', $limit, PDO::PARAM_INT);
            $this->db->bind(':offset', $offset, PDO::PARAM_INT);
        }
        return $this->db->resultSet();
    }

    public function acknowledgeBySectionHead($data){
        // 1. เพิ่ม Flow การรับทราบ
        $this->db->query('INSERT INTO document_flow (document_id, action_by_id, action, comment) VALUES (:document_id, :action_by_id, :action, :comment)');
        $this->db->bind(':document_id', $data['document_id']);
        $this->db->bind(':action_by_id', $data['user_id']);
        $this->db->bind(':action', 'acknowledged_by_section_head');
        $this->db->bind(':comment', $data['comment']);
        if(!$this->db->execute()) return false;

        // 2. อัปเดตสถานะเอกสารเป็น "เสร็จสิ้น"
        $this->db->query('UPDATE documents SET status = :status WHERE id = :id');
        $this->db->bind(':status', 'completed');
        $this->db->bind(':id', $data['document_id']);
        return $this->db->execute();
    }

    public function findUserWhoForwardedTo($documentId, $recipientId) {
        $this->db->query("SELECT action_by_id FROM document_flow 
                               WHERE document_id = :document_id AND forward_to_id = :recipient_id 
                               ORDER BY created_at DESC LIMIT 1");
        $this->db->bind(':document_id', $documentId);
        $this->db->bind(':recipient_id', $recipientId);
        $row = $this->db->single();
        return $row ? $row->action_by_id : null;
    }

    // ฟังก์ชันสำหรับนับจำนวนเอกสารทั้งหมด (สำหรับ central_admin)
    public function countAllDocuments($searchTerm = null){
        $sql = "SELECT COUNT(d.id) as count FROM documents d WHERE 1";
        if (!empty($searchTerm)) {
            $sql .= " AND (d.doc_subject LIKE :searchTerm OR d.doc_registration_number LIKE :searchTerm OR d.doc_incoming_number LIKE :searchTerm)";
        }
        $this->db->query($sql);
        if (!empty($searchTerm)) {
            $this->db->bind(':searchTerm', '%' . $searchTerm . '%');
        }
        $row = $this->db->single();
        return $row ? $row->count : 0;
    }

    // ฟังก์ชันนับสำหรับ Director
    public function countDocumentsForUser($userId, $searchTerm = null){
        $sql = "SELECT COUNT(d.id) as count FROM documents d WHERE d.status = 'pending_director' AND d.id IN (SELECT document_id FROM document_flow WHERE forward_to_id = :user_id AND id = (SELECT MAX(id) FROM document_flow WHERE document_id = d.id))";
        if (!empty($searchTerm)) {
            $sql .= " AND (d.doc_subject LIKE :searchTerm OR d.doc_registration_number LIKE :searchTerm OR d.doc_incoming_number LIKE :searchTerm)";
        }
        $this->db->query($sql);
        $this->db->bind(':user_id', $userId);
        if (!empty($searchTerm)) {
            $this->db->bind(':searchTerm', '%' . $searchTerm . '%');
        }
        $row = $this->db->single();
        return $row ? $row->count : 0;
    }

    public function countDocumentsForDeptAdmin($userId, $searchTerm = null){
        $this->db->query("SELECT department_id FROM users WHERE id = :user_id");
        $this->db->bind(':user_id', $userId);
        $userDept = $this->db->single();
        if(!$userDept || is_null($userDept->department_id)) return 0;
        
        $sql = "SELECT COUNT(d.id) as count FROM documents d WHERE EXISTS (SELECT 1 FROM document_flow df JOIN users fu ON df.forward_to_id = fu.id WHERE df.document_id = d.id AND fu.department_id = :department_id) AND d.status IN ('forwarded_to_dept', 'pending_deputy_approval', 'pending_dept_admin_action', 'pending_section_head_action')";
        if (!empty($searchTerm)) {
            $sql .= " AND (d.doc_subject LIKE :searchTerm OR d.doc_registration_number LIKE :searchTerm OR d.doc_incoming_number LIKE :searchTerm)";
        }
        $this->db->query($sql);
        $this->db->bind(':department_id', $userDept->department_id);
        if (!empty($searchTerm)) {
            $this->db->bind(':searchTerm', '%' . $searchTerm . '%');
        }
        $row = $this->db->single();
        return $row ? $row->count : 0;
    }

    public function countDocumentsForDeputy($userId, $searchTerm = null){
        $sql = "SELECT COUNT(d.id) as count FROM documents d WHERE d.status = 'pending_deputy_approval' AND d.id IN (SELECT document_id FROM document_flow WHERE forward_to_id = :user_id AND id = (SELECT MAX(id) FROM document_flow WHERE document_id = d.id))";
        if (!empty($searchTerm)) {
            $sql .= " AND (d.doc_subject LIKE :searchTerm OR d.doc_registration_number LIKE :searchTerm OR d.doc_incoming_number LIKE :searchTerm)";
        }
        $this->db->query($sql);
        $this->db->bind(':user_id', $userId);
        if (!empty($searchTerm)) {
            $this->db->bind(':searchTerm', '%' . $searchTerm . '%');
        }
        $row = $this->db->single();
        return $row ? $row->count : 0;
    }

    public function countDocumentsForSectionHead($userId, $searchTerm = null){
        $sql = "SELECT COUNT(d.id) as count FROM documents d WHERE d.status = 'pending_section_head_action' AND d.id IN (SELECT document_id FROM document_flow WHERE forward_to_id = :user_id AND id = (SELECT MAX(id) FROM document_flow WHERE document_id = d.id))";
        if (!empty($searchTerm)) {
            $sql .= " AND (d.doc_subject LIKE :searchTerm OR d.doc_registration_number LIKE :searchTerm OR d.doc_incoming_number LIKE :searchTerm)";
        }
        $this->db->query($sql);
        $this->db->bind(':user_id', $userId);
        if (!empty($searchTerm)) {
            $this->db->bind(':searchTerm', '%' . $searchTerm . '%');
        }
        $row = $this->db->single();
        return $row ? $row->count : 0;
    }

    // ฟังก์ชันใหม่: สำหรับอัปเดตข้อมูลเอกสาร
    public function updateDocument($data){
        $this->db->query('UPDATE documents SET 
                                registration_date = :registration_date,
                                doc_registration_number = :doc_registration_number,
                                doc_incoming_number = :doc_incoming_number,
                                doc_date = :doc_date,
                                doc_from = :doc_from,
                                doc_to = :doc_to,
                                doc_subject = :doc_subject,
                                remarks = :remarks
                            WHERE id = :id');
        
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':registration_date', $data['registration_date']);
        $this->db->bind(':doc_registration_number', $data['doc_registration_number']);
        $this->db->bind(':doc_incoming_number', $data['doc_incoming_number']);
        $this->db->bind(':doc_date', $data['doc_date']);
        $this->db->bind(':doc_from', $data['doc_from']);
        $this->db->bind(':doc_to', $data['doc_to']);
        $this->db->bind(':doc_subject', $data['doc_subject']);
        $this->db->bind(':remarks', $data['remarks']);
        
        return $this->db->execute();
    }

    // ฟังก์ชันใหม่: สำหรับลบเอกสาร
    public function deleteDocument($id){
        $this->db->query('DELETE FROM documents WHERE id = :id');
        $this->db->bind(':id', $id);
        
        return $this->db->execute();
    }

    // ฟังก์ชันใหม่: ดึงค่าการตั้งค่าจาก Key
    public function getSettingValue($key){
        $this->db->query("SELECT setting_value FROM settings WHERE setting_key = :key");
        $this->db->bind(':key', $key);
        $row = $this->db->single();
        return $row ? $row->setting_value : null;
    }

    // ฟังก์ชันใหม่: เพิ่มค่าตัวนับขึ้น 1
    public function incrementSettingValue($key){
        $this->db->query("UPDATE settings SET setting_value = setting_value + 1 WHERE setting_key = :key");
        $this->db->bind(':key', $key);
        return $this->db->execute();
    }

    // ========== DEPARTMENT MANAGEMENT METHODS ==========

    public function getDepartmentById($id){
        $this->db->query("SELECT * FROM departments WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function addDepartment($name){
        $this->db->query("INSERT INTO departments (name) VALUES (:name)");
        $this->db->bind(':name', $name);
        return $this->db->execute();
    }

    public function updateDepartment($id, $name){
        $this->db->query("UPDATE departments SET name = :name WHERE id = :id");
        $this->db->bind(':id', $id);
        $this->db->bind(':name', $name);
        return $this->db->execute();
    }

    public function deleteDepartment($id){
        // ตรวจสอบก่อนว่ามีผู้ใช้ผูกกับฝ่ายนี้หรือไม่
        $this->db->query("SELECT COUNT(id) as user_count FROM users WHERE department_id = :id");
        $this->db->bind(':id', $id);
        $result = $this->db->single();

        if ($result && $result->user_count > 0) {
            return false; // ไม่สามารถลบได้ เพราะมีผู้ใช้สังกัดอยู่
        }

        // ถ้าไม่มีผู้ใช้สังกัดอยู่ ก็ทำการลบ
        $this->db->query("DELETE FROM departments WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
?>