<?php
	class Room {
		private $_db,
				$_roomId,
				$_error = false,
				$_imagePaths = array(),
				$_videoPaths = array(),
				$_audioPaths = array(),
				$_client,
				$_extension = array();

		public function __construct($roomKey) {
			$this->_db = DB::getInstance();
			$this->_error = false;
			$this->_imagePaths = array();
			$this->_videoPaths = array();
			$this->_audioPaths = array();
			$this->_extension = array('image' => 'jpg', 'video' => 'gif', 'audio' => 'mp3');
			$this->_roomId = Session::get('room_name');
			if (Session::get('room_name') != null) {
				$this->_roomId = $roomKey;
				$this->_client = $this->_db->get('users', array('id', '=', $roomKey))->first();
				Session::put('imagePaths', $this->getPaths('image'));
				Session::put('audioPaths', $this->getPaths('audio'));
				Session::put('videoPaths', $this->getPaths('video'));
				return true;
			}
			else {
				$this->_error = true;
				return false;
			}
		}

		private function getPaths($type) {
			${'_' . $type . 'Paths'} = array();
			if ($type == 'image')
				$count = $this->_client->image_count;
			else if ($type == 'video')
				$count = $this->_client->video_count;
			else if ($type == 'audio')
				$count = $this->_client->audio_count;
			$pt = 1;
			while ($pt <= $count) {
				$newPath = "client_files/client{$this->_roomId}/{$type}/{$type}{$pt}/";
				array_push($this->{'_' . $type . "Paths"}, $newPath);
				$pt++;
			}
			return $this->{'_' . $type . "Paths"};
		}

		public function imagePaths() {
			return $this->_imagePaths;
		}

		public function videoPaths() {
			return $this->_videoPaths;
		}

		public function audioPaths() {
			return $this->_audioPaths;
		}

		public function error() {
			return $this->_error;
		}

		public function current($type) {
			return $this->{'_' . $type . "Paths"}[Session::get($type . "Num")] . $type . "." . $this->_extension[$type];
		}

		public function nextImage() {
			if (!Session::exists('imageNum')) {
				echo "FUCK";
        		Session::put('imageNum', 0);
        		return $this->imagePaths()[0] . "image.jpg";
			}
			echo "imageNUM 1: " . Session::get('imageNum') . "<br>";
	        $dir = $this->imagePaths()[Session::get('imageNum')];
	        if (Input::get('sentence')) {
	            $myfile = fopen("{$dir}/data.txt", 'w');
	            $txt = Input::get('sentence') . "\r\n";
	            fwrite($myfile, $txt);
	            fclose($myfile);
	            Session::put('file_path', $dir);
	            Redirect::to("DataManager.php");
	        }
	        echo "imageNUM 2: " . Session::get('imageNum') . "<br>";
	        $user = new User($this->_roomId);
	       	$cntImage = $user->imgCount();
	         if (Session::get('imageNum') >= $cntImage - 1)
	            Redirect::to('reachedLastFile.php');
	        Session::put('imageNum', Session::get('imageNum') + 1);
	        echo "imageNUM 3: " . Session::get('imageNum') . "<br>";
	   		$dir = $this->imagePaths()[Session::get('imageNum')] . 'image.jpg';
	   		return $dir;
		}

		public function nextVideo() {
			if (!Session::exists('videoNum')) {
        		Session::put('videoNum', 0);
        		return $this->videoPaths()[0] . "video.gif";
			}
	        $dir = $this->videoPaths()[Session::get('videoNum')];
	        if (Input::get('sentence')) {
	            $myfile = fopen("{$dir}/data.txt", 'w');
	            $txt = Input::get('sentence') . "\r\n";
	            fwrite($myfile, $txt);
	            fclose($myfile);
	        }
	        $user = new User($this->_roomId);
	       	$cntVideo = $user->videoCount();
	        if (Session::get('videoNum') == $cntVideo - 1)
	        	Redirect::to('reachedLastFile.php');
	        Session::put('videoNum', Session::get('videoNum') + 1);
	   		$dir = $this->videoPaths()[Session::get('videoNum')] . 'video.gif';
	   		return $dir;
		}

		public function nextAudio() {
			if (!Session::exists('audioNum')) {
        		Session::put('audioNum', 0);
        		return $this->audioPaths()[0] . "audio.gif";
			}
	        $dir = $this->audioPaths()[Session::get('audioNum')];
	        if (Input::get('sentence')) {
	            $myfile = fopen("{$dir}/data.txt", 'w');
	            $txt = Input::get('sentence') . "\r\n";
	            fwrite($myfile, $txt . PHP_EOL);
	            fclose($myfile);
	        }
	        $user = new User($this->_roomId);
	       	$cntAudio = $user->audioCount();
	        if (Session::get('audioNum') == $cntAudio - 1)
	            Redirect::to('reachedLastFile.php');
	        Session::put('audioNum', Session::get('audioNum') + 1);
	   		$dir = $this->audioPaths()[Session::get('audioNum')] . 'audio.mp3';
	   		return $dir;
		}
	}
?>
