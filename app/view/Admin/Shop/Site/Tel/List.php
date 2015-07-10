<?php
/**
 *  Admin/Customer/Site/Tel/List.php
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @version    $Id: app.view.default.php 387 2006-11-06 14:31:24Z cocoitiban $
 */

/**
 *  admin_customer_site_tel_listビューの実装
 *
 *  @author     Alev Co., Ltd.
 *  @access     public
 *  @package    Lpo
 */
class Lpo_View_AdminCustomerSiteTelList extends Lpo_ViewClass
{
    /**
     *  遷移前処理
     *
     *  @access public
     */
    function preforward()
    {
        // サイトs情報取得
        $search = array('id' => $this->session->get('admin.site.id'));
        $siteManager = new SiteManager();
        $site = $siteManager->get($search);
        $this->af->setApp('site', $site);

        $search = array('site_id' => $this->session->get('admin.site.id'),
                        'orderby' => array('id ASC'),
                       );
        // 管理者ID
        if ((int)$this->session->get('admin.login.master_flg') !== 1) {
            $search['administrator_id'] = $this->session->get('admin.login.id');
        }
        $telManager = new TelManager();
        $list = $telManager->getList($search);
        $oldList = array();
        foreach ($list as $key => $value) {
            if ($value['to_date'] && $value['to_date'] <= date('Y-m-d')) {
                // 廃止日が現在より過去の場合、一覧から削除
                $oldList[] = $value;
                unset($list[$key]);
            }
        }
        $this->af->setAppNE('list', $list);
        $this->af->setAppNE('old_list', $oldList);

        // 発番数セレクトボックス
        $telCount = array();
        for ($i = 1; $i <= $this->config->get('lmit_tel') - count($list); $i++) {
            $telCount[$i] = $i . '個';
        }
        $this->af->setApp('tel_count', $telCount);

        // 停止期日
        $limit = array();
        for ($i = -1; $i <= 0; $i++) {
            $time = strtotime('+' . $i . ' Month');
            $date = date('Y-m-t', $time);
            $limit[$date] = sprintf('%s停止', date('Y年m月t日', $time));
        }
        $this->af->setApp('limit', $limit);
    }
}
?>
