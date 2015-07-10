<?php
/**
 *  lib/util/PagerUtil.php
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @version   $Id: app.action.default.php 387 2006-11-06 14:31:24Z cocoitiban $
*/

/**
 *  PagerUtilクラス
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @access     public
 */
class PagerUtil
{
    /**
     *  @access public
     *  @var  integer  全件数
     */
    static $allCount;

    /**
     *  @access public
     *  @var  object  Ethnaオブジェクト
     */
    static $obj = null;

    /**
     *  ページャ一覧情報を取得します。
     *
     *  @access public
     *  @param  object   $obj            Ethnaオブジェクト
     *  @param  string   $sessName       セッション名
     *  @param  integer  $dispNum        表示件数
     *  @param  string   $dbManagerName  取得するDBマネージャ名
     *  @param  string   $methodName     取得すするDBメソッド名
     *  @param  array    $search         検索条件配列
     *  @param  string   $url            ページャURLベース文字列（nullの場合は、第2引数の$sessNameから自動生成）
     *  @return array  ページャ配列
     */
    function getPager(&$obj, &$search, $dispNum, $sessName, $dbManagerName, $methodName, $url = null)
    {
        self::$obj = $obj;
        // 一覧取得
        $session = $obj->session->get($sessName);
        if ($obj->session->get($sessName . '.current_page')) {
            $currentPage = $obj->session->get($sessName . '.current_page');
        } else if (!empty($search['current_page']) && isset($session)) {
            $currentPage = $search['current_page'];
            $search = $session;
        } else {
            $obj->session->set($sessName . '.current_page', 1);
            $currentPage = 1;
        }

        if (!is_null($dispNum)) {
            $search['offset'] = ($currentPage - 1) * $dispNum;
            if ($search['offset'] < 0) {
                $search['offset'] = 0;
            }
            $search['limit']  = $dispNum;
        }

        // 検索条件フォーム値のs_を取り除く
        if (is_array($search)) {
            foreach ($search as $key => $value) {
                if (preg_match('/^s_/', $key)) {
                    $key = preg_replace('/^s_/', '', $key);
                    $search[$key] = $value;
                }
            }
        }

        $dbManager = new $dbManagerName();
        $list = $dbManager->{$methodName}($search, 'SQL_CALC_FOUND_ROWS');

        // 全件数取得
        $rows = $dbManager->getRows();
        $allCount = $rows;
        self::$allCount = $allCount;
        if (is_null($url)) {
            $url = sprintf('/%s/%%d/', preg_replace('/_/', '/', $sessName));
        }

        $customParams = array();
        if ($obj->af->get('mobile') === 'mobile') {
            $customParams = array('delta' => 2);
        }

        $data = array('list'  => $list,
                      'pager' => PagerUtil::getPagerInfo($currentPage, $dispNum, $allCount, $url, null, $url, $customParams)
                     );
        if (count($data['list']) === 0 && $currentPage > 1) {
            // 取得データがない場合、ページャから取得したoffset,limitで再度検索する
            $search['offset'] = $data['pager']['offset'];
            if ($search['offset'] < 0) {
                $search['offset'] = 0;
            }
            $search['limit']  = $dispNum;
            $data['list']  = $dbManager->{$methodName}($search);
        }
        return $data;
    }

