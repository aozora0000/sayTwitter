<?php
	class Timeline extends TwitterAPI {
		public function get() {
			$requestJson = $this->twitter->OAuthRequest(self::HOME_TIMELINE_TO_JSON,"GET",array("count"=>COUNT,"include_rts"=>false));
			file_put_contents("file.txt",$requestJson);
			$requestObj = json_decode($requestJson);
			$flag = FALSE;
			if($requestObj) {
				$tweetObjs = self::parseTimelineOrderASC($requestObj);
				foreach($tweetObjs as $tweet) {
					if($this->lasttweet_id < $tweet->id) {
						$this->lasttweet_id = $tweet->id;
						Terminal::Put("{$tweet->user} : {$tweet->text}   [{$tweet->timestamp}]");
						Terminal::Say($tweet->user,$tweet->text);
					} else {
						$flag = TRUE;
					}
				}
				if($flag) {
					self::notNewTweet();
				}
			} else {
				self::notNewTweet();
			}
		}
	}