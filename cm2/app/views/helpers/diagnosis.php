<?php
class DiagnosisHelper extends AppHelper
{
	function tree($data, $checkboxes) {
		$tree = array();
		
		foreach ($data as $r) {
			$node = array(
				'id' => $r['Diagnosis']['id'],
				'text'=>$r['Diagnosis']['description'] . ($r['Diagnosis']['is_obsolete']?' (obsolete)':''),
				'leaf' => empty($r['children']),
				'cls' => $r['Diagnosis']['is_obsolete']?'obsolete':'',
				'checked' => empty($checkboxes)?null:empty($r['Diagnosis']['is_obsolete']),
			);
			if (!empty($r['children'])) {
				$node['children'] = $this->tree($r['children'], $checkboxes);
			}
			$tree[] = $node;
		}
		
		return $tree;
	}
}