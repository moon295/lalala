<?php
$function = get_defined_functions();
if (!in_array('pr', $function['user'])) {
    function pr($data)
    {
        $trace = debug_backtrace();
        if (defined('CLI_MODE') && CLI_MODE || !isset($_SERVER['SERVER_ADDR'])) {
            printf("%s(%d)\n", $trace[0]['file'], $trace[0]['line']);
            if (is_null($data)) {
                print '<i>NULL</i>';
            } else {
                print_r($data);
            }
            print "\n\n";
        } else {
            print "<pre style=\"text-align:left; color:#FFFFFF; background-color:#000000;\">";
            printf("%s(%d)\n", $trace[0]['file'], $trace[0]['line']);
            if (is_null($data)) {
                print '<i>NULL</i>';
            } else {
                print_r($data);
            }
            print "</pre>";
        }
    }
}
if (!in_array('vd', $function['user'])) {
    function vd($data)
    {
        $trace = debug_backtrace();
        print "<pre style=\"text-align:left; color:#FFFFFF; background-color:#000000;\">";
        printf("%s(%d)\n", $trace[0]['file'], $trace[0]['line']);
        var_dump($data);
        print "</pre>";
    }
}
if (!in_array('trace', $function['user'])) {
    function trace()
    {
        $debug_backtrace = debug_backtrace();
        $trace = array();
        foreach ($debug_backtrace as $key => $value) {
            $trace[] = array('file'     => $value['file'],
                             'line'     => $value['line'],
                             'class'    => $value['class'],
                             'function' => $value['function'],
                            );
        }
        pr($trace);
    }
}
if (!in_array('fpr', $function['user'])) {
    function fpr($data, $fn, $mode = 'a')
    {
        $fp = fopen($fn, $mode);
        ob_start();
        print_r($data);
        fwrite($fp, ob_get_contents());
        ob_end_clean();
        fclose($fp);
    }
}
?>