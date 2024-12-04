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

        public function db_query($sql) {
            $result = $this->db->query($sql);
            if (!$result) {
                error_log("SQL Error: " . $this->db->error . " in query: " . substr($sql, 0, 500));
                return false;
            }
            return $result;
        }

        public function db_fetch_all($sql) {
            $result = $this->db_query($sql);
            return $result !== false ? $result->fetch_all(MYSQLI_ASSOC) : false;
        }
        
        public function db_fetch_one($sql) {
            $result = $this->db_query($sql);
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