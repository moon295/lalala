<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title>{if $title}{$title}｜{/if}{$config.site_name|escape}</title>

    <meta name="description" content="overview &amp; stats">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {* basic styles *}

    <link href="{$config.url}assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{$config.url}assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="{$config.url}assets/css/jquery-ui-1.10.3.full.min.css">

    <!--[if IE 7]>
      <link rel="stylesheet" href="{$config.url}assets/css/font-awesome-ie7.min.css">
    <![endif]-->

    {* page specific plugin styles *}

    {* fonts *}

    <link href="http://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
<!--
    <link rel="stylesheet" href="{$config.url}assets/css/ace-fonts.css" />
-->

    {* ace styles *}

    <link rel="stylesheet" href="{$config.url}assets/css/ace.min.css">
    <link rel="stylesheet" href="{$config.url}assets/css/ace-rtl.min.css">
    <link rel="stylesheet" href="{$config.url}assets/css/ace-skins.min.css">

    <!-- カスタム styles -->
    <link rel="stylesheet" href="{$config.url}assets/css/style.css" />

    <!--[if lte IE 8]>
      <link rel="stylesheet" href="{$config.url}assets/css/ace-ie.min.css">
    <![endif]-->

    {* inline styles related to this page *}

    {* ace settings handler *}

    <script src="{$config.url}assets/js/ace-extra.min.js"></script>

    {* HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries *}

    <!--[if lt IE 9]>
    <script src="{$config.url}assets/js/html5shiv.js"></script>
    <script src="{$config.url}assets/js/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>
    <div class="navbar navbar-default" id="navbar" style="background:#24a2dd;">
      <script>
        {literal}try{ace.settings.check('navbar' , 'fixed')}catch(e){}{/literal}
      </script>

      <div class="navbar-container" id="navbar-container">
        <div class="navbar-header pull-left">
          <a href="{$config.url}admin/" class="navbar-brand">
            ラララ天神橋筋
          </a>{* /.brand *}
        </div>{* /.navbar-header *}

        <div class="navbar-header pull-right" role="navigation">
          <ul class="nav ace-nav">
            <li class="light-blue">
              <a data-toggle="dropdown" href="#" class="dropdown-toggle" style="background:#1492cd;">
                <i class="icon-user"></i>
                <span class="user-info">
                  <small>ようこそ</small>
                  {$smarty.session.admin.login.name|escape}様
                </span>
                <i class="icon-caret-down"></i>
              </a>

              <ul class="user-menu pull-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
                <li>
                  <a href="?action_admin_login_logout=true">
                    <i class="icon-off"></i>
                    ログアウト
                  </a>
                </li>
              </ul>
            </li>
          </ul>{* /.ace-nav *}
        </div>{* /.navbar-header *}
      </div>{* /.container *}
    </div>
    <div class="main-container" id="main-container">
      <script>
        {literal}try{ace.settings.check('main-container', 'fixed')}catch(e){}{/literal}
      </script>