    /**
     *  ページャ情報を取得します。
     *
     *  @access public
     *  @param  integer  $currentPage  現在のページ番号
     *  @param  integer  $dispNum      表示件数
     *  @param  integer  $allCount     全件数
     *  @param  string   $fileName     ページャファイル名
     *  @param  array    $itemData     データ配列（$allCountはtotalItems / $itemDataはitemData）
     *  @param  string   $url          ページャURLベース文字列
     *  @return array  ページャ配列
     */
    function getPagerInfo($currentPage, $dispNum, $allCount, $fileName = null, $itemData = null, $url = null, $customParams = array())
    {
        $delta = 5;
        if (self::$obj !== null && self::$obj->af->get('mobile') === 'smartphone') {
            $delta = 1;
        }
        // ページャの設定
        $params = array(
            'mode'                  => 'Sliding',
            'altPrev'               => '前へ',
            'prevImg'               => '<i class="icon-angle-left"></i>',
            'altNext'               => '次へ',
            'nextImg'               => '<i class="icon-angle-right"></i>',
            'separator'             => '',
            'altFirst'              => '最初へ',
            'firstPageText'         => '<i class="icon-double-angle-left"></i>',
            'altLast'               => '最後へ',
            'lastPageText'          => '<i class="icon-double-angle-right"></i>',
            'curPageSpanPre'        => '<li class="active">',
            'curPageSpanPost'       => '</li>',
            'firstPagePre'          => '',
            'lastPagePre'           => '',
            'firstPagePost'         => '',
            'lastPagePost'          => '',
            'currentPage'           => $currentPage,
            'perPage'               => $dispNum,
            'delta'                 => $delta,
            'urlVar'                => 'current_page',
            'spacesBeforeSeparator' => 1,
            'spacesAfterSeparator'  => 1,
            'totalItems'            => $allCount,
            'httpMethod'            => 'GET',
        );
        if (isset($customParams) && count($customParams) > 0) {
            foreach ($customParams as $key => $value) {
                $params[$key] = $value;
            }
        }
        if (!is_null($itemData)) {
            unset($prams['totalItems']);
            $params['itemData'] = $itemData;
            $allCount = count($itemData);
        }

        if (!is_null($fileName)) {
            $params['fileName'] = $fileName;
            $params['append']   = false;
        }

        $navi = '%3$s 件中 %1$s - %2$s件を表示';
        $pager = & Pager::factory($params);
        list($from, $to) = $pager->getOffsetByPageId();
        $links = $pager->getLinks();
        if ($to == 0) {
            $from = 0;
        }
        $pagerInfo = array('links'     => $links['all'],
                           'navi'      => sprintf($navi, number_format($from), number_format($to), number_format($allCount)),
                           'all_count' => $allCount,
                           'offset'    => $from - 1,
                           'limit'     => $to - $from + 1
                          );
        if (!is_null($itemData)) {
            $pagerInfo['dates'] = $pager->getPageData();
        }
        $pagerInfo['links'] = str_replace('&nbsp;', '', $pagerInfo['links']);
        $pagerInfo['links'] = str_replace('<a', '<li><a', $pagerInfo['links']);
        $pagerInfo['links'] = str_replace('</a>', '</a></li>', $pagerInfo['links']);
        $pagerInfo['links'] = preg_replace('/<li( class="active")>([0-9]+)<\/li>/', '<li$1><a href="#">$2</a></li>', $pagerInfo['links']);
        return $pagerInfo;
    }

    /**
     *  PCページャリンクの整形
     *
     *  @access public
     *  @param  string  $links  リンク整形元文字列
     *  @param  string  $url    ベースURL
     *  @return string  整形後のページャリンク
     */
    function formatPc($links, $url)
    {
        $pager = array();
        $list = explode('&nbsp;&nbsp;', $links);
        foreach ($list as $value) {
            if (strlen(trim($value)) > 0) {
                if (strstr($value, 'strong')) {
                    $no = strip_tags($value);
                    $pager[] = sprintf("<li><span>%d</span></li>\n", $no, $no);
                } else {
                    if (preg_match('/.+href="\/([0-9]+)".+>(.+)<\/a>/', $value, $m)) {
                        if (count($m) === 3) {
                            if (is_numeric($m[2])) {
                                if (strstr($value, 'last page')) {
                                    $pager[] = sprintf("<li><a href=\"{$url}\">%s</a></li>\n", $m[1], $m[1]);
                                } else {
                                    $pager[] = sprintf("<li><a href=\"{$url}\">%s</a></li>\n", $m[1], $m[1]);
                                }
                            } else if ($m[2] === '次へ →') {
                                $pager[] = sprintf("<li class=\"nextLink\"><a href=\"{$url}\">Next</a></li>\n", $m[1]);
                            } else if ($m[2] === '← 戻る') {
                                $pager[] = sprintf("<li class=\"prevLink\"><a href=\"{$url}\">Prev</a></li>\n", $m[1]);
                            }
                        }
                    }
                    
                }
            }
        }
        return implode('', $pager);
    }

