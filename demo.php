<?php
set_include_path(get_include_path().PATH_SEPARATOR.dirname(__FILE__).'/Moysklad'.
                                    PATH_SEPARATOR.dirname(__FILE__).'/Zend');

require_once 'Moysklad/Settings.php';
require_once 'Moysklad/Factory.php';

$settings = new Moysklad_Settings(array('apiUrl'=>'https://online.moysklad.ru',
                                        'orderSourceStoreId'=>'2FCk4O0miHiAsF6tGLdON2',
                                        'orderSourceAgentId'=>'ylWsocRghjeTSKDM7Plmm3',
                                        'username'=>'<your username>',
                                        'password'=>'<your password>'));

$factory = new Moysklad_Factory($settings);

$repo = $factory->getRepository('Leftovers');

print_r($repo->fetchAll());