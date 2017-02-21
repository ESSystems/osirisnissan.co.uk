<?php
/**
 * @author stv
 *
 * @property Document $Document
 */
class DocumentsController extends AppController
{
	var $name = 'Documents';

	var $uses = array('AttendanceFeedback', 'Document', 'Notification', 'Referrer');

	function direct_index() {
		$conditions = array(
			'Document.attachable_id' => $this->params['named']['id'],
			'Document.attachable_type' => $this->params['named']['type'],
		);

		$data = $this->Document->find('all',
			array(
				'contain' => array(),
				'conditions' => $conditions
			)
		);

		return array(
			'success' => true,
			'data' => $data
		);
	}

	function download() {
		// Configure::write('debug', 2);
		$document = $this->Document->find('first',
			array(
				'conditions' => array(
					'id' => $this->params['id'],
					'document_fingerprint' => $this->params['fingerprint']
				)
			)
		);

		if($document) {
			if($this->user() || $this->Referrer->isReferrer($this->params['referrer'])) {
				$path = "";
				if($document['Document']['origin'] == Document::PORTAL) {
					$current_user = $this->user();
					$path = $this->Document->composeDocumentUrl(
						$document['Document']['id'],
						$document['Document']['document_fingerprint'],
						$document['Document']['document_file_name'],
						true,
						$current_user['User']['id']);
				} else {
					$path = $this->Document->getDocumentFilePath($document['Document']);
				}
				$this->download_file($path, $document['Document']['document_file_name'], $document['Document']['document_content_type'], $document['Document']['document_file_size']);
			} else {
				$this->redirect('/');
			}
		} else {
			$this->redirect('/');
		}
	}

	function download_file($path, $file_name, $content_type, $size) {
		$buffer = '';
		$this->autoRender = false;

		if ($handle = fopen ($path, "rb")) {
			$agent = env('HTTP_USER_AGENT');

            if (preg_match('%Opera(/| )([0-9].[0-9]{1,2})%', $agent)) {
                header("Content-type: application/octetstream");
            } else if (preg_match('/MSIE ([0-9].[0-9]{1,2})/', $agent)) {
                header("Content-type: application/force-download");
                header("Content-type: application/octet-stream");
                header("Content-type: application/download");
            } else {
            	header("Content-type: application/octet-stream");
            }
		    header("Content-type: " . $content_type);
		    header("Content-Disposition: attachment; filename=\"".$file_name."\";");
		    header("Content-length: $size");
    		header('Expires: 0');
			header('Accept-Ranges: bytes');
			header('Cache-Control: private');
			// header('Pragma: private');

			@ob_end_clean();

		    while (!feof($handle)) {
                if (!(connection_status() == 0 && !connection_aborted())) {
                    fclose($handle);
                    return false;
                }
                set_time_limit(0);
                $buffer = fread($handle, 8192);
                echo $buffer;
                @flush();
        		@ob_flush();
            }
            fclose($handle);
		}
	}

	function attached_files($attachable_id, $attachable_type) {
		$attached_files = array();

		if(isset($attachable_id)) {
			if($attachable_id == 'undefined' && isset($this->params['form']['attachable_type_condition']) && isset($this->params['form']['attachable_type_condition_value'])) {
				$attachable_id = $this->get_attachable_type_id($attachable_type, $this->params['form']['attachable_type_condition'], $this->params['form']['attachable_type_condition_value']);
			}
			$attached_files = $this->Document->find('all', array(
				'conditions' => array('attachable_id' => $attachable_id, 'attachable_type' => $attachable_type)
			));
		}

		$this->set('status',
			array(
				'success' => !empty($attached_files) ? true : false,
				'data' => $attached_files
			)
		);
	}

	function upload() {
		$success = false;
		$message = "";

		$attachable_type = $this->data['Document']['attachable_type'];

		if($this->data['Document']['attachable_id'] != 'undefined') {
			$attachable_id = $this->data['Document']['attachable_id'];
		} else {
			$condition = $this->data[$attachable_type]['attachable_type_condition'];
			$value = $this->data[$attachable_type]['attachable_type_condition_value'];
			$this->log('Parameters were set for this type: condition: ' . $condition . ' with value: ' . $value);

			$attachable_id = $this->get_attachable_type_id($attachable_type, $condition, $value);
		}

		$this->log('Upload document for attachable type: ' . $attachable_type . ' and id ' . $attachable_id . ' ' . $_FILES['Document']["name"]);

		$this->data['Document']['attachable_id'] = $attachable_id;
		$this->data['Document']['document'] = $_FILES['Document'];

		if (!empty($this->data)) {
			$this->log('Upload document started');
			$this->Document->create($this->data);
			if ($this->Document->save()) {
				if($attachable_type == 'AttendanceFeedback') {
					$this->Notification->notify($attachable_type, $attachable_id);
				}
				$success = true;
			} else {
				$message = "The document could not be saved. A file with the same name might already exist.";
			}
		} else {
			$message = "No file was submitted to the server";
		}

		$this->set('status',
			array(
				'success' => $success,
				'error' => $message
			)
		);
	}

	function delete($id) {
		//Configure::write('debug', 2);

		$success = false;
		if ($id != '') {
			if ($this->Document->delete($id)) {
				$success = true;
			}
		}

		$this->set('status',
			array(
				'success' => $success,
				'deletedId' => $id
			)
		);
	}

	function get_attachable_type_id($attachable_type, $condition, $value) {
		// find existing attachable type for set condition
		$attachable_type_info = $this->{$attachable_type}->find('first', array(
			'conditions' => array($condition => $value),
			'fields' => 'id'
		));

		if(!empty($attachable_type_info)) {
			$attachable_id = $attachable_type_info[$attachable_type]['id'];
		} else {
			$data = array($attachable_type => array($condition => $value));
			$this->{$attachable_type}->save($data);
			$attachable_id = $this->{$attachable_type}->id;
			$this->log('Created new attachable_type with id: ' . $this->{$attachable_type}->id);
		}

		return $attachable_id;
	}
}
?>