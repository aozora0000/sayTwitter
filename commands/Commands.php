<?php
	include 'Twitter.php';

	class Commands {
		protected $config;
		protected $opt;
		public function __construct($opt,$config) {
			$this->config = $config;
			$this->opt    = $opt;
		}

		public static function parseOption($option) {
			if(empty($option)) { //デフォルト動作(自タイムライン)
				return array(
					'action'=>'Timeline',
					'filter'=>NULL,
					'delay'=>DEFAULT_DELAY
				);
			} elseif ( self::margenalOption('u','user',$option) && ($option['t'] === false || $option['user'] === false) ) { //自タイムライン
				return array(
					'action'=>'Timeline',
					'filter'=>(isset($option['f'])) ? $option['f'] : NULL,
					'delay'=>(isset($option['d'])) ? $option['d'] : DEFAULT_DELAY
				);
			} elseif ( self::margenalOption('u','user',$option) && ($option['u'] !== false || $option['user'] !== false) ) { //他タイムライン
				return array(
					'action'=>'UserTimeline',
					'user'=>self::getMargenalOption('u','user',$option),
					'filter'=>(isset($option['f'])) ? $option['f'] : NULL,
					'delay'=>(isset($option['d'])) ? $option['d'] : DEFAULT_DELAY
				);
			} elseif(self::margenalOption('m','mention',$option)) { //メンション表示
				return array(
					'action'=>'Mention',
					'filter'=>(isset($option['f'])) ? $option['f'] : NULL,
					'delay'=>(isset($option['d'])) ? $option['d'] : DEFAULT_DELAY
				);
			} elseif(self::margenalOption('h','hash',$option)) { //ハッシュ検索表示
				return array(
					'action'=>'Hash',
					'filter'=>self:: getMargenalOption('h','hash',$option),
					'delay'=>(isset($option['d'])) ? $option['d'] : DEFAULT_DELAY
				);
			} elseif(self::margenalOption('t','trend',$option)) { //トレンド選択表示
				return [
					'action'=>'Trends',
					'filter'=>NULL,
					'delay'=>(isset($option['d'])) ? $option['d'] : DEFAULT_DELAY
				];
			} elseif(self::margenalOption('l','location',$option)) {
				return [
					'action'=>'Location',
					'filter'=>NULL,
					'delay'=>(isset($option['d'])) ? $option['d'] : DEFAULT_DELAY
				];
			} else { //ヘルプ表示
				return array(
					'action'=>'Help',
					'filter'=>NULL,
					'delay'=>NULL
				);
			}
		}

		static function margenalOption($key1,$key2,$option) {
			return (isset($option[$key1]) || isset($option[$key2])) ? true : false;
		}

		static function getMargenalOption($key1,$key2,$option) {
			if($option[$key1]) {
				return $option[$key1];
			} elseif ($option[$key2]) {
				return $option[$key2];
			} else {
				return NULL;
			}
		}


		public function execute() {
			$setting = self::parseOption($this->opt);
			$class_name = $setting['action'];
			include CMD_DIR.$class_name.".php";
			$CommandObject = new $class_name($setting,$this->config);
			while(true) {
				$CommandObject->get();
				sleep($setting['delay']);
			}
		}
	}