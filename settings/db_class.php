<?php
require_once('db_cred.php');

if (!class_exists('db_connection')) {
    class db_connection {
        protected $db;

        public function __construct() {
            $this->db = mysqli_connect(SERVER, USERNAME, PASSWD, DATABASE);
            if (mysqli_connect_errno()) {
                die("Failed to connect to MySQL: " . mysqli_connect_error());
            }
        }

        public function db_query($sql) {
            $result = $this->db->query($sql);
            if (!$result) {
                error_log("SQL Error: " . $this->db->error);
            }
            return $result;
        }

        public function db_fetch_all($sql) {
            $result = $this->db_query($sql);
            return $result ? $result->fetch_all(MYSQLI_ASSOC) : false;
        }

        public function db_fetch_one($sql) {
            $result = $this->db_query($sql);
            return $result ? $result->fetch_assoc() : false;
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