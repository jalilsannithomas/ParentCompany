<?php
if (!class_exists('db_connection')) {
    class db_connection {
        protected $db;

        public function __construct() {
            $url = parse_url(getenv("JAWSDB_URL"));
            $server = $url["host"];
            $username = $url["user"];
            $password = $url["pass"];
            $db = substr($url["path"], 1);

            $this->db = mysqli_connect($server, $username, $password, $db);
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