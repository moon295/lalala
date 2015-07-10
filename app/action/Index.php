<?php
/**
 *  Index.php
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @version    $Id: skel.action.php 387 2006-11-06 14:31:24Z cocoitiban $
 */

/**
 *  indexフォームの実装
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @access     public
 */
class Lpo_Form_Index extends Lpo_ActionForm
{
    /** @var    bool    バリデータにプラグインを使うフラグ */
    var $use_validator_plugin = false;

    /**
     *  @access   private
     *  @var      array   フォーム値定義
     */
     var $form = array(
        'key' => array(
            'form_type'     => FORM_TYPE_HIDDEN,
            'name'          => 'キー',
            'required'      => true,
            'regexp'        => '/^[0-9a-z]{10}$/',
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
        ),
        'referrer' => array(
            'form_type'     => FORM_TYPE_HIDDEN,
            'name'          => 'リファラー',
            'required'      => true,
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
        ),
        'url' => array(
            'form_type'     => FORM_TYPE_HIDDEN,
            'name'          => 'URL',
            'required'      => true,
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
        ),
        'enc' => array(
            'form_type'     => FORM_TYPE_HIDDEN,
            'name'          => 'エンコード',
            'required'      => true,
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
        ),
    );
}

/**
 *  indexアクションの実装
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @access     public
 */
class Lpo_Action_Index extends Lpo_ActionClass
{
    /**
     * @access protected
     * @var array  $loginCheckFlg  ログインチェックフラグ
     */
    var $loginCheckFlg = null;

    /**
     * @access public
     * @var const  AdWords用パラメーター
     */
    const ADWORDS_PARAM = 'ltadwrs=true';

    /**
     * @access public
     * @var const  Yahooリスティング用パラメーター
     */
    const YAHOO_PARAM   = 'ltovtre=true';

    /**
     * @access public
     * @var string $logFilePath  ログファイル格納パス
     */
    var $logFilePath = '';

    var $log = array();

    /**
     *  indexアクションの前処理
     *
     *  @access public
     *  @return string      遷移名(正常終了ならnull, 処理終了ならfalse)
     */
    function prepare()
    {
        // ログファイルパス生成
        $this->logFilePath = sprintf('%s/log/lpo-%s.log', BASE, date('Ymd'));
        if ($this->af->validate()) {
            $form = $this->af->getArray(false);
            $this->saveLog($form, implode("\n", $this->ae->getMessageList()));
            header('Content-type:text/javascript; charset=UTF-8');
            exit();
        }
        return null;
    }

