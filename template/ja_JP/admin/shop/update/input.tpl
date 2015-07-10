{include file="admin/header.tpl" title="顧客変更入力｜顧客管理"}
      <div class="main-container-inner">
{include file="admin/sidebar.tpl"}
        <div class="main-content">
{include file="admin/breadcrumb.tpl" step="icon-user:`$config.url`admin/?action_admin_customer_list=true:顧客管理|::顧客変更入力"}

          <div class="page-content">
            <div class="page-header">
              <h1><i class="icon-user"></i> 顧客管理</h1>
            </div>{* /.page-header *}

            <div class="row">
              <div class="col-xs-12">
{* PAGE CONTENT BEGINS *}
                <form method="post" class="form-horizontal" role="form">
                  <div class="form-group{has_error name='company_name'}">
                    <label class="control-label col-sm-2">会社名<i class="icon-asterisk red"></i></label>
                    <div class="col-sm-8">
                      <input type="text" name="company_name" value="{$form.company_name}" class="form-control">
                    </div>
                  </div>
                  {msg name='company_name'}

                  <div class="form-group{has_error name='name_mei,name_sei'}">
                    <label class="control-label col-sm-2">担当者</label>
                    <div class="col-sm-4{has_error name='name_sei'}">
                      <div class="input-group">
                        <span class="input-group-addon">姓</span>
                        <input type="text" name="name_sei" value="{$form.name_sei}" class="form-control">
                      </div>
                    </div>
                    <div class="col-sm-4{has_error name='name_mei'}">
                      <div class="input-group">
                        <span class="input-group-addon">名</span>
                        <input type="text" name="name_mei" value="{$form.name_mei}" class="form-control">
                      </div>
                    </div>
                  </div>
                  {msg name='name_sei'}
                  {msg name='name_mei'}

                  <div class="form-group{has_error name='mail'}">
                    <label class="control-label col-sm-2">メールアドレス</label>
                    <div class="col-sm-8">
                      <input type="text" name="mail" value="{$form.mail}" class="form-control">
                    </div>
                  </div>
                  {msg name='mail'}

                  <div class="form-group{has_error name='password'}">
                    <label class="control-label col-sm-2">パスワード</label>
                    <div class="col-sm-8">
                      <input type="text" name="password" class="form-control">
                      {if $form.password}パスワードはすでに設定されています。
                      {else}パスワードは未設定です。顧客がログインする場合は必ず設定が必要となります。{/if}
                    </div>
                  </div>
                  {msg name='password'}

                  <div class="form-group{has_error name='suspend_flg'}">
                    <label class="control-label col-sm-2">状況</label>
                    <div class="col-sm-4">
                      <input type="hidden" name="suspend_flg" value="0">
                      <label><input type="checkbox" id="suspend_flg" name="suspend_flg" class="ace ace-switch ace-switch-suspend" value="1"{if $form.suspend_flg} checked{/if}><span class="lbl"></span></label>
                    </div>
                  </div>
                  {msg name='suspend_flg'}

                  <div class="form-actions">
                    <input type="hidden" name="id" value="{$form.id}">
                    <input type="hidden" name="current_page" value="{$session.admin.customer.list.current_page}">
                    <button type="submit" name="action_admin_customer_update_confirm" value="true" class="btn btn-primary pull-right"><i class="icon-desktop"></i> 確認する</button>
                    <button type="submit" name="action_admin_customer_list" value="true" class="btn"><i class="icon-long-arrow-left"></i> 一覧へ戻る</button>
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

