<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title>ログイン｜{$config.site_name|escape}</title>

    <meta name="description" content="User login page">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {* basic styles *}

    <link rel="stylesheet" href="{$config.url}assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="{$config.url}assets/css/font-awesome.min.css">

    <!--[if IE 7]>
      <link rel="stylesheet" href="{$config.url}assets/css/font-awesome-ie7.min.css">
    <![endif]-->

    {* page specific plugin styles *}

    {* fonts *}

    <link rel="stylesheet" href="{$config.url}assets/css/ace-fonts.css">

    {* ace styles *}

    <link rel="stylesheet" href="{$config.url}assets/css/ace.min.css">
    <link rel="stylesheet" href="{$config.url}assets/css/ace-rtl.min.css">

    <!--[if lte IE 8]>
      <link rel="stylesheet" href="{$config.url}assets/css/ace-ie.min.css">
    <![endif]-->

    {* inline styles related to this page *}

    {* HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries *}

    <!--[if lt IE 9]>
    <script src="{$config.url}assets/js/html5shiv.js"></script>
    <script src="{$config.url}assets/js/respond.min.js"></script>
    <![endif]-->
  </head>

  <body class="login-layout" style="background:#fff;">
    <div class="main-container">
      <div class="main-content">
        <div class="row">
          <div class="col-sm-10 col-sm-offset-1">
            <div class="login-container">
              <div class="center">
                <h1>
                  <img src="{$config.url}assets/img/login_logo.png" alt="TELシル">
                </h1>
              </div>
              <div class="space-6"></div>
              <div class="position-relative">
                <div id="login-box" class="login-box visible widget-box no-border" style="background:#fff;">
                  <div class="widget-body" style="background:#fff;">
                    <div class="widget-main" style="background:#fff;">
                      <h4 class="header blue lighter bigger">
                        <i class="icon-coffee green"></i>
                        ログイン情報を入力してください
                      </h4>
                      <div class="space-6"></div>
                      <form method="post" action="./">
                        {msg name="invalid" type="full" offset=0}
                        <fieldset>
                          <label class="block clearfix">
                            <span class="block input-icon input-icon-right">
                              <input type="text" class="form-control" placeholder="メールアドレス" name="mail" value="{$form.mail}">
                              <i class="icon-user"></i>
                            </span>
                          </label>
                          {msg name="mail" type="full" offset=0}
                          <label class="block clearfix">
                            <span class="block input-icon input-icon-right">
                              <input type="password" class="form-control" placeholder="パスワード" name="password" value="">
                              <i class="icon-lock"></i>
                            </span>
                          </label>
                          {msg name="password" type="full" offset=0}
                          <div class="space"></div>
                          <div class="clearfix">
                            {csrfid}
                            <button type="submit" name="action_customer_login_complete" class="width-35 pull-right btn btn-sm btn-primary" value="true">
                              <i class="icon-key"></i>
                              ログイン
                            </button>
                          </div>
                          <div class="space-4"></div>
                        </fieldset>
                      </form>
                    </div>{* /widget-main *}
                  </div>{* /widget-body *}
                </div>{* /login-box *}
              </div>{* /position-relative *}
            </div>
          </div>{* /.col *}
        </div>{* /.row *}
      </div>
    </div>{* /.main-container *}
    {* basic scripts *}
  </body>
</html>
