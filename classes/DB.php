<?php
	class DB 
	{
		private static $_instance = null;
		private $_pdo, 
			$_query, //the last query represented
			$_error = false, //wether error 
			$_results, //store results set
			$_count = 0; //number of results

		private function __construct()
		{
			try 
			{
				$this -> _pdo = new PDO('mysql:host=' . Config::get('mysql/host') . ';dbname=' . Config::get('mysql/db'), Config::get('mysql/username'), Config::get('mysql/password'));
			} catch(PDOException $e) {
				die($e -> getMessage());
			}
		}

		public static function getInstance()
		{
			if (!isset(self::$_instance))
			{
				self::$_instance = new DB();
			}
			return self::$_instance;
		}

		public function query($command, $params = array())
		{
			$this -> _error = false;
			//print_r($params);
			if ($this -> _query = $this -> _pdo -> prepare($command)) 
			{
				$pt = 1;
				if (count($params))
				{
					foreach($params as $param) 
					{
						$this -> _query -> bindValue($pt, $param);
						$pt++;
					}
				}
			}
			if ($this->_query->execute())
			{
				$this -> _results = $this -> _query -> fetchAll(PDO::FETCH_OBJ);
				$this -> _count = $this -> _query -> rowCount();
			}
			else
			{
				$this -> _error = true;
			}
		}

		public function action($action, $table, $where = array())
		{
			if (count($where) == 3)
			{
				$opperators = array('=','>','<','>=','<=');
				$field = $where[0];
				$opperator = $where[1];
				$value = $where[2];
				if(in_array($opperator, $opperators))
				{
					$command = "{$action} FROM {$table} WHERE {$field} {$opperator} ?";
					$this->query($command, array($value));
					if (!$this->error())
					{
						return $this;
					}
				}
			}
			return false;
		}

		public function get($table, $where)
		{
			return $this->action('SELECT *', $table, $where);
		}

		public function delete($table, $where)
		{
			return $this->action('DELETE', $table, $where);
		}


		public function insert($table, $fields = array())
		{
			if (count($fields))
			{
				$keys = array_keys($fields);
				$sql = "INSERT INTO {$table} (" . $keys[0];
				$values = array();
				foreach($fields as $field) {
					$values[] = $field;
				}
				foreach($keys as $key)
				{
					if ($key != $keys[0])
					{
						$sql = $sql . "," . $key;
					}
				}
				
				$sql = $sql . ") VALUES (?";
				for ($x = 0; $x < count($fields) - 1; $x++)
					$sql = $sql . ', ?';
				$sql .= ')';
				$this->query($sql, $fields);
				
				//finding user after creating it.
				$x = 0;
				$username = "";
				foreach($fields as $field) { 
					echo $keys[$x] . " ";
					if ($keys[$x] === 'username')
						$username = $field;
					$x++;
				}
				$this->get($table, array('username', '=', $username));
				//done
				
				if (!$this->error())
					return true;
			}
			return false;
		}
		
		public function update($table, $id, $fields = array())
		{
			$sql = "UPDATE {$table} SET ";
			$set = '';
			$keys = array_keys($fields);
			$x = 1;
			foreach($fields as $name => $value)
			{
				$set .= "{$name} = ?";
				if ($x < count($fields))
					$set .= ', ';
				$x++;
			}
			//echo $set;
			$sql = "UPDATE {$table} SET {$set} where id = {$id}";
			$this->query($sql, $fields);
			if ($this->error())
				return true;
			return false;
		}
		public function results()
		{
			return $this->_results;
		}

		public function first()
		{
			return $this->results()[0];
		}

		public function count()
		{
			return $this->_count;
		}

		public function error() 
		{
			return $this -> _error;
		}
	}
?>