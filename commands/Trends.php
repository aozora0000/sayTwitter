<?php
	class Trends extends TwitterAPI {
		public function get() {
			$trendWord = $this->getTrendWords();
			Terminal::Put("トレンドワード：{$trendWord}の取得結果");

			$requestJson = $this->getRequest(self::HASH_TIMELINE_TO_JSON,"GET",array("count"=>COUNT,"include_rts"=>false,"q"=>$trendWord));
			$requestObj = json_decode($requestJson);
			$flag = FALSE;
			if($requestObj) {
				$tweetObjs = self::parseTimelineOrderASC($requestObj->statuses);
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

		private function getTrendWords() {
			$trendsJson = $this->getRequest(self::TREND_WORDS_TO_JSON,"GET",array('count'=>COUNT));
			$trendsObj = json_decode($trendsJson);
			$trendsObj = $trendsObj[0];

			foreach($trendsObj->trends as $key=>$obj) {
				Terminal::Put(sprintf("%2d:   %s",$key+1,$obj->name));
			}
			$input_number = Terminal::getStdInNumber("半角英数字でトレンド番号を入力してください。");
			return $trendsObj->trends[$input_number - 1]->name;
		}
	}