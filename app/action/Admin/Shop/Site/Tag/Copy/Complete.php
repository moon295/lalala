<?php
/**
 *  Admin/Customer/Site/Tag/Copy/Complete.php
 *
 *  @author    Alev Co., Ltd.
 *  @package   Lpo
 *  @version   $Id: app.action.default.php 387 2006-11-06 14:31:24Z cocoitiban $
 */

/**
 *  admin_customer_site_tag_copy_completeフォームの実装
 *
 *  @author    Alev Co., Ltd.
 *  @access    public
 *  @package   Lpo
 */
class Lpo_Form_AdminCustomerSiteTagCopyComplete extends Lpo_ActionForm
{
    /** @var    bool    バリデータにプラグインを使うフラグ */
    var $use_validator_plugin = false;

    /**
     *  @access   private
     *  @var      array   フォーム値定義
     */
     var $form = array(
        'action_admin_customer_site_tag_copy_complete' => array(
            'name'          => 'ID',
            'required'      => true,
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
        ),
    );
}

/**
 *  admin_customer_site_tag_copy_completeアクションの実装
 *
 *  @author     Alev Co., Ltd.
 *  @access     public
 *  @package    Lpo
 */
class Lpo_Action_AdminCustomerSiteTagCopyComplete extends Lpo_ActionClass
{
    /**
     *  @access private
     *  @var    array  セッション格納領域
     */
    var $_session;

    /**
     *  admin_customer_site_tag_copy_completeアクションの前処理
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
            return 'admin_customer_site_tag_list';
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
            return 'admin_customer_site_tag_list';
        }
        // 登録前の再チェック
        if ($this->af->validate()) {
            return 'admin_customer_site_tag_list';
        }
        return null;
    }

    /**
     *  admin_customer_site_tag_copy_completeアクションの実装
     *
     *  @access    public
     *  @return    string  遷移名
     */
    function perform()
    {
        try {
            $targetId = $this->af->get('action_admin_customer_site_tag_copy_complete');

            $targetManager = new TargetManager();
            // コピー元情報取得
            $search = array('id' => $targetId);
            // 管理者ID
            if ((int)$this->session->get('admin.login.master_flg') !== 1) {
                $search['administrator_id'] = $this->session->get('admin.login.id');
            }
            $srcTargetData = $targetManager->get($search);
            if (!$srcTargetData) {
                throw new Exception('該当情報が存在しません。');
            }

            // コピー元切り替え条件情報取得
            $search = array('target_id' => $targetId);
            // 管理者ID
            if ((int)$this->session->get('admin.login.master_flg') !== 1) {
                $search['administrator_id'] = $this->session->get('admin.login.id');
            }
            $conditionManager = new ConditionManager();
            $srcConditionData = $conditionManager->getList($search);

            // キーの生成と重複チェック
            do {
                $key = CommonUtil::getKey(10);
                $search = array('key' => $key);
                $result = $targetManager->isDuplication($search);
            } while ($result);

            // 切り替え対象情報登録
            DB::begin();
            $value = array('#site_id'    => $srcTargetData['site_id'],
                           'name'        => sprintf('コピー_%s[%s]', $srcTargetData['name'], $key),
                           'key'         => $key,
                           'suspend_flg' => $srcTargetData['suspend_flg'],
                          );
            $id = $targetManager->insert($value);
            if (!$id) {
                throw new Exception('データベースの登録に失敗しました。[1]');
            }
            foreach ($srcConditionData as $value) {
                foreach ($value as $key => $val) {
                    if (!$val) {
                        unset($value[$key]);
                    }
                }
                $value['target_id'] = $id;
                $value['#insert_time'] = 'NOW()';
                unset($value['id']);
                unset($value['insert_time']);
                unset($value['update_time']);
                unset($value['date']);
                unset($value['pv']);
                if (!$conditionManager->insert($value)) {
                    throw new Exception('データベースの登録に失敗しました。[2]');
                }
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $this->af->setAppNE('error_message', $e->getMessage());
            return 'admin_customer_site_tag_list';
        }
        $this->af->clearFormVars();
        $this->af->setAppNE('message', 'コピーが完了しました。');
        return 'admin_customer_site_tag_list';
    }
}
?>
