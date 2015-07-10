<?php
/**
 *  Admin/Administrator/List.php
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @version    $Id: app.view.default.php 387 2006-11-06 14:31:24Z cocoitiban $
 */

/**
 *  admin_administrator_listビューの実装
 *
 *  @author     Alev Co., Ltd.
 *  @access     public
 *  @package    Lpo
 */
class Lpo_View_AdminAdministratorList extends Lpo_ViewClass
{
    /**
     *  遷移前処理
     *
     *  @access public
     */
    function preforward()
    {
        // ページャ遷移時
        $currentPage = $this->af->get('current_page');
        $sessionName = 'admin.administrator.list';
        $this->af->form_vars = $this->session->get($sessionName);
        if (!is_null($currentPage) && is_numeric($currentPage)) {
            $this->session->set($sessionName . '.current_page', $currentPage);
        }
        $pager = PagerUtil::getPager($this,
                                     $this->af->getArray(false),
                                     20,
                                     $sessionName,
                                     'AdministratorManager',
                                     'getList',
                                     "?action_admin_administrator_list=true&current_page=%d"
                                    );
        $this->af->setAppNE('list', $pager['list']);
        $this->af->setAppNE('pager', $pager['pager']);
    }
}
?>
