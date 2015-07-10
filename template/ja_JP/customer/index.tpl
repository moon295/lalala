{include file="customer/header.tpl" title="ホーム"}
      <div class="main-container-inner">
{include file="customer/sidebar.tpl"}
        <div class="main-content">
{* パンくず *}
          <div class="breadcrumbs" id="breadcrumbs">
            <script>
              {literal}try{ace.settings.check('breadcrumbs' , 'fixed')}catch(e){}{/literal}
            </script>
            <ul class="breadcrumb">
              <li><i class="icon-home home-icon"></i> ホーム</li>
            </ul>{* .breadcrumb *}
          </div>
{* パンくず *}

          <div class="page-content">
            <div class="page-header">
              <h1>ホーム</h1>
            </div>{* /.page-header *}

            <div class="row">
              <div class="col-xs-12">
{* PAGE CONTENT BEGINS *}
{include file="customer/message.tpl"}
{* PAGE CONTENT ENDS *}
              </div>{* /.col *}
            </div>{* /.row *}
          </div>{* /.page-content *}
        </div>{* /.main-content *}

      </div>{* /.main-container-inner *}
{include file="customer/middle_footer.tpl"}
{include file="customer/footer.tpl"}