    /**
     *  indexアクションの実装
     *
     *  @access public
     *  @return string  遷移名
     */
    function perform()
    {
        $form = $this->af->getArray(false);
        $form['referrer'] = rawurldecode($form['referrer']);
        $form['url']      = rawurldecode($form['url']);

        try {
            $urlParts = parse_url($form['url']);
            $search = array('key'         => $form['key'],
                            'url'         => $urlParts['host'],
                            'suspend_flg' => 0,
                           );
            $targetManager = new TargetManager();
            $target = $targetManager->getFront($search);
            if (!$target) {
                throw new Exception('切り替え対象かサイトが存在しないか、停止中です。[Key]');
            }
/*
            $search = array('id'          => $target['site_id'],
                            'url'         => $urlParts['host'],
                            'suspend_flg' => 0,
                           );
            $siteManager = new SiteManager();
            $site = $siteManager->getFront($search);
            if (!$site) {
                throw new Exception('サイトが存在しないか、停止中です。[URL]');
            }
*/
            $search = array('target_id'   => $target['id'],
                            'suspend_flg' => 0,
                           );
            $conditionManager = new ConditionManager();
            $conditions = $conditionManager->getListFront($search);
            if (!$conditions) {
                throw new Exception('切り替え条件が存在しません。');
            }
            $matchCondition = array();
            // リファラーURL分解
            $referrerParts = parse_url($form['referrer']);
            if (isset($referrerParts['query'])) {
                 parse_str($referrerParts['query'], $referrerQuery);
            }

            // 切り替え条件並び替え
            $bufConditions = array();
            foreach ($conditions as $condition) {
                if (!isset($bufCondition[$condition['condition_ctg']])) {
                    $bufCondition[$condition['condition_ctg']] = array();
                }
                $bufCondition[$condition['condition_ctg']][] = $condition;
            }
            // 1.LPパラメータ
            // 2.リスティング
            // 3.参照元チェック
            // 4.キーワード
            $sort = array(4, 1, 2, 3);
            $conditions = array();
            foreach ($sort as $val) {
                if (isset($bufCondition[$val])) {
                    $conditions = array_merge($conditions, $bufCondition[$val]);
                }
            }

            // 切り替え条件一致確認処理
            foreach ($conditions as $condition) {
                switch ($condition['condition_ctg']) {
                    case 1:         // リスティング
                        $this->log[] = '■リスティング';
                        if ($this->isSite($condition['site_id'], $referrerParts['query'])) {
                            // サイトチェック一致の場合
                            $queryKeyword = '';
                            if ((int)$condition['site_id'] === 4) {
                                // Yahooリスティング
                                $queryKeyword = $referrerQuery['p'];
                            } else if ((int)$condition['site_id'] === 5) {
                                // Google AdWords
                                $queryKeyword = $referrerQuery['q'];
                            }
                            if (!$this->isMatch($condition['disable_keywords'], $condition['disable_match_all_flg'], $queryKeyword, true) &&
                                (!$condition['enable_keywords'] || $this->isMatch($condition['enable_keywords'], $condition['enable_match_all_flg'], $queryKeyword))) {
                                // 拒否キーワードチェック不一致でキーワード一致の場合
                                $matchCondition = $condition;
                            }
                        }
                        break;
                    case 2:         // 参照元チェック
                        $this->log[] = '■参照元チェック';
                        if ($this->isDomain($condition['from_urls'], $condition['urls_match_all_flg'], $form['referrer'], $referrerParts) ||
                            $this->isMatch($condition['from_params'], $condition['params_match_all_flg'], $referrerParts['query'])) {
                            // URL一致かパラメータ一致の場合
                            $matchCondition = $condition;
                        }
                        break;
                    case 3:         // キーワード
                        $this->log[] = '■キーワード';
                        if (!strstr($referrerParts['query'], self::ADWORDS_PARAM) && !strstr($referrerParts['query'], self::YAHOO_PARAM)) {
                            // サイトチェック
                            if ($this->isSite($condition['site_id'], $referrerParts['host'])) {
                                if ((int)$condition['site_id'] === 2) {
                                    // Googleの場合、クエリ文字列が取得できないためキーワード一致処理を無視する
                                    $matchCondition = $condition;
                                } else {
                                    $queryKeyword = '';
                                    if ((int)$condition['site_id'] === 1) {
                                        // Yahoo
                                        $queryKeyword = $referrerQuery['p'];
                                    } else if ((int)$condition['site_id'] === 3) {
                                        // MSN
                                        $queryKeyword = $referrerQuery['q'];
                                    }
                                    if (!$this->isMatch($condition['disable_keywords'], $condition['disable_match_all_flg'], $queryKeyword, true) &&
                                        (!$condition['enable_keywords'] || $this->isMatch($condition['enable_keywords'], $condition['enable_match_all_flg'], $queryKeyword))) {
                                        // 拒否キーワードチェック不一致でキーワード一致の場合
                                        if (!$condition['enable_keywords']) {
                                            $this->log[] = '└ [○]キーワード登録なし：キーワード空';
                                        }
                                        $matchCondition = $condition;
                                    }
                                }
                            }
                        } else {
                            $this->log[] = '└ [×]リスティングのため無視';
                        }
                        break;
                    case 4:         // LPパラメーターチェック
                        $this->log[] = '■LPパラメーターチェック';
                        $urlParts = parse_url($form['url']);
                        if ($this->isMatch($condition['from_params'], $condition['params_match_all_flg'], $urlParts['query']) ||
                            $this->isMatch($condition['from_params'], $condition['params_match_all_flg'], $referrerParts['query'])
                           ) {
                            // パラメータ一致の場合
                            $matchCondition = $condition;
                        }
                        break;
                }
                if ($matchCondition) {
                    if ($this->log) {
                        // ログ保存
                        $this->log[] = '└ LPO実行';
                        $this->saveLog($form, implode("\n", $this->log));
                    }
                    // 切り替え条件一致の場合
                    $replace = '';
                    switch ($matchCondition['contents_type']) {
                        case 1:     // 画像
                            $replace = sprintf('<img src="%s" border="0">', $matchCondition['image_url']);
                            if ((int)$matchCondition['image_target'] === 2) {
                                $replace = sprintf('<a href="%s" target="_blank">%s</a>', $matchCondition['image_link_url'], $replace);
                            } else if ((int)$matchCondition['image_target'] === 3) {
                                $replace = sprintf('<a href="%s" target="_top">%s</a>', $matchCondition['image_link_url'], $replace);
                            }
                            break;
                        case 2:     // HTML
                            $matchCondition['html_tag'] = preg_replace("/\r|\n/", "", $matchCondition['html_tag']);
                            $enc = strtolower($form['enc']);
                            if ($enc === 'utf-8') {
                                $replace = $matchCondition['html_tag'];
                            } else if ($enc === 'euc-jp') {
                                $replace = mb_convert_encoding($matchCondition['html_tag'], 'EUC-JP', 'UTF-8');
                            } else if ($enc === 'shift_jis') {
                                $replace = mb_convert_encoding($matchCondition['html_tag'], 'SJIS', 'UTF-8');
                            }
                            break;
                    }

                    // アクセスログ保存
                    $logPath = sprintf('%s/log/access/%s/%07d.log', BASE, date('Y-m-d'), $matchCondition['id']);
                    $logDirPath = dirname($logPath);
                    if (!is_dir($logDirPath)) {
                        mkdir($logDirPath, 0777);
                        chmod($logDirPath, 0777);
                    }
                    $access = is_file($logPath) ? file_get_contents($logPath) : 0;
                    $access++;
                    file_put_contents($logPath, $access);

                    header('Content-type:text/javascript; charset=' . $form['enc']);
                    printf('document.getElementById("%s").innerHTML = "%s";', $form['key'], addslashes($replace));
                    exit();
                }
            }
        } catch (Exception $e) {
            // ログ保存
            $this->saveLog($form, $e->getMessage());
            header('Content-type:text/javascript; charset=' . $form['enc']);
            exit();
        }
        // ログ保存
        $this->saveLog($form, implode("\n", $this->log));
        header('Content-type:text/javascript; charset=' . $form['enc']);
        exit();
    }

