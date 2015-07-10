<?php
// vim: foldmethod=marker
/**
 *  Lpo_ActionClass.php
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @version    $Id: app.actionclass.php 323 2006-08-22 15:52:26Z fujimoto $
 */

// {{{ Lpo_ActionClass
/**
 *  action実行クラス
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @access     public
 */
class Lpo_ActionClass extends Ethna_ActionClass
{
    /**
     *  @access public
     *  @var    string  現在のアクション名
     */
    var $actionName;

    /**
     *  @access public
     *  @var    string  現在の親アクション名
     */
    var $parentActionName;

    /**
     *  @access public
     *  @var    string  現在のセッション名
     */
    var $sessionName;

    /**
     *  @access public
     *  @var    string  現在の親セッション名
     */
    var $parentSessionName;

    /**
     * @access protected
     * @var    boolean  ログインチェックフラグ（true:チェックあり / false:チェックなし / null:無視）
     */
    var $loginCheckFlg = true;

    /**
     *  アクション実行前の認証処理を行う
     *
     *  @access public
     *  @return string  遷移名(nullなら正常終了, falseなら処理終了)
     */
    function authenticate()
    {
        // バージョンによってテンプレートパスを変更
        if (substr(ETHNA_VERSION, 0, 3) === '2.3') {
            // 2.3系の場合は2.5系のパスに変更
            $this->backend->controller->directory_default['template'] .= '/ja_JP';
            $this->backend->controller->directory['template'] = TEMPLATE_DIR;
        }
        // 現在実行中のアクションを設定
        $actionName = $this->backend->ctl->getCurrentActionName();

        $this->actionName        = $actionName;
        $this->parentActionName  = preg_replace('/_[^_]+$/', '', $actionName);
        $this->originActionName  = preg_replace('/_[^_]+$/', '', $this->parentActionName);
        $this->sessionName       = preg_replace('/_/', '.', $actionName);
        $this->parentSessionName = preg_replace('/_/', '.', $this->parentActionName);
        $this->originSessionName = preg_replace('/_/', '.', $this->originActionName);

        $this->af->setAppNE('action_name', $actionName);
        $this->af->setAppNE('parent_action_name', $this->parentActionName);
        $this->af->setAppNE('origin_action_name', $this->originActionName);

        // セッションスタート
        $this->session->start();
        // クロスサイトリクエストフォージェリ対策(ID発行)
        Ethna_Util::setCsrfID();

        // ログインチェック
        if (!is_null($this->loginCheckFlg)) {
            $actionPath = explode('_', $actionName);
            $login = $this->isLogin($actionPath[0]);
            if ($this->loginCheckFlg && !$login) {
                // ログイン機能で非ログイン時
                switch ($actionPath[0]) {
                    case 'customer':
                        return 'customer_login_input';
                        break;
                    case 'admin':
                        return 'admin_login_input';
                        break;
                }
            } else if (!$this->loginCheckFlg && $login) {
                // 非ログイン機能でログイン時利用不可機能時
                switch ($actionPath[0]) {
                    case 'customer':
                        header('Location: ' . SITE_SSL_URL . 'customer/');
                        exit();
                        break;
                    case 'admin':
                        return 'admin_index';
                        break;
                }
            }
        }
        return parent::authenticate();
    }

    /**
     *  アクション実行前の処理(フォーム値チェック等)を行う
     *
     *  @access public
     *  @return string  遷移名(nullなら正常終了, falseなら処理終了)
     */
    function prepare()
    {
        return parent::prepare();
    }

    /**
     *  アクション実行
     *
     *  @access public
     *  @return string  遷移名(nullなら遷移は行わない)
     */
    function perform()
    {
        return parent::perform();
    }

    /**
     * ロケーション
     *
     *  @access public
     *  @param  string  $url  ロケーション先URL
     *  @return void
     */
    function location($url)
    {
        header('Location:' . $url);
        exit();
    }

    /**
     * JSON出力
     *
     *  @access public
     *  @return array  JSON生成配列
     */
    function outputJson($data)
    {
        header("Content-Type: text/plain; charset=utf-8");
        print json_encode($data);
        exit();
    }

    /**
     * 一覧表示件数の取得
     *
     *  @access public
     *  @param  string  $name  表示件数種別名
     *  @return integer  表示件数
     */
    function getDispMax($name = 'user')
    {
        $dispMax = $this->config->get('disp_max');
        if (isset($dispMax[$name])) {
            return $dispMax[$name];
        } else {
            return $dispMax['user'];
        }
    }

    /**
     *  ログインチェック
     *
     *  @access public
     *  @param  string  $userMode  ユーザー種別（admin / user）
     *  @return boolean  null:未ログイン / null以外:ログイン
     */
    function isLogin($userMode)
    {
        try {
            $login = $this->session->get($userMode . '.login');
            if (isset($login['id']) && is_numeric($login['id']) && $login['id'] > 0) {
                // IDが正数1以上でアカウントを持っている場合
                $reconfirmTime = time() - $this->config->get('login_reconfirm_time');
                if (!isset($login['last_check_time']) || $login['last_check_time'] < $reconfirmTime) {
                    // ログインユーザー存在チェックを一定秒ごとに行う
                    $search    = array();
                    $dbManager = null;
                    switch ($userMode) {
                        case 'customer':
                            $search = array('id'   => $login['id'],
                                            'mail' => $login['mail'],
                                           );
                            $dbManager = new CustomerManager();
                            break;
                        case 'admin':
                            $search = array('id'      => $login['id'],
                                            'account' => $login['account'],
                                           );
                            $dbManager = new AdministratorManager();
                            break;
                    }
                    if (!is_null($dbManager) && $dbManager->get($search)) {
                        $login['last_check_time'] = time();
                        $this->session->set($userMode . '.login', $login);
                    } else {
                        throw new Exception(null);
                    }
                }
            } else {
                throw new Exception(null);
            }
        } catch(Exception $e) {
            $this->session->remove($userMode . '.login');
            $login = null;
        }
        return $login;
    }
}
// }}}
?>
