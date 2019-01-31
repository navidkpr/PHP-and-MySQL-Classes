		<?php
			class User {
				private $_db,
						$_data,
						$_sessionName,
						$_isLoggedIn,
						$_imgCount,
						$_videoCount,
						$_audioCount,
						$_clientPath;

				public function __construct($user = null)
				{
					$this->_db = DB::getInstance();
					$this->_sessionName = Config::get('session/session_name');
					if ($user != null)
						$this->find_id($user);
					else if (Session::exists($this->_sessionName)) {
						$user = Session::get($this->_sessionName);
						if ($this->find_id($user))
							$this->_isLoggedIn = 1;
						else
							$this->logOut();
						//echo "CHECK : ISLOGGEDIN: " . $this->_isLoggedIn . "<br>";
					}
				}

				public function create($fields = array()) {
					if (!$this->_db->insert('users', $fields))
						throw new Exception('User was not created.');
					else
					{
						$this->_data = $this->_db->first();
						$this->_clientPath = "client_files/client" . $this->data()->id;
						mkdir($this->_clientPath);
						mkdir($this->_clientPath . '/image');
						mkdir($this->_clientPath . '/video');
						mkdir($this->_clientPath . '/audio');
					}
				}

				public function find_username($username = null) {
					$this->_data = $this->_db->get('users', array('username', '=', $username));
					if ($this->_data->count()) {
						$this -> _data = $this->_data->first();
						return true;
					}
					return false;
				}

				public function find_id($id = null) {
					$this->_data = $this->_db->get('users', array('id', '=', $id));
					if ($this->_data->count()) {
						$this->_data = $this->_data->first();
						$this->_clientPath = "client_files/client" . $this->data()->id;
						$this->_imgCount = $this->data()->image_count;
						$this->_videoCount = $this->data()->video_count;
						$this->_audioCount = $this->data()->audio_count;
						//print_r($this->_data);
						return true;
					}
					return false;
				}
				
				public function login($username = null, $password = null) {
					$exists = $this->find_username($username);
					if (!$exists)
						return false;
					if ($this->data()->password != Hash::make($password, $this->data()->salt))
						return false;
					Session::put($this->_sessionName, $this->data()->id); 
					return true;
				}

				public function data() {
					return $this->_data;
				}

				public function logOut() {
					Session::delete($this->_sessionName);
				}

				public function isLoggedIn() {
					return $this->_isLoggedIn;
				}

				public function upload($type) {
					$dir = $this->_clientPath;
					$name = "";
					if ($type === 'image') {
						$name = "/image/image" . ($this->_imgCount + 1);
						$fileExtension = 'jpg';
					}
					else if ($type === 'video') {
						$name = "/video/video" . ($this->_videoCount + 1);
						$fileExtension = 'gif';
					}
					else if ($type === 'audio') {
						$name = "/audio/audio" . ($this->_audioCount + 1);
						$fileExtension = 'mp3';
					}
					//$fileExtension = end(explode(".", $_FILES['FileToUpload']['name']));
					$dir = $dir . $name;
					mkdir($dir);
					echo "DIR: " . $dir . "<br>";
					$Target_file = $dir . "/" . $type . "." . $fileExtension;
					echo $Target_file;
					if (move_uploaded_file($_FILES["FileToUpload"]["tmp_name"], $Target_file)) {
						if ($type === 'image')
							$this->_imgCount++;
						else if ($type === 'video')
							$this->_videoCount++;
						else if ($type === 'audio')
							$this->_audioCount++;
						$this->_db->update('users', $this->data()->id, array('image_count' => $this->_imgCount, 
																			'video_count' => $this->_videoCount, 
																			'audio_count' => $this->_audioCount));
						return true;
					}
					else
						return false;
				}

				public function imgCount() {
					return $this->_imgCount;
				}

				public function videoCount() {
					return $this->_videoCount;
				}

				public function audioCount() {
					return $this->_audioCount;
				}

				public function clientPath() {
					return $this->_clientPath;
				}
			}
		?>