    /**
     * ログ保存
     *
     * @access public
     * @param  string  $log  ログ情報
     * @return void
     */
    function saveLog($form, $log)
    {
        $log = sprintf("%s\nIP:%s\nUA:%s\nKey:%s\nUrl:%s\nReferrer:%s\n%s\n-------------------\n",
                       date('Y-m-d H:i:s'),
                       $_SERVER['REMOTE_ADDR'],
                       $_SERVER['HTTP_USER_AGENT'],
                       $form['key'],
                       $form['url'],
                       $form['referrer'],
                       $log
                      );
        file_put_contents($this->logFilePath, $log, FILE_APPEND);
    }

    /**
     * サイト確認
     *
     * @access public
     * @param  integer   $siteId  サイトID
     * @param  string    $value   ホスト名 / 検索クエリ
     * @return boolean  true:一致 / false:不一致
     */
    function isSite($siteId, $value)
    {
        if ($siteId < 4) {
            $host = explode('.', $value);
            $lastNo = count($host) - 1;
            $arrNo1 = $lastNo - 1;
            $arrNo2 = $lastNo - 2;
        }
        switch ($siteId) {
            case 1:     // Yahoo
                if (isset($host[$arrNo1]) && $host[$arrNo1] === 'yahoo' || isset($host[$arrNo2]) && $host[$arrNo2] === 'yahoo') {
                    $this->log[] = '└ [○]サイト一致：Yahoo';
                    return true;
                }
                break;
            case 2:     // Google
                if (isset($host[$arrNo1]) && $host[$arrNo1] === 'google' || isset($host[$arrNo2]) && $host[$arrNo2] === 'google') {
                    $this->log[] = '└ [○]サイト一致：Google';
                    return true;
                }
                break;
            case 3:     // MSN
                if (isset($host[$arrNo1]) && $host[$arrNo1] === 'bing' || isset($host[$arrNo2]) && $host[$arrNo2] === 'bing') {
                    $this->log[] = '└ [○]サイト一致：MSN';
                    return true;
                }
                break;
            case 4:     // Yahooリスティング
                if (strstr($value, self::YAHOO_PARAM)) {
                    $this->log[] = '└ [○]サイト一致：Yahooリスティング';
                    return true;
                }
                break;
            case 5:     // AdWords
                if (strstr($value, self::ADWORDS_PARAM)) {
                    $this->log[] = '└ [○]サイト一致：AdWords';
                    return true;
                }
                break;
        }
        $siteName = array(1 => 'Yahoo', 2 => 'Google', 3 => 'MSN', 4 => 'Yahooリスティング', 5 => 'AdWords');
        $this->log[] = '└ [×]サイト不一致：' . $siteName[$siteId];
        return false;
    }

