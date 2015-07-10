<?php
/**
 *  Lpo_Controller.php
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @version    $Id: app.controller.php 500 2008-04-24 19:47:05Z mumumu-org $
 */
/** アプリケーションベースディレクトリ */
define('BASE', dirname(dirname(__FILE__)));

/** include_pathの設定(アプリケーションディレクトリを追加) */
$app = BASE . "/app";
$lib = BASE . "/lib";
$pear = BASE ."/PEAR";
ini_set('include_path', implode(PATH_SEPARATOR, array($app, $lib, $pear)) . PATH_SEPARATOR . ini_get('include_path'));

/** 設定ファイルのインクルード */
require_once 'pr.php';
require_once BASE . '/etc/define.php';
require_once BASE . '/etc/db_config.php';
require_once BASE . '/lib/DB.php';

/** アプリケーションライブラリのインクルード */
require_once 'Ethna/Ethna.php';
require_once 'Lpo_ActionClass.php';
require_once 'Lpo_ActionForm.php';
require_once 'Lpo_Error.php';
require_once 'Lpo_Session.php';
require_once 'Lpo_ViewClass.php';

/** PEARライブラリのインクルード */
require_once 'Pager/Pager.php';

// ライブラリの自動インクルード
$dirList = array('/lib/db_manager', '/lib/util');
foreach ($dirList as $dir) {
    $fList = glob(BASE . $dir . '/*.php');
    if (is_array($fList)) {
        foreach ($fList as $fPath) {
            require_once $fPath;
        }
    }
}

/**
 *  Lpoアプリケーションのコントローラ定義
 *
 *  @author     Alev Co., Ltd.
 *  @access     public
 *  @package    Lpo
 */
class Lpo_Controller extends Ethna_Controller
{
    /**#@+
     *  @access private
     */

    /**
     *  @var    string  アプリケーションID
     */
    var $appid = 'LPO';

    /**
     *  @var    array   forward定義
     */
    var $forward = array(
        /*
         *  TODO: ここにforward先を記述してください
         *
         *  記述例：
         *
         *  'index'         => array(
         *      'view_name' => 'Lpo_View_Index',
         *  ),
         */
    );

    /**
     *  @var    array   action定義
     */
    var $action = array(
        /*
         *  TODO: ここにaction定義を記述してください
         *
         *  記述例：
         *
         *  'index'     => array(),
         */
    );

    /**
     *  @var    array   soap action定義
     */
    var $soap_action = array(
        /*
         *  TODO: ここにSOAPアプリケーション用のaction定義を
         *  記述してください
         *  記述例：
         *
         *  'sample'            => array(),
         */
    );

    /**
     *  @var    array       アプリケーションディレクトリ
     */
    var $directory = array(
        'action'        => 'app/action',
        'action_cli'    => 'app/action_cli',
        'action_xmlrpc' => 'app/action_xmlrpc',
        'app'           => 'app',
        'plugin'        => 'app/plugin',
        'bin'           => 'bin',
        'etc'           => 'etc',
        'filter'        => 'app/filter',
        'locale'        => 'locale',
        'log'           => 'log',
        'plugins'       => array('lib/SmartyPlugins'),
        'template'      => 'template',
        'template_c'    => 'tmp',
        'tmp'           => 'tmp',
        'view'          => 'app/view',
        'www'           => 'www',
        'test'          => 'app/test',
    );

    /**
     *  @var    array       DBアクセス定義
     */
    var $db = array(
        ''              => DB_TYPE_RW,
    );

    /**
     *  @var    array       拡張子設定
     */
    var $ext = array(
        'php'           => 'php',
        'tpl'           => 'tpl',
    );

    /**
     *  @var    array   クラス定義
     */
    var $class = array(
        /*
         *  TODO: 設定クラス、ログクラス、SQLクラスをオーバーライド
         *  した場合は下記のクラス名を忘れずに変更してください
         */
        'class'         => 'Ethna_ClassFactory',
        'backend'       => 'Ethna_Backend',
        'config'        => 'Ethna_Config',
        'db'            => 'Ethna_DB_PEAR',
        'error'         => 'Ethna_ActionError',
        'form'          => 'Lpo_ActionForm',
        'i18n'          => 'Ethna_I18N',
        'logger'        => 'Ethna_Logger',
        'plugin'        => 'Ethna_Plugin',
        'session'       => 'Lpo_Session',
        'sql'           => 'Ethna_AppSQL',
        'view'          => 'Lpo_ViewClass',
        'renderer'      => 'Ethna_Renderer_Smarty',
        'url_handler'   => 'Lpo_UrlHandler',
    );

