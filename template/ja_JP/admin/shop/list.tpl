{include file="admin/header.tpl" title="ショップ管理"}
      <div class="main-container-inner">
{include file="admin/sidebar.tpl"}
        <div class="main-content">
{include file="admin/breadcrumb.tpl" step="icon-beer::ショップ管理"}
          <div class="page-content">
            <div class="page-header">
            <h1><i class="icon-beer"></i> ショップ管理</h1>
            </div>{* /.page-header *}
            <div class="row">
              <div class="col-xs-12">
{* PAGE CONTENT BEGINS *}
{include file="admin/message.tpl"}
{* ▼検索フォーム *}
                <form method="post" class="form-horizontal" role="form">
                  <div class="form-group{has_error name='company_name'}">
                    <label class="control-label col-sm-2">会社名</label>
                    <div class="col-sm-5">
                      <input type="text" name="company_name" value="{$form.company_name}" class="form-control">
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="control-label col-sm-2">メールアドレス</label>
                    <div class="col-sm-10">
                      <input type="text" name="mail" value="{$form.mail}" class="form-control">
                    </div>
                  </div>
                  <div class="form-actions">
                    <button type="submit" name="action_admin_customer_list" value="true" class="btn btn-primary btn-block"><i class="icon-search"></i> 検索</button>
                  </div>
                </form>
{* ▲検索フォーム *}
                <div class="row">
                  <div class="col-sm-12">
                    <form method="post">
                      <button type="submit" name="action_admin_customer_insert_input" value="true" class="btn btn-primary pull-right"><i class="icon-pencil"></i>新規登録</button>
                    </form>
                  </div>
                </div>

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
                          <th class="hidden-xs">ID</th>
                          <th>会社名</th>
                          <th>操作</th>
                        </tr>
                      </thead>
                      <tbody>
{foreach from=$app_ne.list item="item"}
                        <tr>
                          <td class="hidden-xs text-right">{$item.id}</td>
                          <td>{$item.name|escape}</td>
                          <td>
                            <button class="btn btn-xs btn-success" type="submit" name="action_admin_customer_update_input" value="{$item.id}">
                              <i class="icon-edit bigger-130"></i> 変更
                            </button>
                            <button class="btn btn-xs btn-danger" type="submit" name="action_admin_customer_delete_confirm" value="{$item.id}">
                              <i class="icon-trash bigger-130"></i> 削除
                            </button>
                            <button class="btn btn-xs btn-info" type="submit" name="action_admin_customer_site_list" value="{$item.id}">
                              <i class="icon-file bigger-130"></i> サイト管理
                            </button>
                          </td>
                        </tr>
{foreachelse}
                        <tr>
                          <td colspan="7">{$smarty.const.ERR_MSG_NO_DATA}</td>
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
{include file="admin/middle_footer.tpl"}
{include file="admin/footer.tpl"}