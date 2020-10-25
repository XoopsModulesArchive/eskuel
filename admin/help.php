<?php

### help.php

require __DIR__ . '/config.inc.php';
require __DIR__ . '/include/setup.inc.php';
require __DIR__ . '/include/functions.inc.php';
require __DIR__ . '/include/help.inc.php';

### Getting current cfg and setting the design vars
$globalConfig = select_config();
$tplConfig = $globalConfig['tpl'];
$sqlConfig = $globalConfig['sql'];
$txt = $globalConfig['txt'];
$design = $globalConfig['design'];

$colors = $design['colors'];
$font = $design['font'];

$metaNavBar = 'eskuel > ' . $txt['help'];
$page_title = $metaNavBar;
$output = '';
require __DIR__ . '/include/design.inc.php';

switch ($action) {
    case 'data_types':
        $output .= data_types();
        break;
    default:
        @require __DIR__ . '/help/' . $txt['config_name'] . '/' . $_GET['action'] . '.inc.php';
        if ('' == $help) {
            ### Help not found or there is an error, displaying an error message

            $output .= '<B>' . $txt['help_not_found'] . '</B>';
        } else {
            $output .= $help;
        }
        break;
}
$output .= '<BR><BR><CENTER><A href="javascript:window.close();">' . $txt['help_close'] . '</A></CENTER>';
display_help($output, 1);
