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
			} elseif(isset($option['t']) && !$option['t']) { //自タイムライン
				return array(
					'action'=>'Timeline',
					'filter'=>(isset($option['f'])) ? $option['f'] : NULL,
					'delay'=>(isset($option['d'])) ? $option['d'] : DEFAULT_DELAY
				);
			} elseif(isset($option['t']) && $option['t']) { //他タイムライン
				return array(
					'action'=>'UserTimeline',
					'user'=>$option['t'],
					'filter'=>(isset($option['f'])) ? $option['f'] : NULL,
					'delay'=>(isset($option['d'])) ? $option['d'] : DEFAULT_DELAY
				);
			} elseif(isset($option['m'])) { //メンション表示
				return array(
					'action'=>'Mention',
					'filter'=>(isset($option['f'])) ? $option['f'] : NULL,
					'delay'=>(isset($option['d'])) ? $option['d'] : DEFAULT_DELAY
				);
			} elseif(isset($option['h'])) {
				return array(
					'action'=>'Hash',
					'filter'=>($option['h'] !== '') ? $option['h'] : NULL,
					'delay'=>(isset($option['d'])) ? $option['d'] : DEFAULT_DELAY
				);
			} else { //ヘルプ表示
				return array(
					'action'=>'Help',
					'filter'=>NULL,
					'delay'=>NULL
				);
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