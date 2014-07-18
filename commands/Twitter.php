<?php
	class TwitterAPI {
		public $twitter;
		public $config;
		public $setting;
		public $lasttweet_id = 0;
		CONST RATE_LIMIT_STATUS_TO_JSON = "https://api.twitter.com/1.1/application/rate_limit_status.json";
		CONST HOME_TIMELINE_TO_JSON 	= "https://api.twitter.com/1.1/statuses/home_timeline.json";
		CONST USER_TIMELINE_TO_JSON 	= "https://api.twitter.com/1.1/statuses/user_timeline.json";
		CONST MENTION_TIMELINE_TO_JSON 	= "https://api.twitter.com/1.1/statuses/mentions_timeline.json";
		CONST HASH_TIMELINE_TO_JSON		= "https://api.twitter.com/1.1/search/tweets.json";

		public function __construct($setting,$config) {
			include 'Terminal.php';
			$this->config = $config;
			$this->setting = $setting;

			$this->connect();
			$resources = $this->getRateLimit();

			$user = (isset($this->setting['user'])) ? $this->setting['user'] : ($this->setting['action'] !== "Hash") ? '自分' : "Hashtag: {$this->setting['filter']} ";
			$action = $this->setting['action'];
			$delay = $this->setting['delay'];

			Terminal::Put('[ctrl+c]escape');
			Terminal::Put("------------ {$action} {$user}のツイート [delay: {$delay}s] --------------");
			foreach($resources as $key=>$resource) {
				Terminal::Put(sprintf("----- %8s: %s -----",$key,$resource));
			}
		}

		public function connect() {
			try {
				$this->twitter = new TwitterOAuth($this->config->consumerKey,$this->config->consumerSecret,$this->config->accessToken,$this->config->accessTokenSecret);
			} catch (Exception $e) {

				var_dump($e);exit;$e->getMessage();
			}
		}

		public function getRateLimit() {
			$requestJson = $this->twitter->OAuthRequest(self::RATE_LIMIT_STATUS_TO_JSON,"GET",array("resources"=>"search,statuses"));
			$requestObj = json_decode($requestJson);

			$mention = $requestObj->resources->statuses->{'/statuses/mentions_timeline'};
			$home    = $requestObj->resources->statuses->{'/statuses/home_timeline'};
			$user    = $requestObj->resources->statuses->{'/statuses/user_timeline'};
			$search  = $requestObj->resources->search->{'/search/tweets'};

			return (object)array(
				'mention'=>self::parseLimit($mention),
				'user'=>self::parseLimit($user),
				'home'=>self::parseLimit($home),
				'search'=>self::parseLimit($search)
			);
		}

		static function parseLimit($object) {
			return sprintf("%3d/%3d nextResetDate: %s",$object->remaining,$object->limit,date("Y-m-d H:i:s",$object->reset));
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
			$replace_needle = array(
				"'","\"","`",
				"\r\n","\r","\n",
				"-"
			);
			$text = str_replace($replace_needle,"",$text);
			$text = self::urlEraser($text);
			$text = self::wwwEraser($text);
			$text = trim($text);
			return $text;
		}

		static function wwwEraser($string) {
			return preg_replace("/(w|ｗ)+/","w",$string);
		}

		static function urlEraser($string) {
			return preg_replace('|https?://[\w/:%#\$&\?\(\)~\.=\+\-]+|i','',$string);
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