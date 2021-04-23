<?php
	require_once("./config/Config.php");

	$db = new Connection();
	$pdo = $db->connect();
	$gm = new GlobalMethods($pdo);
	$post = new Post($pdo);

	if (isset($_REQUEST['request'])) {
		$req = explode('/', rtrim(base64_decode($_REQUEST['request']), '/'));	
	} else {
		$req = array("errorcatcher");
	}

	switch($_SERVER['REQUEST_METHOD']) {
		case 'POST':
			switch ($req[0]) {
				case 'files':
					if (count($req) > 1) {
						echo json_encode($gm->select("tbl_$req[0]", "fld_isDeleted = $req[1]"), JSON_PRETTY_PRINT);
					} else {
						echo json_encode($gm->select("tbl_$req[0]", null), JSON_PRETTY_PRINT);
					}
					break;

				case 'save':
					$file = $_FILES['myFile']['name'];
					$extension = pathinfo($file, PATHINFO_EXTENSION);

					$new_date_time = date_create();
					$date_time_format = $new_date_time->format('Y-m-d H:i:s');
					$filename = str_replace(str_split('- :'), '', $date_time_format);
					$new_file_name = "$filename.$extension";

				    $destination = "uploads/" . $new_file_name;
					$file_tmp_name = $_FILES['myFile']['tmp_name'];	

					$d = [						
						'fld_title' => $_POST['title'],
						'fld_category' => $_POST['category'],
						'fld_amount' => $_POST['amount'],
						'fld_quantity' => $_POST['quantity'],
						'fld_path' => "http://localhost/angular-file-upload/barangay-payment-api/$destination"
					];
					if(move_uploaded_file($file_tmp_name, $destination)) echo json_encode($post->upload_file("tbl_files", $d), JSON_PRETTY_PRINT);
				break;

				case 'remove':
					$d = json_decode(base64_decode(file_get_contents("php://input")));
					echo json_encode($post->remove_file("tbl_files", $d, "fld_id=$req[1]"));
					break;

				case 'download':
					if (count($req) > 1) {
						echo file_get_contents($post->select_path("tbl_files", "fld_id = $req[1] AND fld_isDeleted = 0"));
					}
					break;

				default:
					http_response_code(403);
					echo "Invalid Route/Endpoint";
					break;
			}
			break;


		default:
			http_response_code(403);
			echo "Please contact the Systems Administrator";
			break;
	}
?>