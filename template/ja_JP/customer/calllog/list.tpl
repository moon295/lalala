{include file="customer/header.tpl" title="通話記録管理"}
      <link rel="stylesheet" href="/assets/css/datepicker.css">
      <div class="main-container-inner">
{include file="customer/sidebar.tpl"}
        <div class="main-content">
{include file="customer/breadcrumb.tpl" step="fa fa-phone::通話記録管理"}
          <div class="page-content">
            <div class="page-header">
              <h1><i class="fa fa-phone"></i> 通話記録管理</h1>
            </div>{* /.page-header *}
            <div class="row">
              <div class="col-xs-12">
{* PAGE CONTENT BEGINS *}
{include file="customer/message.tpl"}
{* ▼検索フォーム *}
                <form method="post" class="form-horizontal" role="form">
                  <div class="form-group{has_error name='company_name'}">
                    <label class="control-label col-sm-2">広告主ID</label>
                    <div class="col-sm-5">
                      {html_options_ex name="s_advertiser_id" empty="選択してください" options=$app.advertiser_list selected=$form.s_advertiser_id class="form-control"}
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="control-label col-sm-2">検索期間</label>
                    <div class="col-sm-10">
                      <div data-toggle="buttons" class="btn-group">
{foreach from=$config.search_period key="key" item="item"}
                        <label style="padding:5px;" class="btn btn-sm btn-primary{if $form.s_search_period == $key || !$form.s_search_period && $key == 'all_period'} active{/if}"><input type="radio" name="s_search_period" value="{$key}"{if $form.s_search_period == $key || !$form.s_search_period && $key == 'all_period'} checked{/if}>{$item}</label>
{/foreach}
                      </div>
                    </div>
                  </div>
                  <div class="form-group" id="search_period"{if $form.s_search_period !== 'input'} style="display:none;"{/if}>
                    <div class="col-sm-offset-2 col-sm-4">
                      <div class="input-group">
                        <span class="input-group-addon">開始</span>
                        <input class="form-control date-picker" type="text" name="s_start_date" value="{$form.s_start_date}" data-date-format="yyyy-mm-dd" data-date-language="ja">
                        <span class="input-group-addon">
                          <i class="icon-calendar bigger-110"></i>
                        </span>
                      </div>
                    </div>
                    <div class="col-sm-1 hidden-xs center">～</div>
                    <div class="col-sm-4">
                      <div class="input-group">
                        <span class="input-group-addon">終了</span>
                        <input class="form-control date-picker" type="text" name="s_end_date" value="{$form.s_end_date}" data-date-format="yyyy-mm-dd" data-date-language="ja">
                        <span class="input-group-addon">
                          <i class="icon-calendar bigger-110"></i>
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="form-actions">
                    <button type="submit" name="action_customer_calllog_list" value="true" class="btn btn-primary btn-block"><i class="icon-search"></i> 検索</button>
                  </div>
                </form>
{* ▲検索フォーム *}

{if $app_ne.list}
{capture assign="pager"}
                <div class="modal-footer margin-top">
                  <div class="pull-left">
                    {$app_ne.pager.navi}
                  </div>
                  <ul class="pagination pull-right no-margin">
                    {$app_ne.pager.links}
                  </ul>
                </div>
{/capture}
{$pager}
{/if}
                <form method="post">
                  <div>
                    <table class="table table-striped table-bordered table-hover table-list">
                      <thead>
                        <tr>
                          <th>広告主(ID)</th>
                          <th>開始時間</th>
                          <th>終了時間</th>
                          <th>通話時間</th>
                          <th>発信者番号</th>
                          <th>受信番号</th>
                          <th>転送番号</th>
                          <th>アフィリエイト単価</th>
                          <th>アフィリエイト対象</th>
                          <th>転送動作</th>
                          <th>終話ステータス</th>
                        </tr>
                      </thead>
                      <tbody>
{foreach from=$app_ne.list item="item"}
  {assign var="start_timestamp" value=$item.call_start|strtotime}
  {assign var="end_timestamp" value=$item.call_end|strtotime}
                        <tr>
                          <td>{if $item.site_name}{$item.site_name}{else}{$item.advertiser_id}{/if}</td>
                          <td>{$item.call_start}</td>
                          <td>{if $item.call_end_status==21 || $item.call_end_status==22}{$item.call_end}{/if}</td>
                          <td>{if $item.call_end_status==21 || $item.call_end_status==22}{$end_timestamp-$start_timestamp}{/if}</td>
                          <td>{$item.caller_number}</td>
                          <td>{$item.receiver_number}{if $item.explanation}<br>{$item.explanation}{/if}</td>
                          <td>{$item.transfer_number}</td>
                          <td>{$item.affiliates_unit_price}</td>
                          <td>{if $item.affiliates_target_time==0}×{else}{$item.affiliates_target_time}{/if}</td>
                          <td>{if $item.transfer_action=='CD'}転送{else}メッセージ再生{/if}</td>
                          <td>
                            {if $item.call_end_status==21 || $item.call_end_status==22}
                            正常終了
                            {elseif $item.call_end_status==41 || $item.call_end_status==42}
                            転送先応答なし
                            {elseif $item.call_end_status==11 || $item.call_end_status==12}
                            転送先呼び出し中切断
                            {elseif $item.call_end_status==51 || $item.call_end_status==52}
                            転送設定なし
                            {elseif $item.call_end_status==61 || $item.call_end_status==62}
                            番号応答なし
                            {elseif $item.call_end_status==31 || $item.call_end_status==32}
                            転送先話中
                            {/if}
                          </td>
                        </tr>
{foreachelse}
                        <tr>
                          <td colspan="11">{$smarty.const.ERR_MSG_NO_DATA}</td>
                        </tr>
{/foreach}
                      </tbody>
                    </table>
                  </div>
                </form>
{$pager}
{* PAGE CONTENT ENDS *}
              </div>{* /.col *}
            </div>{* /.row *}
          </div>{* /.page-content *}
        </div>{* /.main-content *}
      </div>{* /.main-container-inner *}
{include file="customer/middle_footer.tpl"}
      <script src="{$config.url}assets/js/date-time/bootstrap-datepicker.min.js"></script>
      <script src="{$config.url}assets/js/date-time/locales/bootstrap-datepicker.ja.js"></script>
      <script>
{literal}
        $(function() {
            $('.date-picker').datepicker({autoclose:true}).next().on(ace.click_event, function(){
                $(this).prev().focus();
            });

            $('[name=s_search_period]').change(function() {
                if ($(this).val() == 'input') {
                    $('#search_period').show();
                } else {
                    $('#search_period').hide();
                }
            });
        });
      </script>
{/literal}
{include file="customer/footer.tpl"}