<?php
/**
 *  Admin/Customer/Site/Tag/Condition/List.php
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @version    $Id: app.view.default.php 387 2006-11-06 14:31:24Z cocoitiban $
 */

/**
 *  admin_customer_site_tag_condition_listビューの実装
 *
 *  @author     Alev Co., Ltd.
 *  @access     public
 *  @package    Lpo
 */
class Lpo_View_AdminCustomerSiteTagConditionList extends Lpo_ViewClass
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
        $sessionName = 'admin.customer.site.tag.condition.list';
        if ($this->session->get($sessionName)) {
            $this->af->form_vars = $this->session->get($sessionName);
            if (!is_null($currentPage) && is_numeric($currentPage)) {
                $this->session->set($sessionName . '.current_page', $currentPage);
            }
        }
        $form = $this->af->getArray(false);
        $search = array('target_id' => $this->session->get('admin.target.id'),
                        'period'    => $form['period'],
                        'sort'      => $form['sort'],
                        'orderby'   => array('id ASC'),
                       );
        // 管理者ID
        if ((int)$this->session->get('admin.login.master_flg') !== 1) {
            $search['administrator_id'] = $this->session->get('admin.login.id');
        }
        $pager = PagerUtil::getPager($this,
                                     $search,
                                     20,
                                     $sessionName,
                                     'ConditionManager',
                                     'getList',
                                     '?action_admin_customer_site_tag_condition_list=true&current_page=%d'
                                    );
        foreach ($pager['list'] as $key => $value) {
            $keyword = explode("\n", $value['enable_keywords']);
            $keyword = array_splice($keyword, 0, 10);
            $pager['list'][$key]['enable_keywords'] = implode(',', $keyword);
        }
        $this->af->setAppNE('list', $pager['list']);
        $this->af->setAppNE('pager', $pager['pager']);
    }
}
?>
