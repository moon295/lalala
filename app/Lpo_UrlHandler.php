<?php
/**
 *  Lpo_UrlHandler.php
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @version    $Id: app.url_handler.php 470 2007-07-08 17:48:26Z ichii386 $
 */

/**
 *  URLハンドラクラス
 *
 *  @author     Alev Co., Ltd.
 *  @access     public
 *  @package    Lpo
 */
class Lpo_UrlHandler extends Ethna_UrlHandler
{
    /** @var    array   アクションマッピング */
    var $action_map = array(
        /*
        'user'  => array(
            'user_login' => array(
                'path'          => 'login',
                'path_regexp'   => false,
                'path_ext'      => false,
                'option'        => array(),
            ),
        ),
         */
    );

    /**
     *  Lpo_UrlHandlerクラスのインスタンスを取得する
     *
     *  @access public
     */
    function &getInstance($class_name = null)
    {
        $instance =& parent::getInstance(__CLASS__);
        return $instance;
    }

    // {{{ ゲートウェイリクエスト正規化
    /**
     *  リクエスト正規化(userゲートウェイ)
     *
     *  @access private
     */
    /*
    function _normalizeRequest_User($http_vars)
    {
        return $http_vars;
    }
     */
    // }}}

    // {{{ ゲートウェイパス生成
    /**
     *  パス生成(userゲートウェイ)
     *
     *  @access private
     */
    /*
    function _getPath_User($action, $param)
    {
        return array("/user", array());
    }
     */
    // }}}

    // {{{ フィルタ
    // }}}
}

// vim: foldmethod=marker tabstop=4 shiftwidth=4 autoindent
?>
