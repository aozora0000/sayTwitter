<?php
	class Lists extends TwitterAPI {
		private $list;
		public function preprocess() {
			Terminal::Put("リスト一覧を取得中です、暫くお待ち下さい。");
			Terminal::Mes("リスト一覧を取得中です、暫くお待ち下さい。");
			$lists = $this->getLists();
			$this->list = self::getListNumber($lists);
		}

		public function get() {
			$reqestJson = $this->getRequest(self::SHOW_LIST_TO_JSON,"GET",array("count"=>COUNT,"include_rts"=>false,"list_id"=>$this->list->id,"slug"=>$this->list->slug));
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

		private static function getListNumber($lists) {
			foreach($lists as $key=>$list) {
				Terminal::Put(sprintf("%2d : %s[%d]",$key + 1,$list->name,$list->member));
			}
			Terminal::Mes("リスト番号を選択して入力してください。");
			while(true) {
				$number = Terminal::getStdInNumeric("リスト番号を選択して入力してください。");
				if(isset($lists[$number - 1])) {
					break;
				}
			}
			return $lists[$number - 1];
		}

		private function getLists() {
			try {
				$requestJson = $this->getRequest(self::GET_LISTS_TO_JSON,"GET",NULL);
				$requestObj = json_decode($requestJson);
				if(is_null($requestObj)) {
					throw new Exception("もしかして？「リストが作られていません。」");
				}
				$lists = array();
				foreach($requestObj as $key=>$list) {
					$lists[] = (object)[
						'id'  =>$list->id,
						'slug'=>$list->slug,
						'name'=>$list->name,
						'member'=>$list->member_count
					];
				}
				return $lists;
			} catch(Exception $e) {
				Terminal::Put("エラー発生！処理を終了します。\n{$e->getMessage()}");
				Terminal::Mes("エラー発生！処理を終了します。{$e->getMessage()}");
				exit;
			}
		}
	}