<?php
/**
 *  Admin/Customer/Delete/Complete.php
 *
 *  @author    Alev Co., Ltd.
 *  @package   Lpo
 *  @version   $Id: app.action.default.php 387 2006-11-06 14:31:24Z cocoitiban $
 */

/**
 *  admin_customer_delete_completeフォームの実装
 *
 *  @author    Alev Co., Ltd.
 *  @access    public
 *  @package   Lpo
 */
class Lpo_Form_AdminCustomerDeleteComplete extends Lpo_ActionForm
{
    /** @var    bool    バリデータにプラグインを使うフラグ */
    var $use_validator_plugin = false;

    /**
     *  @access   private
     *  @var      array   フォーム値定義
     */
     var $form = array(
        'id' => array(
            'type'          => VAR_TYPE_INT,
            'required'      => true,
            'filter'        => 'ltrim,rtrim,ntrim',
        ),
     );
}

/**
 *  admin_customer_delete_completeアクションの実装
 *
 *  @author     Alev Co., Ltd.
 *  @access     public
 *  @package    Lpo
 */
class Lpo_Action_AdminCustomerDeleteComplete extends Lpo_ActionClass
{
    /**
     *  @access private
     *  @var    array  セッション格納領域
     */
    var $_session;

    /**
     *  admin_customer_delete_completeアクションの前処理
     *
     *  @access    public
     *  @return    string  Forward先(正常終了ならnull)
     */
    function prepare()
    {
        // クロスサイトリクエストフォージェリ対策
        if(!Ethna_Util::isCsrfSafe()) {
            $this->af->setAppNE('error_message', '不正な処理が実行されました。');
            return 'admin_customer_list';
        }
        $this->_session = $this->session->get($this->parentSessionName);

        // 多重ポストもしくはセッションがない場合
        if (Ethna_Util::isDuplicatePost() || is_null($this->_session)) {
            $this->af->setAppNE('error_message', 'すでに削除処理は完了しています。');
            return 'admin_customer_list';
        }
        // IDが違う場合
        if ($this->af->get('id') != $this->_session['id']) {
            $this->af->setAppNE('error_message', '不正な処理が実行されました。');
            return 'admin_customer_list';
        }
        return null;
    }

    /**
     *  admin_customer_delete_completeアクションの実装
     *
     *  @access    public
     *  @return    string  遷移名
     */
    function perform()
    {
        $form = $this->af->getArray(false);
        $customerManager = new CustomerManager();
        $where = array('id' => $form['id']);
        if ($customerManager->delete($where) === -1) {
            $this->af->setAppNE('error_message', 'データベースの削除に失敗しました。');
            return 'admin_customer_list';
        }

        // セッション削除
        $this->session->remove($this->parentSessionName);
        return 'admin_customer_list';
    }
}
?>
