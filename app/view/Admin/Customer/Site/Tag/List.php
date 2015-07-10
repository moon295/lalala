<?php
/**
 *  Admin/Customer/Site/Tag/List.php
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @version    $Id: app.view.default.php 387 2006-11-06 14:31:24Z cocoitiban $
 */

/**
 *  admin_customer_site_tag_listビューの実装
 *
 *  @author     Alev Co., Ltd.
 *  @access     public
 *  @package    Lpo
 */
class Lpo_View_AdminCustomerSiteTagList extends Lpo_ViewClass
{
    /**
     *  遷移前処理
     *
     *  @access public
     */
    function preforward()
    {
        $search = array('site_id' => $this->session->get('admin.site.id'),
                        'orderby' => array('id ASC'),
                       );
        // 管理者ID
        if ((int)$this->session->get('admin.login.master_flg') !== 1) {
            $search['administrator_id'] = $this->session->get('admin.login.id');
        }
        $targetManager = new TargetManager();
        $list = $targetManager->getList($search);
        $this->af->setAppNE('list', $list);
    }
}
?>
