{* パラメータ説明 *}
{*
名称：step
値　：icon:URL:パンくず名1|icon:URL:パンくず名2|::パンくず名3
*}
{assign var="step_list" value=$step|explode:'|'}
          <div class="breadcrumbs" id="breadcrumbs">
            <script>
              {literal}try{ace.settings.check('breadcrumbs' , 'fixed')}catch(e){}{/literal}
            </script>
            <ul class="breadcrumb">
              <li>
                <i class="icon-home home-icon"></i>
                <a href="{$config.url}customer/">ホーム</a>
              </li>
{foreach from=$step_list item="item" name="item"}
  {assign var="item" value=$item|explode:':'}
              <li{if $smarty.foreach.item.last} class="active"{/if}>
                {if $item.0}<i class="{$item.0} home-icon"></i>{/if}
  {if $item.1}
                <a href="{$item.1}">{$item.2}</a>
  {else}
                {$item.2}
  {/if}
              </li>
{/foreach}
            </ul>{* .breadcrumb *}
          </div>
