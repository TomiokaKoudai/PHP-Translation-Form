# PHP Translation Tool

生PHPで作成した翻訳フォームアプリです。
Google Cloud Translation API を使用して、日本語を英語に翻訳します。

---

## 概要
- フレームワークを使わずに、生PHPで構成
- MVC風構成（Controller / Service / View）
- Google翻訳API（v2）を使用
- .env によるAPIキー管理

---

## 使用技術
- PHP 8.x
- Google Cloud Translation API（Basic / v2）
- CURL
- HTML

---

## ディレクトリ構成
translation-tool/
├─ public/
│  └─ index.php
├─ app/
│  ├─ Controllers/
│  │  └─ TranslateController.php
│  └─ Services/
│     └─ TranslateService.php
├─ views/
│  └─ translate.php
├─ config/
│  └─ config.php
├─ .env
└─ README.md
