<?php
/**
 *  Admin/Login/Logout.php
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @version    $Id: skel.action.php 387 2006-11-06 14:31:24Z cocoitiban $
 */

/**
 *  admin_login_logoutフォームの実装
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @access     public
 */
class Lpo_Form_AdminLoginLogout extends Lpo_ActionForm
{
    /** @var    bool    バリデータにプラグインを使うフラグ */
    var $use_validator_plugin = false;

    /**
     *  @access private
     *  @var    array   フォーム値定義
     */
    var $form = array(
    );
}

/**
 *  admin_login_logoutアクションの実装
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @access     public
 */
class Lpo_Action_AdminLoginLogout extends Lpo_ActionClass
{
    /**
     *  admin_login_logoutアクションの前処理
     *
     *  @access public
     *  @return string  遷移名(正常終了ならnull, 処理終了ならfalse)
     */
    function prepare()
    {
        return null;
    }

    /**
     *  admin_login_logoutアクションの実装
     *
     *  @access public
     *  @return string  遷移名
     */
    function perform()
    {
        // セッション破棄
        if ($this->session->get('admin')) {
            $this->session->remove('admin');
        }
        // ログイン画面へ
        return 'admin_login_input';
    }
}
?>
