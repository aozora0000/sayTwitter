<?php
	class Hash extends TwitterAPI {
		public function get() {
			$requestJson = $this->getRequest(self::HASH_TIMELINE_TO_JSON,"GET",array("count"=>COUNT,"include_rts"=>false,"q"=>$this->setting['filter']));
			$requestObj = json_decode($requestJson);
			$flag = FALSE;
			if($requestObj) {
				$tweetObjs = self::parseTimelineOrderASC($requestObj->statuses);
				foreach($tweetObjs as $tweet) {
					if(!$tweet->retweet && !$tweet->is_replay) {
						if($this->lasttweet_id < $tweet->id) {
							$this->lasttweet_id = $tweet->id;
							Terminal::Put("{$tweet->user} : {$tweet->text}   [{$tweet->timestamp}]");
							Terminal::Say($tweet->user,$tweet->text);
						} else {
							$flag = TRUE;
						}
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