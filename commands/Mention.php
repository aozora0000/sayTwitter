<?php
	class Mention extends TwitterAPI {
		public function get() {
			$requestJson = $this->twitter->OAuthRequest(self::MENTION_TIMELINE_TO_JSON,"GET",array("count"=>COUNT,"include_rts"=>false));
			$requestObj = json_decode($requestJson);
			$flag = FALSE;
			if($requestObj) {
				$tweetObjs = self::parseTimelineOrderASC($requestObj);
				foreach($tweetObjs as $tweet) {
					if($this->lasttweet_id < $tweet->id) {
						$this->lasttweet_id = $tweet->id;
						$tweet->text = preg_replace("/^@[¥x20-¥x7F]+/i","",$tweet->text);
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