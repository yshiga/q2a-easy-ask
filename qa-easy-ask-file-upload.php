<?php

require_once QA_INCLUDE_DIR.'app/posts.php';

class easy_ask_file_upload {
	const SECURITY_SALT = 'secure_random_string';
	
	function match_request($request)
	{
		return ($request == 'easy-ask-file-upload');
	}
	
	function process_request($request)
	{
		$response = array();
		$files = array();
		$errormessage = '';
		$url = '';
		$format = '';
		$filename = '';
		$filesize = '';

		try {
			if(is_array($_FILES) && count($_FILES)) {
				$filename = $_FILES['file']['name'];
				$filetype = $_FILES['file']['type'];
				$filetmp = $_FILES['file']['tmp_name'];
				
				require_once QA_INCLUDE_DIR.'qa-app-upload.php';
				$img_maxwidth = qa_opt('medium_editor_upload_maximgwidth');
				if($filetype === 'image/gif') {
					$fileTmpLoc = $_FILES['files']['tmp_name'][0];
					if(gif_is_animated($fileTmpLoc)) {
						$img_maxwidth = null;
					}
				}
				$upload_max_size = qa_opt('medium_editor_upload_max_size');
				$upload = qa_upload_file(
					$filetmp,
					$filename,
					$upload_max_size,
					false,
					qa_opt('medium_editor_upload_images') ?
					$img_maxwidth : null,
					null
				);

				$errormessage = isset($upload['error']) ? $upload['error'] : '';
				$url = isset($upload['bloburl']) ? $upload['bloburl'] : '';
				$format = isset($upload['format']) ? $upload['format'] : '';
			}
			if(!empty($errormessage)) {
				$files[] = array(
					'name' => 'error',
					'error' => $errormessage
				);
			} else {
				$files[] = array(
					'url' => $url,
					'name' => $filename,
					'type' => $filetype
				);
			}
		} catch (Exception $e) {
			error_log($e->getMessage());
			$files[] = array(
				'name' => 'error',
				'error' => $e->getMessage() 
			);
		}
		$response['files'] = $files;
		if (isset($_SERVER['HTTP_ACCEPT']) &&
			(strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
			header('Content-type: application/json');
		} else {
			header('Content-type: text/plain');
		}
		echo json_encode($response);
	}
	
}