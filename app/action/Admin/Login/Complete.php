<?php
/**
 *  Admin/Login/Complete.php
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @version    $Id: skel.action.php 387 2006-11-06 14:31:24Z cocoitiban $
 */

/**
 *  admin_login_completeフォームの実装
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @access     public
 */
class Lpo_Form_AdminLoginComplete extends Lpo_ActionForm
{
    /** @var    bool    バリデータにプラグインを使うフラグ */
    var $use_validator_plugin = false;

    /**
     *  @access private
     *  @var    array   フォーム値定義
     */
    var $form = array(
        'account' => array(
            'name'          => 'アカウント',
            'required'      => true,
            'min'           => 3,
            'min_error'     => '{form}を正しく入力してください',
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
        ),
        'password' => array(
            'name'          => 'パスワード',
            'required'      => true,
            'min'           => 3,
            'min_error'     => '{form}を正しく入力してください',
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
        ),
    );
}

/**
 *  admin_login_completeアクションの実装
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @access     public
 */
class Lpo_Action_AdminLoginComplete extends Lpo_ActionClass
{
    /**
     * @access protected
     * @var    boolean  ログインチェックフラグ（true:チェックあり / false:チェックなし）
     */
    var $loginCheckFlg = false;

    /**
     *  admin_login_completeアクションの前処理
     *
     *  @access public
     *  @return string  遷移名(正常終了ならnull, 処理終了ならfalse)
     */
    function prepare()
    {
        // クロスサイトリクエストフォージェリ対策
        if(!Ethna_Util::isCsrfSafe()) {
            $this->ae->add('invalid', '不正な処理が実行されました。', E_FORM_INVALIDVALUE);
            return 'admin_login_input';
        }
        if ($this->af->validate()) {
            return 'admin_login_input';
        } else {
            $administratorManager = new AdministratorManager();
            $data = $administratorManager->login($this->af->getArray(false));
            if (!$data) {
                $this->ae->add('invalid', 'アカウントとパスワードを正しく入力してください。', E_FORM_INVALIDVALUE);
                return 'admin_login_input';
            }
            // 最終チェック時刻
            $data['last_check_time'] = time();
            $this->session->set($this->parentSessionName, $data);
        }
        return null;
    }

    /**
     *  admin_login_completeアクションの実装
     *
     *  @access public
     *  @return string  遷移名
     */
    function perform()
    {
        // 管理者トップ画面へリダイレクト
        header('Location: ?action_admin_index=true');
        exit();
    }
}
?>
