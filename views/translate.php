<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>翻訳フォーム</title>
</head>

<body>
  <h1>翻訳フォーム</h1>
  <?php if (!empty($errors)): ?>
    <ul style="color:red;">
      <?php foreach ($errors as $e): ?>
        <li><?php echo htmlspecialchars($e, ENT_QUOTES, 'UTF-8'); ?></li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>


  <form method="POST">
    <textarea name="text" rows="6" cols="50"><?php
      echo htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
    
    ?></textarea>

    <br><br>

    <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>">

    <button type="submit">翻訳</button>
  </form>

  <?php if ($translatedText): ?>
    <h2>翻訳結果</h2>
    <p><?php echo $translatedText; ?></p>
  <?php endif; ?>

</body>

</html>