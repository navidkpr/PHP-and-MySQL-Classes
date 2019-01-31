<?php
		class Redirect {
			public static function to(string $path = null)
			{
				if (is_numeric($path))
				{
					switch($path)
					{
						case 404:
							header('HTTP/1.0 404 Not Found');
							include 'include/errors/404.php';
							exit;
						break;
					}

				}
				if (isset($path))
					header('Location: ' . $path);
			}
		}
?>