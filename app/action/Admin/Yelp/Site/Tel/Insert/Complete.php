<?php
/**
 *  Admin/Customer/Site/Tel/Insert/Complete.php
 *
 *  @author    Alev Co., Ltd.
 *  @package   Lpo
 *  @version   $Id: app.action.default.php 387 2006-11-06 14:31:24Z cocoitiban $
 */

/**
 *  admin_customer_site_tel_insert_completeフォームの実装
 *
 *  @author    Alev Co., Ltd.
 *  @access    public
 *  @package   Lpo
 */
class Lpo_Form_AdminCustomerSiteTelInsertComplete extends Lpo_ActionForm
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
        'tel_count' => array(
            'type'          => VAR_TYPE_INT,
            'name'          => '電話番号発番数',
            'required'      => true,
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
        ),
     );

    /**
     *  フォームチェック（オーバーライド）
     *
     *  @access public
     *  @return integer  エラー数
     */
    function validate()
    {
        $this->form['tel_count']['max'] = $this->backend->config->get('limit_tel');
        parent::validate();
        return $this->ae->count();
    }
}

/**
 *  admin_customer_site_tel_insert_completeアクションの実装
 *
 *  @author     Alev Co., Ltd.
 *  @access     public
 *  @package    Lpo
 */
class Lpo_Action_AdminCustomerSiteTelInsertComplete extends Lpo_ActionClass
{
    /**
     *  @access private
     *  @var    array  セッション格納領域
     */
    var $_session;

    /**
     *  admin_customer_site_tel_insert_completeアクションの前処理
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
            $this->af->setAppNE('error_message', 'すでに更新処理は完了しています。');
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
     *  admin_customer_site_tel_insert_completeアクションの実装
     *
     *  @access    public
     *  @return    string  遷移名
     */
    function perform()
    {
        $form = $this->af->getArray(false);
        try {
            $telManager = new TelManager();
            DB::begin();
            for ($i = 0; $i < $form['tel_count']; $i++) {
                $value = array('site_id'    => $form['id'],
                               '#from_date' => 'NULL',
                               '#to_date'   => 'NULL',
                              );
                if (!$telManager->insert($value)) {
                    throw new Exception('データベースの登録に失敗しました。');
                }
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
        }
        $this->af->clearFormVars();
        $this->af->set('action_admin_customer_site_tel_list', $form['id']);
        $this->af->setAppNE('message', '正常に電話番号発番申請されました');
        return 'admin_customer_site_tel_list';
    }
}
?>
