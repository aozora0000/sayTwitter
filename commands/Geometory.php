<?php
	class Geometory extends TwitterAPI {
		protected $geoObject;

		public function preprocess() {
			Terminal::Put("位置情報解析中です、暫くお待ち下さい。");
			Terminal::Mes("位置情報解析中です、暫くお待ち下さい。");
			try {
				$this->geoObject = self::getGeocode();
			} catch(Exception $e) {
				Terminal::Put("エラー発生！処理を終了します。\n{$e->getMessage()}");
				Terminal::Mes("エラー発生！処理を終了します。{$e->getMessage()}");
			}
		}

		public function get() {
			try {
				$requestJson = $this->getRequest(self::HASH_TIMELINE_TO_JSON,"GET",array("count"=>COUNT,"include_rts"=>false,"q"=>"","geocode"=>implode(",",$this->geoObject)));
				$requestObj = json_decode($requestJson);
				$flag = FALSE;
				if($requestObj) {
					$tweetObjs = self::parseTimelineOrderASC($requestObj->statuses);
					foreach($tweetObjs as $tweet) {
						if(!$tweet->retweet && !$tweet->is_replay) {
							if($this->lasttweet_id < $tweet->id) {
								$this->lasttweet_id = $tweet->id;
								Terminal::Put("{$tweet->user} : {$tweet->text}   [$tweet->location][{$tweet->timestamp}]");
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
			} catch(Exception $e) {
				Terminal::Put("エラー発生！処理を終了します。\n{$e->getMessage()}");
				Terminal::Mes("エラー発生！処理を終了します。{$e->getMessage()}");
			}
		}

		private static function getGeocode() {
			$globalIP = self::getGlobalIP();
			$url = sprintf(self::GET_GEOLOCATION_TO_JSON,$globalIP);
			$geoLocation = self::getHttpContents(sprintf(self::GET_GEOLOCATION_TO_JSON,$globalIP));
			if(self::isGeoLocation($geoLocation->latitude) && self::isGeoLocation($geoLocation->longitude)) {
				Terminal::Put("位置情報取得完了。[{$geoLocation->country_name}:{$geoLocation->region_name}:{$geoLocation->city}]");
				Terminal::Mes("位置情報取得完了。");
				$range = Terminal::getStdInNumeric("キロメートル単位で範囲を入力してください。(1以上)");
				return [
					$geoLocation->latitude,
					$geoLocation->longitude,
					$range."km"
				];
			} else {
				Terminal::Put("位置情報の解析に失敗しました。手入力で位置情報を入力してください。 escape[q]");
				Terminal::Mes("位置情報の解析に失敗しました。手入力で位置情報を入力してください。");
				$latitude  = Terminal::getStdInFloat("緯度を入力してください。(例：東京駅   35.681382)");
				$longitude = Terminal::getStdInFloat("経度を入力してください。(例：東京駅  139.766084)");
				$range     = Terminal::getStdInNumeric("キロメートル単位で範囲を入力してください。(1以上)");
				return [
					$latitude,
					$longitude,
					$range."km"
				];
			}
		}

		private static function getGlobalIP() {
			$globalIPObj = self::getHttpContents(self::GET_GLOBALIP_TO_JSON);
			if(self::isIPAddress($globalIPObj)) {
				Terminal::Put("IP情報取得完了。");
				Terminal::Mes("IP情報取得完了。");
				return $globalIPObj->ip;
			} else {
				throw new Exception("IP情報が取得出来ませんでした。暫く待って再度試して下さい。");
			}
		}

		private static function isIPAddress($obj) {
			$needle = "/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/i";
			return (isset($obj->ip) && preg_match($needle,$obj->ip)) ? true : false;
		}

		private static function isGeoLocation($string) {
			return (!is_null($string) && is_float((float)$string)) ? true : false;
		}



		private static function getHttpContents($url) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,5);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			$request = curl_exec($ch);
			$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			if($httpcode !== 200) {
				throw new Exception("コンテンツが取得出来ませんでした。暫く待って再度試して下さい。");
			}
			return json_decode($request);
		}
	}