<?php
/**
 *  Customer/Calllog/List.php
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
class Lpo_View_CustomerCalllogList extends Lpo_ViewClass
{
    /**
     *  遷移前処理
     *
     *  @access public
     */
    function preforward()
    {
        // 広告主ID取得
        $search = array('customer_id' => $this->session->get('customer.login.id'));
        $phonecalllogManager = new PhonecalllogManager();
        $list = $phonecalllogManager->getIdList($search);
        $adList = array();
        foreach ($list as $value) {
            $adList[$value['advertiser_id']] = $value['name'];
        }
        $this->af->setApp('advertiser_list', $adList);

        // ページャ遷移時
        if ($adList) {
            $currentPage = $this->af->get('current_page');
            $sessionName = 'customer.calllog.list';
            $this->af->form_vars = $this->session->get($sessionName);
            if (!is_null($currentPage) && is_numeric($currentPage)) {
                $this->session->set($sessionName . '.current_page', $currentPage);
            }
            $search = $this->af->getArray(false);
            if (!$search['s_advertiser_id']) {
                $search['s_advertiser_id'] = array_keys($adList);
            }
            $search['orderby']       = array('id ASC');
            $search['current_page']  = $this->af->get('current_page');
            $pager = PagerUtil::getPager($this,
                $search,
                20,
                $sessionName,
                'PhonecalllogManager',
                'getList',
                $this->config->get('url') . 'customer/calllog/%d/'
            );
            $this->af->setAppNE('list', $pager['list']);
            $this->af->setAppNE('pager', $pager['pager']);
        }
    }
}
?>
