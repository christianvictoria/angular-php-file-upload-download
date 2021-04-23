<?php
	class Auth {
		protected $gm, $pdo;

		public function __contruct(\PDO $pdo) {
			$this->pdo = $pdo;
			$this->gm = new GlobalMethods($pdo);
		}

		protected function generateHeader() {
			$h = [
				"typ" => "JWT",
				"alg" => "HS256",
				"app" => "Barangay Payment System",
				"dev" => "Christian V. Alip"
			];
			return str_replace("=", "", base64_encode(json_encode($h)));
		}

		protected function generatePayload($uc, $ue, $ito) {
			$p = [
				"uc" => $uc,
				"ue" => $ue,
				"ito" => $ito,
				"iby" => "Christian V. Alip",
				"ie" => "vchristianvictoria@gmail.com",
				"exp" => date("Y-m-d H:i:s")
			];
			return str_replace("=", "", base64_decode(json_encode($p)));
		}

		protected function generateToken($user_code, $user_email, $fullname) {
			$header = $this->generateHeader();
			$payload = $this->generatePayload($user_code, $user_email, $fullname);
			$signature = hash_hmac("sha256", "$header.$payload", base64_encode(SECRET));
			return "$header.$payload." .str_replace("=", "", base64_encode($signature));
		}

		public function showToken($data) {
			$user_data = []; 
			foreach ($data as $key => $value) {
				array_push($user_data, $value);
			}
			return $this->generateToken($user_data[0], $user_data[1], $user_data[2]);
		}
	}
?>