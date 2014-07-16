<?php
	class TwitterAPI {
		public $twitter;
		public $config;
		public $setting;
		public $lasttweet_id = 0;
		CONST HOME_TIMELINE_TO_JSON 	= "https://api.twitter.com/1.1/statuses/user_timeline.json";
		CONST USER_TIMELINE_TO_JSON 	= "https://api.twitter.com/1.1/statuses/user_timeline.json";
		CONST MENTION_TIMELINE_TO_JSON 	= "https://api.twitter.com/1.1/statuses/mentions_timeline.json";
		CONST HASH_TIMELINE_TO_JSON		= "https://api.twitter.com/1.1/search/tweets.json";

		public function __construct($setting,$config) {
			include 'Terminal.php';
			$this->config = $config;
			$this->setting = $setting;
			$this->connect();
			$user = (isset($this->setting['user'])) ? $this->setting['user'] : ($this->setting['action'] !== "Hash") ? '自分' : "Hashtag: #{$this->setting['filter']} ";
			$action = $this->setting['action'];
			$delay = $this->setting['delay'];

			print '[ctrl+c]escape'.PHP_EOL;
			print "----------- {$action} {$user}のツイート [delay: {$delay}s]-------------".PHP_EOL;
		}

		public function connect() {
			try {
				$this->twitter = new TwitterOAuth($this->config->consumerKey,$this->config->consumerSecret,$this->config->accessToken,$this->config->accessTokenSecret);
			} catch (OAuthException $e) {
				var_dump($e->getMessage());
			}
		}

		static function parseTimeline($tweet) {
			return (object)array(
				'id'=>$tweet->id,
				'text'=>self::toString($tweet->text),
				'user'=>$tweet->user->name,
				'timestamp'=>date('Y-m-d H:i:s',strtotime($tweet->created_at))
			);
		}

		static function toString($text) {
			$text = str_replace(array("'","\"","`"),"",$text);
			$text = preg_replace('|https?://[\w/:%#\$&\?\(\)~\.=\+\-]+|i','',$text);
			$text = str_replace(array("\r\n","\r","\n"),' ',$text);
			$text = trim($text);
			return $text;
		}

		static function parseTimelineOrderASC($requestObj) {
			if($requestObj) {
				foreach($requestObj as $req) {
					$obj[] = self::parseTimeline($req);
				}
				return (object)array_reverse($obj);
			}
		}

		static function notNewTweet() {
			Terminal::Put("新着ツイートはありませんでした。");
			Terminal::Mes("新着ツイートはありませんでした。");
		}
	}