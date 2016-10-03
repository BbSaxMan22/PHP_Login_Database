<?php

class Validation {
	private $_passed = false,
			$_erroes = array(),
			$_db = null;

	public function __construct() {
		$this->_db = DB::getInstance();
	}

	public function check($source, $items = array()) {
		foreach($items as $item => $rules) {
			foreach($rules as $rule => $rule_value) {
				
				$value = trim($source[$item]);//trimmed to ensure no white space
				$item = escape($item);

				if ($rule === 'required' && empty($value)) {// not good, the user needs to add this information
					$this->addError("{$item} is required");//item will be the field name
					// TODO include a name attribute in each item (rule) and pull that for display
				} else if (!empty($value)) {
					switch ($rule) {
						case 'min':
							if (strlen($value) < $rule_value) {
								$this->addError("{$item} must be a min of {$rule_value} characters");
							}
						break;
						case 'max':
							if (strlen($value) > $rule_value) {
								$this->addError("{$item} must be a max of {$rule_value} characters");
							}
						break;
						case 'matches':// used here for passwords, but applicable elsewhere eg captcha values
							if ($value != $source[$rule_value]) {
								$this->addError("{$rule_value} must match {$item}");
							}
						break;

						case 'unique':
							$check = $this->_db->get($rule_value, array($item, '=', $value));
							if ($check->count()) {
								$this->addError("{$item} already exists.");
							}
						break;
					}
				}
			}
		}//items in the array, rules in each item array
	
		if (empty($this->_errors)) {
			$this->_passed = true;
		}

		return $this;

	}
	
	private function addError($error) {
		$this->_errors[] = $error;
	}

	public function error() {
		return $this->_errors;
	}// for testing purposes

	public function passed() {
		return $this->_passed;
	}
}