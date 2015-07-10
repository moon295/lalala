<?php
/**
 *  Admin/Customer/Site/Default.php
 *
 *  @author     Alev Co., Ltd.
 *  @package    Lpo
 *  @version    $Id: app.view.default.php 387 2006-11-06 14:31:24Z cocoitiban $
 */

/**
 *  admin_customer_site_defaultビューの実装
 *
 *  @author     Alev Co., Ltd.
 *  @access     public
 *  @package    Lpo
 */
class Lpo_View_AdminCustomerSiteDefault extends Lpo_ViewClass
{
    /**
     *  遷移前処理
     *
     *  @access public
     */
    function preforward()
    {
        // 発番数セレクトボックス
        $telCount = array();
        for ($i = 1; $i <= $this->config->get('lmit_tel'); $i++) {
            $telCount[$i] = $i . '個';
        }
        $this->af->setApp('tel_count', $telCount);
    }
}
?>
