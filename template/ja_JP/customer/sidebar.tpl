{assign var="an" value=$app_ne.action_name}
{assign var="pan" value=$app_ne.parent_action_name}
        <div class="sidebar" id="sidebar">
          <script>
            {literal}try{ace.settings.check('sidebar' , 'fixed')}catch(e){}{/literal}
          </script>
          <ul class="nav nav-list">
            <li{if $an|strstr:'customer_index'} class="active"{/if}>
              <a href="{$config.url}customer/">
                <i class="icon-home"></i>
                <span class="menu-text"> ホーム </span>
              </a>
            </li>
            <li{if $an|strstr:'customer_calllog_'} class="active"{/if}>
                <a href="{$config.url}customer/calllog/">
                    <i class="icon-phone"></i>
                    <span class="menu-text"> 通話記録管理 </span>
                </a>
            </li>
          </ul>{* /.nav-list *}
          <div class="sidebar-collapse" id="sidebar-collapse">
            <i class="icon-double-angle-left" data-icon1="icon-double-angle-left" data-icon2="icon-double-angle-right"></i>
          </div>
          <script>
            {literal}try{ace.settings.check('sidebar' , 'collapsed')}catch(e){}{/literal}
          </script>
        </div>
