<?php
require_once(dirname(__FILE__).'/../settings/db_class.php');

class raffle_class extends db_connection {
    public function __construct() {
        parent::__construct();
    }
    public function add_raffle_entry($name, $phone, $instagram) {
        try {
            // Remove @ from Instagram handle if present
            $instagram = ltrim($instagram, '@');
            
            // Prepare statement
            $stmt = $this->db->prepare("INSERT INTO raffle_entries (name, phone, instagram) VALUES (?, ?, ?)");
            if (!$stmt) {
                error_log("Prepare failed: " . $this->db->error);
                return false;
            }
            
            $stmt->bind_param("sss", $name, $phone, $instagram);
            $result = $stmt->execute();
            
            if (!$result) {
                error_log("Execute failed: " . $stmt->error);
                return false;
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Exception in add_raffle_entry: " . $e->getMessage());
            return false;
        }
    }
    
    public function get_all_entries() {
        try {
            $stmt = $this->db->prepare("SELECT * FROM raffle_entries ORDER BY created_at DESC");
            if (!$stmt) {
                error_log("Prepare failed: " . $this->db->error);
                return false;
            }
            
            if (!$stmt->execute()) {
                error_log("Execute failed: " . $stmt->error);
                return false;
            }
            
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            error_log("Exception in get_all_entries: " . $e->getMessage());
            return false;
        }
    }
    
    public function check_duplicate_entry($phone) {
        try {
            $stmt = $this->db->prepare("SELECT entry_id FROM raffle_entries WHERE phone = ?");
            if (!$stmt) {
                error_log("Prepare failed: " . $this->db->error);
                return true; // Return true to prevent entry on error
            }
            
            $stmt->bind_param("s", $phone);
            if (!$stmt->execute()) {
                error_log("Execute failed: " . $stmt->error);
                return true; // Return true to prevent entry on error
            }
            
            $result = $stmt->get_result();
            return $result->num_rows > 0;
        } catch (Exception $e) {
            error_log("Exception in check_duplicate_entry: " . $e->getMessage());
            return true; // Return true to prevent entry on error
        }
    }
    
    public function delete_entry($entry_id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM raffle_entries WHERE entry_id = ?");
            if (!$stmt) {
                error_log("Prepare failed: " . $this->db->error);
                return false;
            }
            
            $stmt->bind_param("i", $entry_id);
            if (!$stmt->execute()) {
                error_log("Execute failed: " . $stmt->error);
                return false;
            }
            
            return $stmt->affected_rows > 0;
        } catch (Exception $e) {
            error_log("Exception in delete_entry: " . $e->getMessage());
            return false;
        }
    }
    
    public function get_recent_entries($limit = 10) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM raffle_entries ORDER BY created_at DESC LIMIT ?");
            if (!$stmt) {
                error_log("Prepare failed: " . $this->db->error);
                return false;
            }
            
            $stmt->bind_param("i", $limit);
            if (!$stmt->execute()) {
                error_log("Execute failed: " . $stmt->error);
                return false;
            }
            
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            error_log("Exception in get_recent_entries: " . $e->getMessage());
            return false;
        }
    }
    
    public function get_entry_count() {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM raffle_entries");
            if (!$stmt) {
                error_log("Prepare failed: " . $this->db->error);
                return 0;
            }
            
            if (!$stmt->execute()) {
                error_log("Execute failed: " . $stmt->error);
                return 0;
            }
            
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            return $row ? $row['total'] : 0;
        } catch (Exception $e) {
            error_log("Exception in get_entry_count: " . $e->getMessage());
            return 0;
        }
    }
}
?>