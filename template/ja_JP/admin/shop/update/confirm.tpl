{include file="admin/header.tpl" title="顧客変更確認｜顧客管理"}
      <div class="main-container-inner">
{include file="admin/sidebar.tpl"}
        <div class="main-content">
{include file="admin/breadcrumb.tpl" step="icon-user:`$config.url`admin/?action_admin_customer_list=true:顧客管理|::顧客変更確認"}
          <div class="page-content">
            <div class="page-header">
              <h1><i class="icon-user"></i> 顧客管理</h1>
            </div>{* /.page-header *}

            <div class="row">
              <div class="col-xs-12">
{* PAGE CONTENT BEGINS *}
                <form method="post" class="form-horizontal" role="form">
                  <div class="form-group">
                    <label class="control-label col-sm-2">会社名</label>
                    <div class="input-group col-sm-10 form-control-static">{$form.company_name}</div>
                  </div>
                  <div class="form-group">
                    <label class="control-label col-sm-2">担当者</label>
                    <div class="input-group col-sm-10 form-control-static">{$form.name_sei}　{$form.name_mei}</div>
                  </div>
                  <div class="form-group">
                    <label class="control-label col-sm-2">メールアドレス</label>
                    <div class="input-group col-sm-10 form-control-static">{$form.mail}</div>
                  </div>
                  <div class="form-group">
                    <label class="control-label col-sm-2">パスワード</label>
                    <div class="input-group col-sm-10 form-control-static">{$form.password}</div>
                  </div>
                  <div class="form-group">
                    <label class="control-label col-sm-2">状態</label>
                    <div class="input-group col-sm-10 form-control-static">{$config.suspend[$form.suspend_flg]|escape}</div>
                  </div>
                  <div class="form-actions">
                    {csrfid}
                    {uniqid}
                    <input type="hidden" name="id" value="{$form.id}">
                    <button type="submit" name="action_admin_customer_update_input" value="true" class="btn"><i class="icon-long-arrow-left"></i> 戻る</button>
                    <button type="submit" name="action_admin_customer_update_complete" value="true" class="btn btn-primary pull-right"><i class="icon-edit"></i> 変更</button>
                  </div>
                </form>
                {* PAGE CONTENT ENDS *}
              </div>{* /.col *}
            </div>{* /.row *}
          </div>{* /.page-content *}
        </div>{* /.main-content *}
      </div>{* /.main-container-inner *}
{include file="admin/middle_footer.tpl"}
{include file="admin/footer.tpl"}