    /**
     *  スマートフォンページャリンクの整形
     *
     *  @access public
     *  @param  string  $links  リンク整形元文字列
     *  @param  string  $url    ベースURL
     *  @return string  整形後のページャリンク
     */
    function formatSmartPhone($links, $url)
    {
        $pager = array();
        $list = explode('&nbsp;&nbsp;', $links);
        foreach ($list as $value) {
            if (strlen(trim($value)) > 0) {
                if (strstr($value, 'strong')) {
                    $no = strip_tags($value);
                    $pager[] = sprintf("<li><span>%d</span></li>", $no, $no);
                } else {
                    if (preg_match('/.+href="\/([0-9]+)".+>(.+)<\/a>/', $value, $m)) {
                        if (count($m) === 3) {
                            if (is_numeric($m[2])) {
                                if (strstr($value, 'last page')) {
                                    $pager[] = sprintf("<li><a href=\"{$url}\">%s</a></li>", $m[1], $m[1]);
                                } else {
                                    $pager[] = sprintf("<li><a href=\"{$url}\">%s</a></li>", $m[1], $m[1]);
                                }
                            } else if ($m[2] === '次へ →') {
                                $pager[] = sprintf("<li class=\"nextLink\"><a href=\"{$url}\">Next</a></li>", $m[1]);
                            } else if ($m[2] === '← 戻る') {
                                $pager[] = sprintf("<li class=\"nextLink\"><a href=\"{$url}\">Prev</a></li>", $m[1]);
                            }
                        }
                    }
                    
                }
            }
        }
        return implode('', $pager);
    }

    /**
     *  モバイルページャリンクの整形
     *
     *  @access public
     *  @param  string  $links  リンク整形元文字列
     *  @param  string  $url    ベースURL
     *  @return string  整形後のページャリンク
     */
    function formatMobilePhone($links, $url, $color = '#000')
    {
        $pager = array();
        $list = explode('&nbsp;&nbsp;', $links);
        foreach ($list as $value) {
            if (strlen(trim($value)) > 0) {
                if (strstr($value, 'strong')) {
                    $no = strip_tags($value);
                    $pager[] = sprintf("<a style=\"color:{$color};\" href=\"{$url}\"><strong>%d</strong></a>&nbsp;", $no, $no);
                } else {
                    if (preg_match('/.+href="\/([0-9]+)".+>(.+)<\/a>/', $value, $m)) {
                        if (count($m) === 3) {
                            if (is_numeric($m[2])) {
                                if (strstr($value, 'last page')) {
                                    $pager[] = sprintf("&nbsp;<a style=\"color:{$color};\" href=\"{$url}\">%s</a>", $m[1], $m[1]);
                                } else if (strstr($value, 'first page')) {
                                    $pager[] = sprintf("<a style=\"color:{$color};\" href=\"{$url}\">%s</a>&nbsp;", $m[1], $m[1]);
                                } else {
                                    $pager[] = sprintf("<a style=\"color:{$color};\" href=\"{$url}\">%s</a>&nbsp;", $m[1], $m[1]);
                                }
                            } else if ($m[2] === '次へ →') {
                                $pager[] = sprintf("<a style=\"color:{$color};\" href=\"{$url}\">next&gt;</a>", $m[1]);
                            } else if ($m[2] === '← 戻る') {
                                $pager[] = sprintf("<a style=\"color:{$color};\" href=\"{$url}\">&lt;prev</a>&nbsp;", $m[1]);
                            }
                        }
                    }
                    
                }
            }
        }
        return implode('', $pager);
    }

    /**
     *  全件数を取得
     *
     *  @access  public
     *  @return  integer  全件数
     */
    function getAllCount()
    {
        return self::$allCount;
    }
}
?>
