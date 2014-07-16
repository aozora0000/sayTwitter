# sayTwitter
- script:        sayTwitter
- version         :        0.1.0
- auther          :        J山B作(@aozora0000)
- special thanks  :        [twitteroauth](https://github.com/abraham/twitteroauth)

## オプション
### 主要オプション
```
    [none]                          引数無しで自タイムライン表示
    -t"[user_id]"                   タイムライン表示(デフォルトで自タイムライン)
    -m                              メンション表示
    -h="#example"                   ハッシュタグ表示
```
### その他オプション
```
    -help                           へるぷ表示
    -d[second]                      ディレイ設定(デフォルトで60秒)
```

## 備考

ディレイに関してはTwitterAPI上の仕様により、15分間のリクエスト数が決まっています。

大体のリクエストに関しては15req/minutesになっているようです。

config.ini.sampleをconfig.iniにリネーム。

Key/Secretを取得・配置してから実行してください。

Key取得先URL: [https://dev.twitter.com/](https://dev.twitter.com/)

##★ Caution ★

すべてはオウンリスクです。

このスクリプトを実行した事による何らかの被害、社会的地位や名誉への被害への責任は一切負いません。

このスクリプトを利用し、Twitterに多大な負荷を掛ける行為や迷惑行為は禁止します。というか死んで下さい。

このスクリプトの再配布は禁止します。改変はご自由にどうぞ。


怪我しても泣かない人・怪我が快感に感じる人のみ実行して下さい。
