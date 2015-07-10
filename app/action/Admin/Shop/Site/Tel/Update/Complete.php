<?php
/**
 *  Admin/Customer/Site/Tel/Update/Complete.php
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
class Lpo_Form_AdminCustomerSiteTelUpdateComplete extends Lpo_ActionForm
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
        'tel' => array(
            'type'          => array(VAR_TYPE_STRING),
            'name'          => '電話番号',
            'max'           => 16,
            'filter'        => 'array_ltrim,array_rtrim,array_ntrim,array_alnum_zentohan',
        ),
        'explanation' => array(
            'type'          => array(VAR_TYPE_STRING),
            'name'          => '説明',
            'max'           => 128,
            'filter'        => 'array_ltrim,array_rtrim,array_ntrim,array_alnum_zentohan',
        ),
        'limit' => array(
            'type'          => array(VAR_TYPE_STRING),
            'name'          => '停止日',
            'filter'        => 'array_ltrim,array_rtrim,array_ntrim,array_alnum_zentohan',
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
        $form = $this->getArray(false);
        foreach ($form['tel'] as $id => $tel) {
            $this->form['tel' . $id] = $this->form['tel'];
            $this->form['tel' . $id]['type']   = VAR_TYPE_STRING;
            $this->form['tel' . $id]['custom'] = 'checkTel';
            $this->set('tel' . $id, $tel);

            $this->form['explanation' . $id] = $this->form['explanation'];
            $this->form['explanation' . $id]['type']   = VAR_TYPE_STRING;
            $this->set('explanation' . $id, $form['explanation'][$id]);
        }
        parent::validate();
        return $this->ae->count();
    }
}

/**
 *  admin_customer_site_tel_update_completeアクションの実装
 *
 *  @author     Alev Co., Ltd.
 *  @access     public
 *  @package    Lpo
 */
class Lpo_Action_AdminCustomerSiteTelUpdateComplete extends Lpo_ActionClass
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
     *  admin_customer_site_tel_update_completeアクションの実装
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
            foreach ($form['tel'] as $id => $tel) {
                $value = array('tel'          => $form['tel'][$id],
                               'explanation'  => $form['explanation'][$id],
                               '#update_time' => 'NOW()',
                              );
                if (isset($form['limit'][$id]) && $form['limit'][$id]) {
                    $value['to_date'] = $form['limit'][$id];
                }
                $where = array('id' => $id);
                if ($telManager->update($value, $where) === -1) {
                    throw new Exception('データベースの削除に失敗しました。');
                }
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
        }
        $this->af->clearFormVars();
        $this->af->set('action_admin_customer_site_tel_list', $form['id']);
        $this->af->setAppNE('message', '正常に変更されました');
        return 'admin_customer_site_tel_list';
    }
}
?>
