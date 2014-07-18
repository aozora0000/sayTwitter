<?php 
	class UserTimeline extends TwitterAPI {
		public function get() {
			$reqestJson = $this->getRequest(self::USER_TIMELINE_TO_JSON,"GET",array("count"=>COUNT,"include_rts"=>false,"screen_name"=>$this->setting['user']));
			$requestObj = json_decode($reqestJson);
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