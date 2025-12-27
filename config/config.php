<?php

declare(strict_types=1); //厳密な型付け

date_default_timezone_set('Asia/Tokyo'); //日本標準時を指定

// プロジェクト直下の.envを読み込み、環境変数に流し込む
// public直下に.envを置かないこと
// シンプル実装（複雑な .env の仕様は未対応）
function loadEnv(string $path): void
{
  // ①ファイルが存在するかどうかの確認
  // file_exists：指定したパスにファイルまたはディレクトリが存在するかどうかを確認する関数
  if (!file_exists($path)) {
    return;
  }

  // ②ファイル内容を配列形式で格納（余計なものを含まないようにする）
  // file関数：指定されたファイルを読み込む。行ごとに配列の要素として格納。
  // FILE_IGNORE_NEW_LINES：行末の改行文字が配列の要素に含まれなくなる。
  // FILE_SKIP_EMPTY_LINES：空行が配列から除外される。
  $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

  // ③配列の要素を１つずつ取り出す
  foreach ($lines as $line) {
    // 要素の前後の空白などの除去
    $line = trim($line);

    // 空行やコメント行などはスキップ
    // str_starts_with は PHP8+ で使用可能
    if ($line === '' || str_starts_with($line, '#')) {
      continue;
    }

    // ④KEY=VALUE 形式のみを抽出
    // strpos関数：文字列の中から指定した要素を探し出す関数。
    // 要素がなければfalseを返す。
    $pos = strpos($line, '=');
    if ($pos === false) {
      // 要素がなければスキップ
      continue;
    }

    // ⑤抽出したKEY=VALUE形式をKEYとVALUEに分解
    // substr関数：指定した文字列の開始位置から、指定した長さだけ部分文字列を抽出する関数。
    // 第一引数：対象の文字列
    // 第二引数：開始位置
    // 第三引数：長さ（省略可能）
    $key = trim(substr($line, 0, $pos));
    $value = trim(substr($line, $pos + 1));

    // 両端の" " や ' ' を外す（簡易）
    $value = trim($value, "\"'");

    // すでに環境変数があれば上書きしない（好みで変更可能）
    // getenv：環境変数を取得するための関数
    // 環境変数が存在しない場合の処理
    if (getenv($key) === false) {
      // 環境変数として値を設定
      // putenv：環境変数の設定をする関数
      putenv("$key=$value");
      // putenvだけでは$_ENVに自動反映されない場合があるため手動同期
      $_ENV[$key] = $value;
    }
  }
}

// 環境変数の取得（なければデフォルト or 例外）
function env(string $key, ?string $default = null): string
{
  $val = getenv($key);
  // 例外処理
  if ($val === false || $val === '') {
    if ($default !== null) return $default;
    throw new RuntimeException("env {$key} が見つかりません");
  }
  return $val;
}

// 読み込みの実行
// これでどこからでも使えるようになる。
loadEnv(__DIR__ . '/../.env');
