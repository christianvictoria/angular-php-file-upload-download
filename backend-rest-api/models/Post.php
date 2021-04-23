<?php
	class Post {
		private $pdo;
		private $sql;
		private $data = array();
		private $staus = array();
		public function __construct(\PDO $pdo) {
			$this->pdo = $pdo;
		}

		public function select($table, $filter_data) {
			$sql = "SELECT * FROM $table ";

			if ($filter_data != null) {
				$sql .= "WHERE $filter_data";
			}
			$data = array(); $errmsg = ""; $code = 0;
			try {
				if ($res = $this->pdo->query($sql)->fetchAll()) {
					foreach ($res as $rec) {
						array_push($data, $rec);
						$res = null; $code = 200;
					}
				}
			} catch(\PDOException $e) {
				$errmsg = $e->getMessage(); $code = 401;
			}
			return $this->sendPayload($data, "success", $errmsg, $code);
		}

		public function upload_file($table, $data) {
			$fields = []; $values = [];
			foreach ($data as $key => $value) {
				array_push($fields, $key);
				array_push($values, $value);
			}
			try {
				$ctr = 0;
				$sqlstr = "INSERT INTO $table (";
				foreach ($fields as $field) {
					$sqlstr .= $field;
					$ctr++;
					if($ctr < count($fields)) {
						$sqlstr .= ", ";
					}
				}
				$sqlstr .= ") VALUES (".str_repeat("?, ", count($values)-1)."?)";
				$sql = $this->pdo->prepare($sqlstr);
				$sql->execute($values);
				return $this->select($table, null);
				return array("code" => 200, "errmsg" => "");
			} catch(\PDOException $e) {
				$errmsg = $e->getMessage(); $code = 403;
			}
			return array("code" => $code, "errmsg" => $errmsg);
		}

		public function remove_file($table, $data, $conditionString) {
	        $fields = []; $values = []; $setStr = ""; $stored_path = ""; $file_path = "";

			$this->sql = "SELECT * FROM $table WHERE $conditionString";

			if($result = $this->pdo->prepare($this->sql)) {
				$result->execute();
				if ($result->rowCount()>0) {
					$res = $result->fetchAll();
					foreach ($res as $value) {
						if($value['fld_path']) {
							$stored_path = explode('/', rtrim($value['fld_path'], '/'));
							$file_path = $stored_path[6];
						}
					}
					if ($file_path != "" && file_exists("uploads/$file_path")) {
	                 	unlink("uploads/$file_path");

						foreach ($data as $key => $value) {
							array_push($fields, $key);
							array_push($values, $value);
						}

						try {
							$ctr = 0;
							$sqlstr = "UPDATE $table SET ";
							foreach ($data as $key => $value) {
								$sqlstr .= "$key=?"; 
								$ctr++;
								if ($ctr < count($fields)) {
									$sqlstr .= ", ";
								}
							}
							$sqlstr .= " WHERE $conditionString";

							$sql = $this->pdo->prepare($sqlstr);
							$sql->execute($values);
							return $this->select($table, null);
						} catch(\PDOException $e) {
							$errmsg = $e->getMessage(); $code = 403;
						}
						return array("code" => $code, "errmsg" => $errmsg);
                   } 
				}
			}
		}

		public function select_path($table, $conditionString) {
			$stored_path = ""; $file_path = "";

			$this->sql = "SELECT * FROM $table WHERE $conditionString";

			if($result = $this->pdo->prepare($this->sql)) {
				$result->execute();
				if ($result->rowCount()>0) {
					$res = $result->fetchAll();
					foreach ($res as $value) {
						if($value['fld_path']) {
							$stored_path = explode('/', rtrim($value['fld_path'], '/'));
							return $file_path = "uploads/$stored_path[6]";
						}
					}
				}
			}
		}
		
		public function sendPayload($payload, $remarks, $message, $code) {
			$status = array("remarks"=>$remarks, "message"=>$message);
			http_response_code($code);
			return array(
				"status"=>$status,
				"payload"=>$payload,
				"timestamp"=>date_create());
		} 
	}
?>