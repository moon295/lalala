<?php
/**
 *  Admin/Customer/Site/Tag/Condition/Copy/Complete.php
 *
 *  @author    Alev Co., Ltd.
 *  @package   Lpo
 *  @version   $Id: app.action.default.php 387 2006-11-06 14:31:24Z cocoitiban $
 */

/**
 *  admin_customer_site_tag_condition_copy_completeフォームの実装
 *
 *  @author    Alev Co., Ltd.
 *  @access    public
 *  @package   Lpo
 */
class Lpo_Form_AdminCustomerSiteTagConditionCopyComplete extends Lpo_ActionForm
{
    /** @var    bool    バリデータにプラグインを使うフラグ */
    var $use_validator_plugin = false;

    /**
     *  @access   private
     *  @var      array   フォーム値定義
     */
     var $form = array(
        'action_admin_customer_site_tag_condition_copy_complete' => array(
            'name'          => 'ID',
            'required'      => true,
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
        ),
    );
}

/**
 *  admin_customer_site_tag_condition_copy_completeアクションの実装
 *
 *  @author     Alev Co., Ltd.
 *  @access     public
 *  @package    Lpo
 */
class Lpo_Action_AdminCustomerSiteTagConditionCopyComplete extends Lpo_ActionClass
{
    /**
     *  @access private
     *  @var    array  セッション格納領域
     */
    var $_session;

    /**
     *  admin_customer_site_tag_condition_copy_completeアクションの前処理
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
            return 'admin_customer_site_tag_condition_list';
        }
        if (!$this->session->get('admin.customer.id')) {
            $this->af->setAppNE('error_message', '顧客が選択されていません。');
            return 'admin_customer_list';
        } else if (!$this->session->get('admin.site.id')) {
            $this->af->setAppNE('error_message', 'サイトが選択されていません。');
            return 'admin_customer_site_list';
        }
        // 多重ポストもしくはセッションがない場合
        if (Ethna_Util::isDuplicatePost()) {
            $this->af->setAppNE('error_message', 'すでにコピー処理は完了しています。');
            $this->af->clearFormVars();
            return 'admin_customer_site_tag_condition_list';
        }
        // 登録前の再チェック
        if ($this->af->validate()) {
            return 'admin_customer_site_tag_condition_list';
        }
        return null;
    }

    /**
     *  admin_customer_site_tag_condition_copy_completeアクションの実装
     *
     *  @access    public
     *  @return    string  遷移名
     */
    function perform()
    {
        try {
            $conditionId = $this->af->get('action_admin_customer_site_tag_condition_copy_complete');

            $conditionManager = new ConditionManager();
            // コピー元情報取得
            $search = array('id' => $conditionId);
            // 管理者ID
            if ((int)$this->session->get('admin.login.master_flg') !== 1) {
                $search['administrator_id'] = $this->session->get('admin.login.id');
            }
            $srcConditionData = $conditionManager->get($search);
            if (!$srcConditionData) {
                throw new Exception('該当情報が存在しません。');
            }

            // 切り替え対象情報登録
            DB::begin();
            foreach ($srcConditionData as $key => $value) {
                if (!$value) {
                    unset($srcConditionData[$key]);
                }
            }
            $search = array('target_id' => $srcConditionData['target_id']);
            $srcConditionData['no']           = $conditionManager->getNo($search);
            $srcConditionData['name']         = sprintf('コピー_%s', $srcConditionData['name']);
            $srcConditionData['#insert_time'] = 'NOW()';
            unset($srcConditionData['id']);
            unset($srcConditionData['insert_time']);
            unset($srcConditionData['update_time']);
            unset($srcConditionData['date']);
            unset($srcConditionData['pv']);
            if (!$conditionManager->insert($srcConditionData)) {
                throw new Exception('データベースの登録に失敗しました。[2]');
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $this->af->setAppNE('error_message', $e->getMessage());
            return 'admin_customer_site_tag_condition_list';
        }
        $this->af->clearFormVars();
        $this->af->setAppNE('message', 'コピーが完了しました。');
        return 'admin_customer_site_tag_condition_list';
    }
}
?>
