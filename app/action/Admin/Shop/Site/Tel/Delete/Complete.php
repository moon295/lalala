<?php
/**
 *  Admin/Customer/Site/Tel/Delete/Complete.php
 *
 *  @author    Alev Co., Ltd.
 *  @package   Lpo
 *  @version   $Id: app.action.default.php 387 2006-11-06 14:31:24Z cocoitiban $
 */

/**
 *  admin_customer_site_tel_update_completeフォームの実装
 *
 *  @author    Alev Co., Ltd.
 *  @access    public
 *  @package   Lpo
 */
class Lpo_Form_AdminCustomerSiteTelDeleteComplete extends Lpo_ActionForm
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
        'action_admin_customer_site_tel_delete_complete' => array(
            'type'          => VAR_TYPE_INT,
            'required'      => true,
            'filter'        => 'ltrim,rtrim,ntrim',
        ),
     );
}

/**
 *  admin_customer_site_tel_update_completeアクションの実装
 *
 *  @author     Alev Co., Ltd.
 *  @access     public
 *  @package    Lpo
 */
class Lpo_Action_AdminCustomerSiteTelDeleteComplete extends Lpo_ActionClass
{
    /**
     *  @access private
     *  @var    array  セッション格納領域
     */
    var $_session;

    /**
     *  admin_customer_site_tel_update_completeアクションの前処理
     *
     *  @access    public
     *  @return    string  Forward先(正常終了ならnull)
     */
    function prepare()
    {
        // クロスサイトリクエストフォージェリ対策
        if(!Ethna_Util::isCsrfSafe()) {
            $this->af->setAppNE('error_message', '不正な処理が実行されました。');
            $this->af->clearFormVars();
            $this->af->set('action_admin_customer_site_tel_list', $this->af->get('id'));
            return 'admin_customer_site_tel_list';
        }
        // 多重ポストもしくはセッションがない場合
        if (Ethna_Util::isDuplicatePost()) {
            $this->af->setAppNE('error_message', 'すでに処理は完了しています。');
            $this->af->set('action_admin_customer_site_tel_list', $this->af->get('id'));
            return 'admin_customer_site_tel_list';
        }
        // IDが違う場合
        if ($this->af->get('id') != $this->session->get('admin.site.id')) {
            $this->af->setAppNE('error_message', '不正な処理が実行されました。');
            $this->af->set('action_admin_customer_site_tel_list', $this->af->get('id'));
            return 'admin_customer_site_tel_list';
        }
        if ($this->af->validate()) {
            $this->af->set('action_admin_customer_site_tel_list', $this->af->get('id'));
            return 'admin_customer_site_tel_list';
        }
        return null;
    }

    /**
     *  admin_customer_site_tel_update_completeアクションの実装
     *
     *  @access    public
     *  @return    string  遷移名
     */
    function perform()
    {
        $form = $this->af->getArray(false);
        try {
            $id = $form['action_admin_customer_site_tel_delete_complete'];
            $telManager = new TelManager();

            DB::begin();
            $where = array('id' => $id);
            if (!$telManager->delete($where)) {
                throw new Exception('データベースの削除に失敗しました');
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
        }
        $this->af->clearFormVars();
        $this->af->set('action_admin_customer_site_tel_list', $form['id']);
        $this->af->setAppNE('message', '正常に削除されました');
        return 'admin_customer_site_tel_list';
    }
}
?>
