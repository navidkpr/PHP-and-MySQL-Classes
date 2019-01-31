<?php
	class Validate
	{
		private $_errors = array();
		private $_passed = true;
		private	$_db;

		public function __construct() {
			$this->_db = DB::getInstance();
		}
		
		public function check($source, $items = array())
		{
			foreach ($items as $column => $item)
			{
				foreach ($item as $condition => $value)
				{
					if ($condition == 'required' && empty($source[$column]))
						$this->addError("{$column} is required to be filled out");
					else if (!empty($source[$column]))
					{
						if ($condition == 'min')
						{
							if (strlen($source[$column]) < $value)
								$this->addError("minimum number of characters allowed for {$column} is {$value}");
						}

						if ($condition == 'max')
						{
							if (strlen($source[$column]) > $value)
								$this->addError("maximum number of characters allowed for {$column} is {$value}");
						}

						if ($condition == 'match')
						{
							if ($source[$value] != $source[$column])
								$this->addError("{$column} does not match with {$value}");
						}
						if ($condition == 'unique')
						{
							if ($this->_db == null)
								echo"FUCK U";
							$this->_db->get($value, array($column, '=', $_POST[$column]));
							if ($this->_db->count() != 0)
								$this->addError("The selected {$column} has been used before");
						}
					}
				}
			}
			return $this;
		}
		
		
		private function addError($error)
		{
			$this->_passed = false;
			$this->_errors[] = $error;
		}
		
		public function passed()
		{
			return $this->_passed;
		}
		
		public function errors()
		{
			return $this->_errors;
		}
	}
?>