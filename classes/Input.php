<?php
	class Input
	{
		public static function exists($inp_type = 'post')
		{
			if ($inp_type == 'post')
				return !empty($_POST);
			if ($inp_type == 'get')
				return !empty($_GET);
			return false;
		}

		public static function get($name)
		{
			if (isset($_POST[$name]))
				return $_POST[$name];
			if (isset($_GET[$name]))
				return $_GET[$name];
			return false;
		}
	}
?>