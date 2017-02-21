<?php
class Document extends AppModel {
	/**
	 * Document origin
	 */
	const OSIRIS       = 'osiris';
	const PORTAL       = 'portal';

	var $name = 'Document';

	var $mime_types = array(
		"xlsx" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
		"xltx" => "application/vnd.openxmlformats-officedocument.spreadsheetml.template",
		"potx" => "application/vnd.openxmlformats-officedocument.presentationml.template",
		"ppsx" => "application/vnd.openxmlformats-officedocument.presentationml.slideshow",
		"pptx" => "application/vnd.openxmlformats-officedocument.presentationml.presentation",
		"sldx" => "application/vnd.openxmlformats-officedocument.presentationml.slide",
		"docx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
		"dotx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.template",
		"xlam" => "application/vnd.ms-excel.addin.macroEnabled.12",
		"xlsb" => "application/vnd.ms-excel.sheet.binary.macroEnabled.12"
	);

	var $actsAs = array(
		'UploadPack.Upload' => array(
			'document' => array(
				'rule' => array('attachmentPresence'),
				'message' => 'Attached document is required',
				'path' => ':app/downloads/:model/:id/:hash/:basename.:extension',
				'default_url' => '/download/:id/:hash//:model'
			)
		)
	);

	function beforeSave($options) {
		$pathinfo = UploadBehavior::_pathinfo($this->data[$this->alias]['document_file_name']);
		$hash = md5($pathinfo['filename'] . Configure::read('Security.salt'));

		// $previous = $this->find('count', array(
		// 	'conditions' => array('document_fingerprint' => $hash)
		// ));

		// if($previous != 0) {
		// 	return false;
		// }

		if(array_key_exists($pathinfo['extension'], $this->mime_types)) {
			$this->data[$this->alias]['document_content_type'] = $this->mime_types[$pathinfo['extension']];
		}

		$this->data[$this->alias]['origin'] = Document::OSIRIS;
		$this->data[$this->alias]['document_fingerprint'] = $hash;
		$this->data[$this->alias]['created_at'] = date("Y-m-d");
		$this->data[$this->alias]['updated_at'] = date("Y-m-d");

		return true;
	}

	function afterFind($data, $primary = false) {
		foreach ($data as $key => $val) {
			if (empty($val[$this->alias]['id'])) {
				continue;
			}
			$id = $val[$this->alias]['id'];
			$fingerprint = $val[$this->alias]['document_fingerprint'];
			$file_name = $val[$this->alias]['document_file_name'];

			$data[$key][$this->alias]['document_url'] = $this->composeDocumentUrl($id, $fingerprint, $file_name);
			$data[$key][$this->alias]['show_name'] = isset($val[$this->alias]['title']) && $val[$this->alias]['title'] != '' ? $val[$this->alias]['title'] : $val[$this->alias]['document_file_name'];
		}

		return $data;
	}

	function getDocumentFilePath($document) {
		return $path = ROOT . "/app/downloads/documents/" . $document['id'] . "/" . $document['document_fingerprint'] . "/" . $document['document_file_name'];
	}

	function composeDocumentUrl($id, $fingerprint, $filename, $cmx = false, $user_id = null) {
		$document_url = "";
		if($cmx) {
			$document_url = "http://" . Configure::read("CMX.server_name");
		}
		$document_url .= "/download/" . $id . "/" . $fingerprint . "/" . $user_id . "/" . Inflector::pluralize(low($this->name));

		return $document_url;
	}
}
?>