    /**
     * 条件一致確認
     *
     * @access public
     * @param  string   $keywordList  キーワードリスト
     * @param  integer  $matchFlg     1:完全一致 / 2:部分一致
     * @param  string   $compKeyword  比較用キーワード
     * @return boolean  $disable      false:キーワード / true:拒否キーワード
     */
    function isMatch($keywordList, $matchFlg, $compKeyword, $disable = false)
    {
        $nomatch = $disable ? '拒否キーワード' : 'キーワード';
        if ($keywordList) {
            $keywords = explode("\n", $keywordList);
            foreach ($keywords as $keyword) {
                $keyword = trim($keyword);
                switch ($matchFlg) {
                    case 1:     // 完全一致
                        if ($compKeyword === $keyword) {
                            // 一致条件あり
                            $this->log[] = '└ [○]' . $nomatch . '完全一致：' . $keyword;
                            return true;
                        } else {
                            $this->log[] = '└ [×]' . $nomatch . '完全一致：' . $keyword;
                        }
                        break;
                    case 2:     // 部分一致
                        if (strstr($compKeyword, $keyword)) {
                            // 一致条件あり
                            $this->log[] = '└ [○]' . $nomatch . '部分一致：' . $keyword;
                            return true;
                        } else {
                            $this->log[] = '└ [×]' . $nomatch . '部分一致：' . $keyword;
                        }
                        break;
                }
            }
        }
        return false;
    }

    /**
     * ドメイン一致確認
     *
     * @access public
     * @param  string   $urls           URL一覧
     * @param  integer  $matchFlg       1:ドメイン一致 / 2:URL完全一致
     * @param  string   $referrer       リファラー
     * @param  array    $referrerParts  リファラー分解配列
     * @return boolean  true:一致 / false:不一致
     */
    function isDomain($urls, $matchFlg, $referrer, $referrerParts)
    {
        if ($urls) {
            $fromUrls = explode("\n", $urls);
            foreach ($fromUrls as $url) {
                $url = trim($url);
                $fromUrlParts = parse_url($url);
                switch ($matchFlg) {
                    case 1:     // ドメイン一致
                        if ($referrerParts['host'] === $fromUrlParts['host']) {
                            $this->log[] = '└ [○]ドメイン一致：' . $fromUrlParts['host'];
                            return true;
                        } else {
                            $this->log[] = '└ [×]ドメイン一致：' . $fromUrlParts['host'];
                        }
                        break;
                    case 2:     // URL完全一致
                        if ($referrer === $url) {
                            $this->log[] = '└ [○]URL完全一致：' . $url;
                            return true;
                        } else {
                            $this->log[] = '└ [×]URL完全一致：' . $url;
                        }
                        break;
                }
            }
        }
        return false;
    }
}
?>
