<?php
/**
 *  Admin/Customer/Site/List.php
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @version    $Id: app.view.default.php 387 2006-11-06 14:31:24Z cocoitiban $
 */

/**
 *  admin_customer_site_listビューの実装
 *
 *  @author     Alev Co., Ltd.
 *  @access     public
 *  @package    Lpo
 */
class Lpo_View_AdminCustomerSiteList extends Lpo_ViewClass
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
        $sessionName = 'admin.customer.site.list';
        if ($this->session->get($sessionName)) {
            $this->af->form_vars = $this->session->get($sessionName);
        }
        if (!is_null($currentPage) && is_numeric($currentPage)) {
            $this->session->set($sessionName . '.current_page', $currentPage);
        }
        $search = $this->af->getArray(false);
        // 管理者ID
        if ((int)$this->session->get('admin.login.master_flg') !== 1) {
            $search['administrator_id'] = $this->session->get('admin.login.id');
        }
        $search['customer_id']  = $this->session->get('admin.customer.id');
        $search['orderby']      = array('id ASC');
        $search['current_page'] = $this->af->get('current_page');
        $pager = PagerUtil::getPager($this,
                                     $search,
                                     20,
                                     $sessionName,
                                     'SiteManager',
                                     'getList',
                                     '?action_admin_customer_site_list=' . $this->session->get('admin.customer.id') . '&current_page=%d'
                                    );
        $targetManager = new TargetManager();
        $ids = array();
        foreach ($pager['list'] as $key => $value) {
            $ids[] = $value['id'];
        }
        if ($ids) {
            $search = array('site_id' => $ids);
            $list = $targetManager->getList($search);
            $keys = array();
            foreach ($list as $value) {
                if (!isset($keys[$value['site_id']])) {
                    $keys[$value['site_id']] = array();
                }
                $keys[$value['site_id']][] = $value['key'];
            }
            $baseTag = sprintf('<script type="text/javascript" src="%s"></script>' . "\n" .
                               '<script type="text/javascript">' . "\n" .
                               '%%s' .
                               '</script>',
                               $this->config->get('js_url')
                              );
            foreach ($pager['list'] as $key => $value) {
                if (isset($keys[$value['id']])) {
                    $tag = '';
                    foreach ($keys[$value['id']] as $k) {
                        $tag .= sprintf('lpo_replace("%s");' . "\n", $k);
                    }
                    $pager['list'][$key]['tag'] = sprintf($baseTag, $tag);
                }
            }
        }
        $this->af->setAppNE('list', $pager['list']);
        $this->af->setAppNE('pager', $pager['pager']);
    }
}
?>
