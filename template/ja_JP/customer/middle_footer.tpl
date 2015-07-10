      <a href="#" id="btn-scroll-up" class="btn-scroll-up btn btn-sm btn-inverse">
        <i class="icon-double-angle-up icon-only bigger-110"></i>
      </a>
    </div>{* /.main-container *}
    {* basic scripts *}

    <!--[if !IE]> -->
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
    <script>
      window.jQuery || document.write("<script src={$config.url}/assets/js/jquery-2.0.3.min.js'>"+"<"+"/script>");
    </script>
    <!-- <![endif]-->

    <!--[if IE]>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script>
      window.jQuery || document.write("<script src='/assets/js/jquery-1.10.2.min.js'>"+"<"+"/script>");
    </script>
    <![endif]-->

    <script>
      if("ontouchend" in document) document.write("<script src='{$config.url}assets/js/jquery.mobile.custom.min.js'>"+"<"+"/script>");
    </script>
    <script src="{$config.url}assets/js/bootstrap.min.js"></script>
    <script src="{$config.url}assets/js/typeahead-bs2.min.js"></script>

		<script src="{$config.url}assets/js/jquery-ui-1.10.3.custom.min.js"></script>

    {* page specific plugin scripts *}

    <!--[if lte IE 8]>
      <script src="{$config.url}assets/js/excanvas.min.js"></script>
    <![endif]-->

    {* ace scripts *}
    <script src="{$config.url}assets/js/ace-elements.min.js"></script>
    <script src="{$config.url}assets/js/ace.min.js"></script>

    {* inline scripts related to this page *}
