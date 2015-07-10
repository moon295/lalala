<?php
/**
 *  Admin/Login/Input.php
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @version    $Id: skel.action.php 387 2006-11-06 14:31:24Z cocoitiban $
 */

/**
 *  admin_login_inputフォームの実装
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @access     public
 */
class Lpo_Form_AdminLoginInput extends Lpo_ActionForm
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
 *  admin_login_inputアクションの実装
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @access     public
 */
class Lpo_Action_AdminLoginInput extends Lpo_ActionClass
{
    /**
     * @access protected
     * @var    boolean  ログインチェックフラグ（true:チェックあり / false:チェックなし）
     */
    var $loginCheckFlg = false;

    /**
     *  @access private
     *  @var    array  セッション格納領域
     */
    var $_session;

    /**
     *  admin_login_inputアクションの前処理
     *
     *  @access public
     *  @return string  遷移名(正常終了ならnull, 処理終了ならfalse)
     */
    function prepare()
    {
        $this->_session = $this->session->get($this->parentSessionName);
        if ($this->_session) {
            if (!empty($this->_session['id'])) {
                return 'admin_index';
            }
            $this->session->remove($this->parentSessionName);
        }
        return null;
    }

    /**
     *  admin_login_inputアクションの実装
     *
     *  @access public
     *  @return string  遷移名
     */
    function perform()
    {
        return 'admin_login_input';
    }
}
?>
