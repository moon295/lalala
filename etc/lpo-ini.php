<?php
$date = date('Ymd');
/*
 * sof-ini.php
 *
 * update:
 */
$config = array(
    // site
    'url' => '/',

    // debug
    // (to enable ethna_info and ethna_unittest, turn this true)
    'debug' => DEBUG,

    // db
    // sample-1: single db
    // 'dsn' => 'mysql://user:password@server/database',
    //
    // sample-2: single db w/ multiple users
    // 'dsn'   => 'mysql://rw_user:password@server/database', // read-write
    // 'dsn_r' => 'mysql://ro_user:password@server/database', // read-only
    //
    // sample-3: multiple db (slaves)
    // 'dsn'   => 'mysql://rw_user:password@master/database', // read-write(master)
    // 'dsn_r' => array(
    //     'mysql://ro_user:password@slave1/database',         // read-only(slave)
    //     'mysql://ro_user:password@slave2/database',         // read-only(slave)
    // ),

    // log
    // sample-1: sigile facility
    'log_facility'          => 'echo',
    'log_level'             => 'warning',
    'log_option'            => 'pid,function,pos',
    'log_filter_do'         => '',
    'log_filter_ignore'     => 'Undefined index.*%%.*tpl',
    // sample-2: mulitple facility
    'log' => array(
        'echo'  => array(
            'level'         => 'warning',
        ),
        'file'  => array(
//            'level'         => 'notice',
            'level'         => 'warning',
            'file'          => BASE . "/log/{$date}_lpo.log",
            'mode'          => 0666,
        ),
        'alertmail'  => array(
            'level'         => 'error',
            'mailaddress'   => SYSTEM_ADMIN_MAIL,
        ),
    ),
    'sql'   => array(
        'file'        => BASE . "/log/{$date}_sql.log",
        'no_log_word' => '^SELECT',
    ),
    //'log_option'            => 'pid,function,pos',
    //'log_filter_do'         => '',
    //'log_filter_ignore'     => 'Undefined index.*%%.*tpl',

    // memcache
    // sample-1: single (or default) memcache
    // 'memcache_host' => 'localhost',
    // 'memcache_port' => 11211,
    // 'memcache_use_connect' => false,
    // 'memcache_retry' => 3,
    // 'memcache_timeout' => 3,
    //
    // sample-2: multiple memcache servers (distributing w/ namespace and ids)
    // 'memcache' => array(
    //     'namespace1' => array(
    //         0 => array(
    //             'memcache_host' => 'cache1.example.com',
    //             'memcache_port' => 11211,
    //         ),
    //         1 => array(
    //             'memcache_host' => 'cache2.example.com',
    //             'memcache_port' => 11211,
    //         ),
    //     ),
    // ),

    // csrf
    // 'csrf' => 'Session',

    /**
     * サイト固有設定
     */
    // JS用URL
    'js_url' => '//ts.marketing.io/lpo.js',
    // 一時ディレクトリ
    'tmp_dir' => array('dir' => WWW_DIR . '/tmp',
                       'url' => '/tmp',
                      ),
    // コピーライト
    'copyright' => '株式会社アトレ',
    // 作成年
    'created_year' => 2014,
    // サイト名
    'site_name' => 'LPOシステム',
    // 停止フラグ
    'suspend' => array(0 => '稼働',
                       1 => '停止',
                      ),
    // コンテンツ種別
    'contents_type' => array(1 => '画像登録',
                             2 => 'HTMLソース登録',
                            ),
    // リンク設定
    'image_target' => array(1 => 'リンク無',
                            2 => 'リンク有（別ウィンドウ）',
                            3 => 'リンク有',
                           ),
    // 切り替え条件
    'condition_ctg' => array(1 => 'リスティング',
                             2 => '参照元',
                             3 => 'キーワード',
                             4 => 'LPパラメータ',
                            ),
    // サイト（キーワード）
    'keyword_site' => array(1 => 'Yahoo',
                            2 => 'Google',
                            3 => 'Msn',
                           ),
    // サイト（リスティング広告）
    'listing_site' => array(4 => 'Yahooリスティング',
                            5 => 'Adwords',
                           ),
    // 一致
    'match' => array(1 => '完全一致',
                     2 => '部分一致',
                    ),
    // ドメイン
    'domain_match' => array(1 => 'ドメイン一致',
                            2 => 'URL完全一致',
                           ),
    // 検索期間
    'search_period' => array('all_period'    => '全期間',
                             'input'         => '日付指定',
                             'yesterday'     => '昨日',
                             'seven'         => '過去7日間',
                             'last_week'     => '先週(月～日)',
                             'last_week_day' => '先週の営業日(月～金)',
                             'this_month'    => '今月分',
                             'last_month'    => '先月分',
                            ),
    // 1サイト辺りの発番数上限
    'lmit_tel'  => 15,
);
