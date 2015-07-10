<?php
/************************************************************************
 * デバッグモード
 ***********************************************************************/
define('DEBUG', false);

/************************************************************************
 * サイト設定
 ***********************************************************************/
// 管理者メールアドレス
define('ADMIN_MAIL', 'info@alev.co.jp');

// システム管理者メールアドレス
define('SYSTEM_ADMIN_MAIL', 'info@alev.co.jp');

if (PHP_OS === 'WINNT' || PHP_OS === 'Darwin') {
    // ローカルホストフラグ
    define('LOCALHOST', true);

    // サイトURL
    define('SITE_URL', 'http://local.lpo.com/');
    define('SITE_SSL_URL', 'http://local.lpo.com/');

    // 公開領域ディレクトリ
    define('WWW_DIR', BASE . '/www');
} else {
    // ローカルホストフラグ
    define('LOCALHOST', false);

    // サイトURL
    define('SITE_URL', 'http://ts.marketing.io/');
    define('SITE_SSL_URL', 'http://ts.marketing.io/');

    // 公開領域ディレクトリ
    define('WWW_DIR', BASE . '/www');
}

// サイト名
define('SITE_NAME', 'LPOシステム');

// パスワード暗号化文字列
define('SALT', 'AaakZyH18AVPt7dHKv');

/************************************************************************
 * ディレクトリ設定
 ***********************************************************************/
// テンプレートディレクトリ
define('TEMPLATE_DIR', BASE . '/template/ja_JP');

// メールテンプレートディレクトリ
define('MAIL_TEMPLATE_DIR', TEMPLATE_DIR . '/mail/template');

/************************************************************************
 * エラーメッセージ
 ***********************************************************************/
define('ERR_MSG_NO_DATA', '該当する情報がありません。');


define('BASIC_CHARGE', 1080);       // 基本料金
define('SALES_RATE', 1.28);         // アトレ料率
define('NEW_ISSUANCE_FEE', 1000);   // 電話番号新規発行料金