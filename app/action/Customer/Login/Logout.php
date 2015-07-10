<?php
/**
 *  Customer/Login/Logout.php
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @version    $Id: skel.action.php 387 2006-11-06 14:31:24Z cocoitiban $
 */

/**
 *  customer_login_logoutフォームの実装
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @access     public
 */
class Lpo_Form_CustomerLoginLogout extends Lpo_ActionForm
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
 *  customer_login_logoutアクションの実装
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @access     public
 */
class Lpo_Action_CustomerLoginLogout extends Lpo_ActionClass
{
    /**
     *  customer_login_logoutアクションの前処理
     *
     *  @access public
     *  @return string  遷移名(正常終了ならnull, 処理終了ならfalse)
     */
    function prepare()
    {
        return null;
    }

    /**
     *  customer_login_logoutアクションの実装
     *
     *  @access public
     *  @return string  遷移名
     */
    function perform()
    {
        // セッション破棄
        if ($this->session->get('customer')) {
            $this->session->remove('customer');
        }
        // ログイン画面へ
        return 'customer_login_input';
    }
}
?>
