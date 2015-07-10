<?php
/**
 *  Admin/Customer/Site/Insert/Complete.php
 *
 *  @author    Alev Co., Ltd.
 *  @package   Lpo
 *  @version   $Id: app.action.default.php 387 2006-11-06 14:31:24Z cocoitiban $
 */
require_once BASE . '/app/action/Admin/Customer/Site/ActionForm.php';

/**
 *  admin_customer_site_insert_completeフォームの実装
 *
 *  @author    Alev Co., Ltd.
 *  @access    public
 *  @package   Lpo
 */
class Lpo_Form_AdminCustomerSiteInsertComplete extends Lpo_Form_AdminCustomerSiteActionForm
{
}

/**
 *  admin_customer_site_insert_completeアクションの実装
 *
 *  @author     Alev Co., Ltd.
 *  @access     public
 *  @package    Lpo
 */
class Lpo_Action_AdminCustomerSiteInsertComplete extends Lpo_ActionClass
{
    /**
     *  @access private
     *  @var    array  セッション格納領域
     */
    var $_session;

    /**
     *  admin_customer_site_insert_completeアクションの前処理
     *
     *  @access    public
     *  @return    string  Forward先(正常終了ならnull)
     */
    function prepare()
    {
        // クロスサイトリクエストフォージェリ対策
        if(!Ethna_Util::isCsrfSafe()) {
            $this->af->setAppNE('error_message', '不正な処理が実行されました。');
            return 'admin_customer_site_list';
        }
        if (!$this->session->get('admin.customer.id')) {
            $this->af->setAppNE('error_message', '顧客が選択されていません。');
            return 'admin_customer_list';
        }

        $this->_session = $this->session->get($this->parentSessionName);

        // 多重ポストもしくはセッションがない場合
        if (Ethna_Util::isDuplicatePost() || is_null($this->_session)) {
            $this->af->setAppNE('error_message', 'すでに登録処理は完了しています。');
            return 'admin_customer_site_list';
        }
        // 登録前の再チェック
        $this->af->form_vars = $this->_session;
        if ($this->af->validate()) {
            $this->af->setAppNE('tpl_file', 'admin_customer_site_insert_input');
            return 'admin_customer_site_default';
        }
        return null;
    }

    /**
     *  admin_customer_site_insert_completeアクションの実装
     *
     *  @access    public
     *  @return    string  遷移名
     */
    function perform()
    {
        $form = $this->_session;
        try {
            $siteManager = new SiteManager();
            $telManager  = new TelManager();
            DB::begin();
            // 顧客情報登録
            $parseUrl = parse_url('http://' . $form['url']);
            $value = array('#customer_id' => $this->session->get('admin.customer.id'),
                           'name'         => $form['name'],
                           'url'          => $parseUrl['host'],
                           '#suspend_flg' => $form['suspend_flg'] ? 1 : 0,
                          );
            $siteId = $siteManager->insert($value);
            if (!$siteId) {
                throw new Exception('データベースの登録に失敗しました。[1]');
            }

            for ($i = 0; $i < $form['tel_count']; $i++) {
                $value = array('site_id' => $siteId);
                if (!$telManager->insert($value)) {
                    throw new Exception('データベースの登録に失敗しました。[2]');
                }
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            $this->af->setAppNE('error_message', $e->getMessage());
            return 'admin_customer_site_list';
        }
        // セッション削除
        $this->session->remove($this->parentSessionName);
        return 'admin_customer_site_list';
    }
}
?>
