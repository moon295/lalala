<?php
/**
 *  masterのDB設定は必ず行ってください。
 **/
if (!LOCALHOST) {
    define('DB_LIST', serialize(array('master' => array('server' => 'localhost',
                                                        'user'   => 'root',
                                                        'pass'   => 'password',
                                                        'dbname' => 'tenjinbashisuji',
                                                       ),
                                      'slave'  => array('server' => 'localhost',
                                                        'user'   => 'root',
                                                        'pass'   => 'password',
                                                        'dbname' => 'tenjinbashisuji',
                                                       ),
                                     )
                               )
          );
} else {
    define('DB_LIST', serialize(array('master' => array('server' => 'localhost',
                                                        'user'   => 'root',
                                                        'pass'   => 'pass',
                                                        'dbname' => 'lpo',
                                                       ),
                                      'slave'  => array('server' => 'localhost',
                                                        'user'   => 'root',
                                                        'pass'   => 'pass',
                                                        'dbname' => 'lpo',
                                                       ),
                                     )
                               )
          );
}
?>