    /**
     *  @var    array       検索対象となるプラグインのアプリケーションIDのリスト
     */
    var $plugin_search_appids = array(
        /*
         *  プラグイン検索時に検索対象となるアプリケーションIDのリストを記述します。
         *
         *  記述例：
         *  Common_Plugin_Foo_Bar のような命名のプラグインがアプリケーションの
         *  プラグインディレクトリに存在する場合、以下のように指定すると
         *  Common_Plugin_Foo_Bar, Lpo_Plugin_Foo_Bar, Ethna_Plugin_Foo_Bar
         *  の順にプラグインが検索されます。 
         *
         *  'Common', 'Lpo', 'Ethna',
         */
        'Lpo', 'Ethna',
    );

    /**
     *  @var    array       フィルタ設定
     */
    var $filter = array(
        /*
         *  TODO: フィルタを利用する場合はここにそのプラグイン名を
         *  記述してください
         *  (クラス名を指定するとfilterディレクトリからフィルタクラス
         *  を読み込みます)
         *
         *  記述例：
         *
         *  'ExecutionTime',
         */
         //'ExecutionTime'
    );

    /**
     *  @var    array   smarty modifier定義
     */
    var $smarty_modifier_plugin = array(
        /*
         *  TODO: ここにユーザ定義のsmarty modifier一覧を記述してください
         *
         *  記述例：
         *
         *  'smarty_modifier_foo_bar',
         */
    );

    /**
     *  @var    array   smarty function定義
     */
    var $smarty_function_plugin = array(
        /*
         *  TODO: ここにユーザ定義のsmarty function一覧を記述してください
         *
         *  記述例：
         *
         *  'smarty_function_foo_bar',
         */
    );

    /**
     *  @var    array   smarty block定義
     */
    var $smarty_block_plugin = array(
        /*
         *  TODO: ここにユーザ定義のsmarty block一覧を記述してください
         *
         *  記述例：
         *
         *  'smarty_block_foo_bar',
         */
    );

    /**
     *  @var    array   smarty prefilter定義
     */
    var $smarty_prefilter_plugin = array(
        /*
         *  TODO: ここにユーザ定義のsmarty prefilter一覧を記述してください
         *
         *  記述例：
         *
         *  'smarty_prefilter_foo_bar',
         */
    );

    /**
     *  @var    array   smarty postfilter定義
     */
    var $smarty_postfilter_plugin = array(
        /*
         *  TODO: ここにユーザ定義のsmarty postfilter一覧を記述してください
         *
         *  記述例：
         *
         *  'smarty_postfilter_foo_bar',
         */
    );

    /**
     *  @var    array   smarty outputfilter定義
     */
    var $smarty_outputfilter_plugin = array(
        /*
         *  TODO: ここにユーザ定義のsmarty outputfilter一覧を記述してください
         *
         *  記述例：
         *
         *  'smarty_outputfilter_foo_bar',
         */
    );

    /**
     *  フォームにより要求されたアクション名を返す（オーバーライド）
     *
     *  アプリケーションの性質に応じてこのメソッドをオーバーライドして下さい。
     *  デフォルトでは"action_"で始まるフォーム値の"action_"の部分を除いたもの
     *  ("action_sample"なら"sample")がアクション名として扱われます
     *
     *  @access protected
     *  @return string  フォームにより要求されたアクション名
     */
    function _getActionName_Form()
    {
        $action_name = parent::_getActionName_Form();
        if (!$action_name && isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == "POST") {
            $http_vars = $_GET;
            foreach ($http_vars as $name => $value) {
                if ($value == "" || strncmp($name, 'action_', 7) != 0) {
                    continue;
                }
                $action_name = substr($name, 7);
            }
        }
        return $action_name;
    }
    /**#@-*/
}
?>
