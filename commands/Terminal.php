<?php
	class Terminal {
		public static function Say($user,$text,$sleep = 0.5) {
			exec("say -r 240 '{$user}さんのツイート'");
			exec("sleep {$sleep}");
			exec("say -r 240 '{$text}'");
			usleep(SAY_INTERVAL);
		}

		public static function Mes($text) {
			exec("say '{$text}'");
			usleep(SAY_INTERVAL);
		}

		public static function Put($string) {
			print $string.PHP_EOL;
		}

		public static function getStdInNumber($string) {
			while(true) {
				self::Put($string);
				$line = trim(fgets(STDIN));
				if($line === "q") {
					exit("終了します。\n");
				}
				if((preg_match("/^[0-9]+$/",$line) && (0 < $line && $line <= 10))) {
					return $line;
					break;
				}
			}
		}
	}