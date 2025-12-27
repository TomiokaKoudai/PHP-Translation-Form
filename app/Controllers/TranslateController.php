<?php

declare(strict_types=1); //厳密な型付け

require_once __DIR__ . '/../Services/TranslateService.php'; //ファイルの読み込み

// クラスの作成
class TranslateController
{
  // indexメソッドの作成
  // void：戻り値の型を何も返さないメソッドという宣言
  public function index(): void
  {
    // 変数の宣言
    $translatedText = '';
    $errors = []; // ← $error → $errors に統一
    $text = '';

    // POSTリクエストが来た場合の処理
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

      // CSRFチェック
      // hash_equals：秘密情報を含む文字列の比較をする関数
      // 第一引数と第二引数を内部で比較し一致すればtrue、不一致ならfalse
      // ??：NULL合体演算子。左側がNULLまたは未定義の場合右の値を使う。
      if (!hash_equals($_SESSION['token'], $_POST['token'] ?? '')) {
        // 強制終了
        exit("不正なリクエストです。");
      }

      // 異常がなければ変数textに送られてきたtextデータの前後の余白を削除し格納
      // なければ空
      $text = trim($_POST['text'] ?? '');

      // バリデーション（最小）
      if ($text === '') {
        $errors[] = '翻訳する文字を入力してください';
      }
      if (mb_strlen($text) > 500) {
        $errors[] = '500文字以内で入力してください';
      }

      // エラーが無い場合のみ翻訳
      if (empty($errors)) {
        try {
          $service = new TranslateService();
          $translatedText = $service->translate($text);
        } catch (Throwable $e) {
          // 翻訳APIの失敗・通信エラーなどをここで捕捉
          $errors[] = '翻訳に失敗しました。しばらくしてから再度お試しください。';

          // デバッグ用（本番では表示しない）
          // $errors[] = $e->getMessage();
        }
      }
    }

    // ファイルの読み込み
    require_once __DIR__ . '/../../views/translate.php';
  }
}
