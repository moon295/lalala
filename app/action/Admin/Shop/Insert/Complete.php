<?php
/**
 *  Admin/Customer/Insert/Complete.php
 *
 *  @author    Alev Co., Ltd.
 *  @package   Lpo
 *  @version   $Id: app.action.default.php 387 2006-11-06 14:31:24Z cocoitiban $
 */
require_once BASE . '/app/action/Admin/Customer/ActionForm.php';

/**
 *  admin_customer_insert_completeフォームの実装
 *
 *  @author    Alev Co., Ltd.
 *  @access    public
 *  @package   Lpo
 */
class Lpo_Form_AdminCustomerInsertComplete extends Lpo_Form_AdminCustomerActionForm
{
}

/**
 *  admin_customer_insert_completeアクションの実装
 *
 *  @author     Alev Co., Ltd.
 *  @access     public
 *  @package    Lpo
 */
class Lpo_Action_AdminCustomerInsertComplete extends Lpo_ActionClass
{
    /**
     *  @access private
     *  @var    array  セッション格納領域
     */
    var $_session;

    /**
     *  admin_customer_insert_completeアクションの前処理
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
            $this->af->setAppNE('error_message', 'すでに登録処理は完了しています。');
            return 'admin_customer_list';
        }
        // 登録前の再チェック
        $this->af->form_vars = $this->_session;
        if ($this->af->validate()) {
            $this->af->setAppNE('tpl_file', 'admin_customer_insert_input');
            return 'admin_customer_default';
        }
        return null;
    }

    /**
     *  admin_customer_insert_completeアクションの実装
     *
     *  @access    public
     *  @return    string  遷移名
     */
    function perform()
    {
        $form = $this->_session;
        try {
            $customerManager = new CustomerManager();
            DB::begin();
            // 顧客情報登録
            $value = array('#administrator_id' => $this->session->get('admin.login.id'),
                           'company_name'      => $form['company_name'],
                           '#suspend_flg'      => $form['suspend_flg'] ? 1 : 0,
                          );
            DB::formatText('name_sei', $form, $value);
            DB::formatText('name_mei', $form, $value);
            DB::formatText('mail', $form, $value);
            if ($form['password']) {
                $value['password'] = CommonUtil::hashPassword($form['password']);
            }
            if (!$customerManager->insert($value)) {
                throw new Exception('データベースの登録に失敗しました。');
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $this->af->setAppNE('error_message', $e->getMessage());
            return 'admin_customer_list';
        }
        // セッション削除
        $this->session->remove($this->parentSessionName);
        return 'admin_customer_list';
    }
}
?>
