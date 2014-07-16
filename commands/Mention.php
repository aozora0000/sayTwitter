<?php
	class Mention extends TwitterAPI {
		public function get() {
			$reqestJson = $this->twitter->OAuthRequest(self::MENTION_TIMELINE_TO_JSON,"GET",array("count"=>COUNT,"include_rts"=>false));
			$requestObj = json_decode($reqestJson);
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
					Terminal::Put("新着ツイートはありませんでした。");
					Terminal::Mes("新着ツイートはありませんでした。");
				}
			} else {
				Terminal::Put("新着ツイートはありませんでした。");
				Terminal::Mes("新着ツイートはありませんでした。");
			}
		}
	}