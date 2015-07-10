<?php
/**
 *  Admin/Customer/Site/Tag/Insert/Complete.php
 *
 *  @author    Alev Co., Ltd.
 *  @package   Lpo
 *  @version   $Id: app.action.default.php 387 2006-11-06 14:31:24Z cocoitiban $
 */

/**
 *  admin_customer_site_tag_insert_completeフォームの実装
 *
 *  @author    Alev Co., Ltd.
 *  @access    public
 *  @package   Lpo
 */
class Lpo_Form_AdminCustomerSiteTagInsertComplete extends Lpo_ActionForm
{
    /** @var    bool    バリデータにプラグインを使うフラグ */
    var $use_validator_plugin = false;

    /**
     *  @access   private
     *  @var      array   フォーム値定義
     */
     var $form = array(
        'name' => array(
            'name'          => '名前',
            'max'           => 32,
            'required'      => true,
            'filter'        => 'ltrim,rtrim,ntrim,alnum_zentohan',
        ),
    );
}

/**
 *  admin_customer_site_tag_insert_completeアクションの実装
 *
 *  @author     Alev Co., Ltd.
 *  @access     public
 *  @package    Lpo
 */
class Lpo_Action_AdminCustomerSiteTagInsertComplete extends Lpo_ActionClass
{
    /**
     *  @access private
     *  @var    array  セッション格納領域
     */
    var $_session;

    /**
     *  admin_customer_site_tag_insert_completeアクションの前処理
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
            $this->af->setAppNE('error_message', 'すでに登録処理は完了しています。');
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
     *  admin_customer_site_tag_insert_completeアクションの実装
     *
     *  @access    public
     *  @return    string  遷移名
     */
    function perform()
    {
        try {
            $targetManager = new TargetManager();
            // キーの重複チェック
            do {
                $key = CommonUtil::getKey(10);
                $search = array('key' => $key);
                $result = $targetManager->isDuplication($search);
            } while ($result);

            // 切り替え対象情報登録
            DB::begin();
            $value = array('#site_id' => $this->session->get('admin.site.id'),
                           'name'     => $this->af->get('name'),
                           'key'      => $key,
                          );
            if (!$targetManager->insert($value)) {
                throw new Exception('データベースの登録に失敗しました。');
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $this->af->setAppNE('error_message', $e->getMessage());
            return 'admin_customer_site_tag_list';
        }
        $this->af->clearFormVars();
        return 'admin_customer_site_tag_list';
    }
}
?>
