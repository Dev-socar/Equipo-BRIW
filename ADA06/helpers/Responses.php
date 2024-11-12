<?php 

function response($type, $msg){

	switch ($type) {
		case 'success':
			
			$response = array(
				"status" => "success",
				"msg" => $msg
			);
			header('Content-type: application/json');
			echo json_encode($response);
			die();

			break;
		
		case 'error':
			
			$response = array(
				"status" => "error",
				"msg" => $msg
			);
			header('Content-type: application/json');
			echo json_encode($response);
			die();

			break;

		default:
			
			break;
	}

}

?>