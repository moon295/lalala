{assign var="an" value=$app_ne.action_name}
{assign var="pan" value=$app_ne.parent_action_name}
        <div class="sidebar" id="sidebar">
          <script>
            {literal}try{ace.settings.check('sidebar' , 'fixed')}catch(e){}{/literal}
          </script>
          <ul class="nav nav-list">
            <li{if $an|strstr:'admin_index'} class="active"{/if}>
              <a href="{$config.url}admin/">
                <i class="icon-home"></i>
                <span class="menu-text"> ホーム </span>
              </a>
            </li>
            <li{if $an|strstr:'admin_shop_'} class="active"{/if}>
              <a href="{$config.url}admin/?action_admin_shop_list=true">
                <i class="fa fa-beer"></i>
                <span class="menu-text"> ショップ管理 </span>
              </a>
            </li>
            <li{if $an|strstr:'admin_yelp_'} class="active"{/if}>
              <a href="{$config.url}admin/?action_admin_yelp_list=true">
                <i class="fa fa-cloud"></i>
                <span class="menu-text"> ショップ追加（Yelp） </span>
              </a>
            </li>
{*
            <li{if $an|strstr:'admin_bill_'} class="active"{/if}>
              <a href="{$config.url}admin/?action_admin_bill_list=true">
                <i class="icon-file"></i>
                <span class="menu-text"> 請求管理 </span>
              </a>
            </li>
            <li{if $an|strstr:'admin_calllog_'} class="active"{/if}>
                <a href="{$config.url}admin/?action_admin_calllog_list=true">
                    <i class="icon-phone"></i>
                    <span class="menu-text"> 通話記録管理 </span>
                </a>
            </li>
*}
          </ul>
          <div class="sidebar-collapse" id="sidebar-collapse">
            <i class="icon-double-angle-left" data-icon1="icon-double-angle-left" data-icon2="icon-double-angle-right"></i>
          </div>
          <script>
            {literal}try{ace.settings.check('sidebar' , 'collapsed')}catch(e){}{/literal}
          </script>
        </div>
