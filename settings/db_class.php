<?php
require_once(__DIR__ . '/db_cred.php');

if (!class_exists('db_connection')) {
    class db_connection {
        protected $db;

        public function __construct() {
            $server = SERVER;
            $username = USERNAME;
            $password = PASSWD;
            $db = DATABASE;
            
            $this->db = mysqli_connect($server, $username, $password, $db);
            if (mysqli_connect_errno()) {
                $error = "Failed to connect to MySQL: " . mysqli_connect_error();
                error_log($error);
                die($error);
            }
        }

        public function db_query($sql, $params = []) {
            $stmt = $this->db->prepare($sql);
            if ($stmt === false) {
                error_log("SQL Prepare Error: " . $this->db->error);
                return false;
            }

            if (!empty($params)) {
                $types = str_repeat('s', count($params)); // Assuming all parameters are strings
                $stmt->bind_param($types, ...$params);
            }

            if (!$stmt->execute()) {
                error_log("SQL Execute Error: " . $stmt->error);
                return false;
            }

            // For INSERT, UPDATE, DELETE queries
            if (stripos($sql, 'INSERT') === 0 || stripos($sql, 'UPDATE') === 0 || stripos($sql, 'DELETE') === 0) {
                $affected_rows = $stmt->affected_rows;
                $stmt->close();
                return $affected_rows > 0;
            }

            // For SELECT queries
            return $stmt->get_result();
        }

        public function db_fetch_all($sql, $params = []) {
            $result = $this->db_query($sql, $params);
            return $result !== false ? $result->fetch_all(MYSQLI_ASSOC) : false;
        }
        
        public function db_fetch_one($sql, $params = []) {
            $result = $this->db_query($sql, $params);
            return $result !== false ? $result->fetch_assoc() : false;
        }

        public function db_count() {
            return $this->db->affected_rows;
        }

        public function real_escape_string($value) {
            return $this->db->real_escape_string($value);
        }
    }
}
?>