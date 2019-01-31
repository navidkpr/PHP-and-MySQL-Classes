<?php
	class Session
	{
		public static function exists($name) {
			if (isset($_SESSION[$name]))
				return true;
			return false;
		}

		public static function delete($name) {
			if (self::exists($name))
				unset($_SESSION[$name]);
		}


		public static function put($name, $value) {
			return $_SESSION[$name] = $value;
		}

		public static function get($name) {
			return $_SESSION[$name];
		}
	}
?>