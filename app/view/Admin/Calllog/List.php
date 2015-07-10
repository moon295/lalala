<?php
/**
 *  Admin/Calllog/List.php
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @version    $Id: app.view.default.php 387 2006-11-06 14:31:24Z cocoitiban $
 */

/**
 *  admin_calllog_listビューの実装
 *
 *  @author     Alev Co., Ltd.
 *  @access     public
 *  @package    Lpo
 */
class Lpo_View_AdminCalllogList extends Lpo_ViewClass
{
    /**
     *  遷移前処理
     *
     *  @access public
     */
    function preforward()
    {
        // 広告主ID取得
        if ((int)$this->session->get('admin.login.master_flg') !== 1) {
            $search['administrator_id'] = $this->session->get('admin.login.id');
        }
        $phonecalllogManager = new PhonecalllogManager();
        $list = $phonecalllogManager->getIdList($search);
        $adList = array();
        foreach ($list as $value) {
            $adList[$value['advertiser_id']] = $value['name'];
        }
        $this->af->setApp('advertiser_list', $adList);

        // ページャ遷移時
        $currentPage = $this->af->get('current_page');
        $sessionName = 'admin.calllog.list';
        $this->af->form_vars = $this->session->get($sessionName);
        if (!is_null($currentPage) && is_numeric($currentPage)) {
            $this->session->set($sessionName . '.current_page', $currentPage);
        }
        $search = $this->af->getArray(false);
        // 管理者ID
        if ((int)$this->session->get('admin.login.master_flg') !== 1) {
            $search['administrator_id'] = $this->session->get('admin.login.id');
        }
        $search['orderby']      = array('id ASC');
        $search['current_page'] = $this->af->get('current_page');
        $pager = PagerUtil::getPager($this,
            $search,
            20,
            $sessionName,
            'PhonecalllogManager',
            'getList',
            '?action_admin_calllog_list=true&current_page=%d'
        );
        $this->af->setAppNE('list', $pager['list']);
        $this->af->setAppNE('pager', $pager['pager']);
    }
}
?>
