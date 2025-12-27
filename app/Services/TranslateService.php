<?php

declare(strict_types=1); // 厳密な型付け（型の自動変換を防ぐ）

// 翻訳処理を担当するクラス（ビジネスロジック層）
class TranslateService
{
  /**
   * 翻訳処理を行うメソッド
   *
   * @param string $text 翻訳したい元の文章
   * @return string 翻訳後の文章
   */
  public function translate(string $text): string
  {
    // ===== 開発初期に使っていた仮の翻訳処理 =====
    // APIを使わず、処理の流れだけを確認するためのコード
    // return 'Translated: ' . htmlspecialchars($text, ENT_QUOTES, 'UTF-8');


    // ===== 環境変数の取得 =====
    // .env に定義した APIキーを取得
    // env() は config.php で自作した関数
    $apiKey = env('GOOGLE_API_KEY');

    // 翻訳先の言語（指定がなければ英語）
    $target = env('GOOGLE_TRANSLATE_TARGET', 'en');

    // 翻訳元の言語（任意）
    // 指定しない場合は Google 側で自動判定される
    $source = getenv('GOOGLE_TRANSLATE_SOURCE') ?: null;


    // ===== Google 翻訳 API（v2）のエンドポイント =====
    $endpoint = 'https://translation.googleapis.com/language/translate/v2';


    // ===== APIに送信するパラメータ =====
    // q      : 翻訳したいテキスト
    // target : 翻訳先の言語
    // format : text（HTMLではなくプレーンテキスト）
    $postFields = [
      'q'      => $text,
      'target' => $target,
      'format' => 'text',
    ];

    // 翻訳元の言語が指定されている場合のみ追加
    if ($source !== null && $source !== '') {
      $postFields['source'] = $source;
    }


    // ===== APIキーをクエリパラメータとしてURLに付与 =====
    // rawurlencode：URLに使えない文字をエンコード
    $url = $endpoint . '?key=' . rawurlencode($apiKey);


    // ===== cURL 初期化 =====
    // cURL：PHPでHTTP通信を行うための仕組み
    $ch = curl_init($url);

    // cURLの各種オプション設定
    curl_setopt_array($ch, [
      CURLOPT_POST           => true,                         // POST通信
      CURLOPT_POSTFIELDS     => http_build_query($postFields), // POSTデータ
      CURLOPT_RETURNTRANSFER => true,                         // 結果を文字列で受け取る
      CURLOPT_TIMEOUT        => 10,                           // タイムアウト（秒）
    ]);


    // ===== API通信の実行 =====
    $raw   = curl_exec($ch);                  // レスポンス本体
    $errno = curl_errno($ch);                 // エラー番号
    $err   = curl_error($ch);                 // エラーメッセージ
    $http  = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE); // HTTPステータスコード

    // 通信終了
    curl_close($ch);


    // ===== 通信自体が失敗した場合 =====
    if ($raw === false) {
      throw new RuntimeException(
        "翻訳API通信に失敗しました（curl）: {$errno} {$err}"
      );
    }


    // ===== JSONを配列に変換 =====
    // true を指定すると連想配列として取得できる
    $json = json_decode($raw, true);

    if (!is_array($json)) {
      throw new RuntimeException('翻訳APIのレスポンスがJSONではありません');
    }


    // ===== API側のエラー判定 =====
    // 例：APIキー不正、Billing未設定、API未有効化など
    if ($http >= 400) {
      $message = $json['error']['message'] ?? '不明なエラー';
      throw new RuntimeException(
        "翻訳APIエラー（HTTP {$http}）: {$message}"
      );
    }


    // ===== 翻訳結果の取得 =====
    // data.translations[0].translatedText に翻訳結果が入っている
    $translated = $json['data']['translations'][0]['translatedText'] ?? null;

    if (!is_string($translated) || $translated === '') {
      throw new RuntimeException('翻訳結果が取得できませんでした');
    }


    // ===== 返却前の整形 =====
    // APIのレスポンスは HTMLエスケープされた文字列の場合があるため、
    // html_entity_decode で元の文字に戻してから返す
    return html_entity_decode(
      $translated,
      ENT_QUOTES | ENT_HTML5,
      'UTF-8'
    );
  }
}
