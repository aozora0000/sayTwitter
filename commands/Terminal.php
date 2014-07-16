<?php
	class Terminal {
		public static function Say($user,$text,$sleep = 0.5) {
			exec("say '{$user}さんのツイート'");
			exec("sleep {$sleep}");
			exec("say '{$text}'");
			usleep(SAY_INTERVAL);
		}

		public static function Mes($text) {
			exec("say '{$text}'");
			usleep(SAY_INTERVAL);
		}

		public static function Put($string) {
			print $string.PHP_EOL;
		}
	}