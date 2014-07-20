<?php
	if(!ini_get('date.timezone')) {
		date_default_timezone_set('Asia/Tokyo');
		ini_set("date.timezone", "Asia/Tokyo");
	}
	//ファイル・ディレクトリ定義
	define('DS',DIRECTORY_SEPARATOR);
	define('ROOT_DIR',dirname(__FILE__).DS);
	define('OAUTH_DIR',ROOT_DIR.'twitteroauth'.DS);
	define('CMD_DIR',ROOT_DIR.'commands'.DS);
	define('CONFIG_INI',ROOT_DIR.'config.ini');
	define('DEFAULT_DELAY',60);
	define('SAY_INTERVAL',3000);
	define('COUNT',30);

	//スクリプト呼び出し
	require_once(OAUTH_DIR.'twitteroauth.php');
	require_once(CMD_DIR.'Commands.php');

	//設定ファイル呼び出し
	$config = (object)parse_ini_file(CONFIG_INI);

	/*
	 *	引数取得(引数無しで自タイムライン表示)
	 *	
	 *	-H --help 							へるぷ表示
	 *	-u[user_id] --user[user_id] 		タイムライン表示(デフォルトで自タイムライン)
	 *	-m --mention						メンション表示
	 *  -h[hash] --hash[hash]				ハッシュ検索
	 * 	-t --trend							トレンド
	 *  -l --location 						位置からツイート検索
	 * 	-d[second] 							ディレイ設定(デフォルトで60秒)
	 */

	$opt = getopt('u::m::f::d::h::H::t::g::l::',array('help::','user::','mention::','hash::','trend::','geometory::','list::'));

	$INST = new Commands($opt,$config);
	$INST->execute();