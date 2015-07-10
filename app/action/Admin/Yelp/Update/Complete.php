<?php
/**
 *  Admin/Customer/Update/Complete.php
 *
 *  @author    Alev Co., Ltd.
 *  @package   Lpo
 *  @version   $Id: app.action.default.php 387 2006-11-06 14:31:24Z cocoitiban $
 */
require_once BASE . '/app/action/Admin/Customer/ActionForm.php';

/**
 *  admin_customer_update_completeフォームの実装
 *
 *  @author    Alev Co., Ltd.
 *  @access    public
 *  @package   Lpo
 */
class Lpo_Form_AdminCustomerUpdateComplete extends Lpo_Form_AdminCustomerActionForm
{
}

/**
 *  admin_customer_update_completeアクションの実装
 *
 *  @author     Alev Co., Ltd.
 *  @access     public
 *  @package    Lpo
 */
class Lpo_Action_AdminCustomerUpdateComplete extends Lpo_ActionClass
{
    /**
     *  @access private
     *  @var    array  セッション格納領域
     */
    var $_session;

    /**
     *  admin_customer_update_completeアクションの前処理
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
            $this->af->setAppNE('error_message', 'すでに更新処理は完了しています。');
            return 'admin_customer_list';
        }
        // IDが違う場合
        if ($this->af->get('id') != $this->_session['id']) {
            $this->af->setAppNE('error_message', '不正な処理が実行されました。');
            return 'admin_customer_list';
        }
        // 更新前の再チェック
        $this->af->form_vars = $this->_session;
        if ($this->af->validate()) {
            $this->af->setAppNE('tpl_file', 'admin_customer_update_input');
            return 'admin_customer_default';
        }
        return null;
    }

    /**
     *  admin_customer_update_completeアクションの実装
     *
     *  @access    public
     *  @return    string  遷移名
     */
    function perform()
    {
        $form = $this->_session;
        $value = array('company_name'      => $form['company_name'],
                       '#suspend_flg'      => $form['suspend_flg'] ? 1 : 0,
                       '#update_time'      => 'NOW()',
                      );
        DB::formatText('name_sei', $form, $value);
        DB::formatText('name_mei', $form, $value);
        DB::formatText('mail', $form, $value);
        if ($form['password']) {
            $value['password'] = CommonUtil::hashPassword($form['password']);
        }
        // 更新
        $where['id'] = $form['id'];
        $customerManager = new CustomerManager();
        if ($customerManager->update($value, $where) === -1) {
            $this->af->setAppNE('error_message', 'データベースの更新に失敗しました。');
            return 'admin_customer_list';
        }

        // セッション削除
        $this->session->remove($this->parentSessionName);
        return 'admin_customer_list';
    }
}
?>
