<?php

//Ce fichier contient des modifications par rapport à la version originale

$output = '';
$dont_output = 0;

if (isset($_GET['action'])) {
    $action = $_GET['action'];
} elseif (isset($_POST['action'])) {
    $action = $_POST['action'];
} else {
    $action = '';
}

//Modifications pour xoops par albnn@netcourrier.com

require __DIR__ . '/config.inc.php';
require __DIR__ . '/include/setup.inc.php';
require __DIR__ . '/include/functions.inc.php';
require __DIR__ . '/include/color_grid.inc.php';

$globalConfig = select_config();
$tplConfig = $globalConfig['tpl'];
$sqlConfig = $globalConfig['sql'];
$txt = $globalConfig['txt'];
$design = $globalConfig['design'];

$colors = $design['colors'];
$font = $design['font'];

require __DIR__ . '/include/design.inc.php';

$metaNavBar = 'eSKUeL > ' . $txt['setup'];
$page_title = $metaNavBar;
$output .= '<FONT ' . $font . '><FONT size="2"><B>' . $txt['setup_title'] . ' :</B></FONT>';
$output .= '<BR><BR>';

switch ($action) {
    case '':
        $output .= show_setup_hp();
        break;
    case 'mod_globals':
        $output .= modify_globals($conf);
        break;
    case 'tpl_create':
        $output .= tpl_create();
        break;
    case 'tpl_mod':
        $output .= tpl_mod();
        break;
    case 'tpl_preview':
        $output .= tpl_preview($new_colors);
        $dont_show_logo = 1;
        $page_title = 'eskuel > Preview';
        $dont_output = 1;
        break;
    case 'tpl_del':
        $output .= tpl_del();
        break;
}

if (!$dont_output) {
    $output .= return_2_eskuel();
}

//Modifications pour xoops par albnn@netcourrier.com
if ('tpl_preview' == $action) {
    display_help($output, 1);
} else {
    display_design($output, 1);
}
