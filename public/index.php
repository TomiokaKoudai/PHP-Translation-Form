<?php
declare(strict_types=1); //厳密な型付け

// 動作確認用
// echo "Hello PHP"; //Hello PHPの出力

// セッションを開始する宣言
session_start();

// CSRFトークン生成（初回のみ）
// セッションの中にtokenというデータが空の場合の処理
if (empty($_SESSION['token'])) {
  // tokenに32バイト分のランダムなテータを生成し16進数に変換して格納
  // bin2hex：扱いやすい16進数の文字列に変換
  // random_bytes(32)：ランダムなデータを引数にしてしたバイト分生成
  $_SESSION['token'] = bin2hex(random_bytes(32));
}

// 他のファイルを読み込む命令
// __DIR__：現在のファイルから見るという宣言
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Controllers/TranslateController.php';

// 変数CcontrollerにTranslateControllerのインスタンスの生成
$controller = new TranslateController();
// indexメソッドの実行
$controller->index();