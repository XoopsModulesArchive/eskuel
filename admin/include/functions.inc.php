<?php

//Ce fichier contient des modifications par rapport à la version originale

$version = '1.0.2';

$data_types = [
    'TINYINT',
    'SMALLINT',
    'MEDIUMINT',
    'INT',
    'BIGINT',
    'FLOAT',
    'DOUBLE',
    'DECIMAL',
    'DATE',
    'DATETIME',
    'TIMESTAMP',
    'TIME',
    'YEAR',
    'CHAR',
    'VARCHAR',
    'TINYBLOB',
    'TINYTEXT',
    'TEXT',
    'BLOB',
    'MEDIUMBLOB',
    'MEDIUMTEXT',
    'LONGBLOB',
    'LONGTEXT',
    'ENUM',
    'SET',
];
$data_attributes = [
    '',
    'BINARY',
    'UNSIGNED',
    'UNSIGNED ZEROFILL',
];
$data_functions = [
    '',
    'ASCII',
    'CHAR',
    'SOUNDEX',
    'ENCRYPT',
    'LCASE',
    'UCASE',
    'NOW',
    'PASSWORD',
    'ENCODE',
    'DECODE',
    'MD5',
    'RAND',
    'LAST_INSERT_ID',
    'COUNT',
    'AVG',
    'SUM',
    'CURDATE',
    'CURTIME',
    'FROM_DAYS',
    'FROM_UNIXTIME',
    'PERIOD_ADD',
    'PERIOD_DIFF',
    'TO_DAYS',
    'USER',
    'WEEKDAY',
];

function octet_2_kilo($nb)
{
    $kilo = $nb / 1024;

    $number = number_format($kilo, 1, ',', ' ');

    return $number;
}

function reg_glob($var)
{
    global $_GET, $_POST;

    if (isset($_GET[$var])) {
        return $_GET[$var];
    } elseif (isset($_POST[$var])) {
        return $_POST[$var];
    }

    return '';
}

//Fonction modifiée par albnn@netcourrier.com pour xoops
function select_lang_config()
{
    global $xoopsConfig;

    if (file_exists('./lang/' . $xoopsConfig['language'] . '.inc.php')) {
        include './lang/' . $xoopsConfig['language'] . '.inc.php';
    } else {
        include './lang/english.inc.php';
    }

    return $txt;
}

function select_tpl_config()
{
    global $HTTP_COOKIE_VARS, $_POST;

    global $confDB;

    $config_selector = $_POST['config_selector'] ?? '';

    $tpl_config_cookie = $HTTP_COOKIE_VARS['ConfTplCookie'] ?? '';

    $force_config = $_POST['tpl_config'] ?? '';

    if ('' != $config_selector) {
        $tpl_conf = '';
    } else {
        if ('' != $force_config) {
            $tpl_conf = $force_config;
        } elseif ('' != $tpl_config_cookie) {
            $tpl_conf = $tpl_config_cookie;
        } else {
            $tpl_conf = getGoodConfigArg('tpl');
        }

        $confDB[getGoodConfig()]['tpl'] = $tpl_conf;
    }

    return $tpl_conf;
}

function select_colors_config()
{
    global $_POST, $HTTP_COOKIE_VARS;

    global $conf, $confDB;

    global $globalConfig;

    $tpl_config = $_POST['tpl_config'] ?? '';

    $use_other_img = 0;

    $sql_conf_id = getGoodConfig();

    if ('' != $tpl_config) {
        if ('null-tpl' == $tpl_config) {
            require __DIR__ . '/include/default_colors.inc.php';
        } else {
            require __DIR__ . '/tpl/' . $tpl_config . '/colors.inc.php';

            $confDB[$sql_conf_id]['tpl'] = $tpl_config;
        }
    } elseif ('' != $confDB[$sql_conf_id]['tpl'] && (is_file('./tpl/' . $confDB[$sql_conf_id]['tpl'] . '/colors.inc.php'))) {
        require __DIR__ . '/tpl/' . $confDB[$sql_conf_id]['tpl'] . '/colors.inc.php';
    } elseif (isset($HTTP_COOKIE_VARS['ConfTplCookie']) && 'null-tpl' == $HTTP_COOKIE_VARS['ConfTplCookie']) {
        require __DIR__ . '/include/default_colors.inc.php';
    } else {
        ### The selected tpl is not available (deleted ?) setting it to default

        setcookie('ConfTplCookie', '');

        require __DIR__ . '/include/default_colors.inc.php';
    }

    if (1 == $use_other_img) {
        $img_path = 'tpl/' . $confDB[$sql_conf_id]['tpl'] . '/';
    } else {
        $img_path = '';
    }

    $colors = [
        'name' => $confDB[$sql_conf_id]['tpl'],
'bgcolor' => $bgcolor,
'table_bg' => $table_bg,
'titre_bg' => $titre_bg,
'titre_font' => $titre_font,
'bordercolor' => $bordercolor,
'vlink' => $vlink,
'link' => $link,
'text' => $text,
'alink' => $alink,
'trou_bg' => $trou_bg,
'trou_border' => $trou_border,
'trou_font' => $trou_font,
'bosse_bg' => $bosse_bg,
'bosse_font' => $bosse_font,
'bosse_border' => $bosse_border,
'pick_bg' => $pick_bg,
'pick_border' => $pick_border,
'img_path' => $img_path,
'tpl_type' => $tpl_type,
    ];

    $design = ['colors' => $colors, 'font' => $font];

    return $design;
}

function select_sql_config()
{
    global $_POST, $HTTP_COOKIE_VARS;

    global $conf, $confDB;

    $sql_config_cookie = $HTTP_COOKIE_VARS['ConfDBCookie'] ?? '';

    $force_config = $_POST['config_selector'] ?? '';

    $sql_conf = '';

    if ('' != $force_config) {
        $sql_conf_id = $force_config;
    } elseif ('' != $sql_config_cookie) {
        $sql_conf_id = $sql_config_cookie;
    } else {
        $sql_conf_id = $conf['defaultConf'];
    }

    if (isset($confDB[$sql_conf_id])) {
        $sql_conf = [
            'HOST' => $confDB[$sql_conf_id]['host'],
'DB' => $confDB[$sql_conf_id]['db'],
'USER' => $confDB[$sql_conf_id]['user'],
'PASSWORD' => $confDB[$sql_conf_id]['password'],
'ONLY_DB' => $confDB[$sql_conf_id]['db'],
        ];
    }

    return $sql_conf;
}

function select_config()
{
    global $HTTP_COOKIE_VARS, $_POST;

    global $conf, $confDB;

    $globalConfig['sql'] = select_sql_config();

    $globalConfig['tpl'] = select_tpl_config();

    $globalConfig['txt'] = select_lang_config();

    $globalConfig['design'] = select_colors_config();

    return $globalConfig;
}

#####################################
# Build the titles and windows.status
# of a link
# $txt = Text to display in the tooltip
# and in the windows status bar
#
#####################################

function link_status($text)
{
    global $font, $colors, $txt;

    $output = 'title="' . $text . '"';

    return $output;
}

function select_type($select_name, $preselected = '')
{
    global $data_types;

    $selected = '';

    $output = '<SELECT name=' . $select_name . ' class="trous">';

    for ($i = 0, $iMax = count($data_types); $i < $iMax; $i++) {
        if (($preselected == $i && is_int($preselected)) || ($preselected == $data_types[$i])) {
            $selected = 'SELECTED';
        } else {
            $selected = '';
        }

        $output .= '<OPTION VALUE=' . $i . ' ' . $selected . '>' . $data_types[$i] . '</OPTION>' . "\n";
    }

    $output .= '</SELECT>';

    return $output;
}

function select_attribute($select_name, $preselected = '-')
{
    global $data_attributes;

    $output = '<SELECT name=' . $select_name . ' class="trous">';

    for ($i = 0, $iMax = count($data_attributes); $i < $iMax; $i++) {
        if (($preselected == $i) || ($preselected == $data_attributes[$i])) {
            $selected = 'SELECTED';
        } else {
            $selected = '';
        }

        $output .= '<OPTION VALUE=' . $i . ' ' . $selected . '>' . $data_attributes[$i] . '</OPTION>' . "\n";
    }

    $output .= '</SELECT>';

    return $output;
}

function select_function($select_name, $preselected = '-')
{
    global $data_functions;

    $output = '<SELECT name="' . $select_name . '" class="trous">';

    for ($i = 0, $iMax = count($data_functions); $i < $iMax; $i++) {
        if (($preselected == $i) || ($preselected == $data_functions[$i])) {
            $selected = 'SELECTED';
        } else {
            $selected = '';
        }

        $output .= '<OPTION VALUE=' . $i . ' ' . $selected . '>' . $data_functions[$i] . '</OPTION>' . "\n";
    }

    $output .= '</SELECT>';

    return $output;
}

function select_tpl($preselected = '')
{
    global $txt, $HTTP_COOKIE_VARS, $_POST;

    $output = '';

    $tpl_config_cookie = $HTTP_COOKIE_VARS['ConfTplCookie'] ?? '';

    $config_selector = $_POST['config_selector'] ?? '';

    $d = dir('./tpl');

    if ('' != $config_selector) {
        $preselected = getGoodConfigArg('tpl');

        if ('' == $preselected) {
            $preselected = 'null';
        }
    } else {
        if ('' != $tpl_config_cookie && '' == $preselected) {
            $preselected = $tpl_config_cookie;
        }

        if ('' == $tpl_config_cookie && '' == $preselected) {
            $preselected = getGoodConfigArg('tpl');

            if ('' == $preselected) {
                $preselected = 'null-tpl';
            }
        }
    }

    while ($entry = $d->read()) {
        if ('.' != $entry && '..' != $entry && @is_dir('./tpl/' . $entry)) {
            $avl_tpl[] = $entry;
        }
    }

    $d->close();

    asort($avl_tpl);

    while (list($key, $val) = each($avl_tpl)) {
        if ($preselected == $val) {
            $selected = 'SELECTED';
        } else {
            $selected = '';
        }

        $output .= '<OPTION value="' . $val . '" ' . $selected . '>' . $val . '</OPTION>';
    }

    if ('null-tpl' == $preselected) {
        $selected = 'SELECTED';
    } else {
        $selected = '';
    }

    $output .= '<OPTION value="null-tpl" ' . $selected . '>' . $txt['default_value'] . '</OPTION>';

    return $output;
}

function getGoodConfig()
{
    global $HTTP_COOKIE_VARS, $_POST;

    global $conf;

    $configSelect = $_POST['config_selector'] ?? '';

    $cookieConfig = $HTTP_COOKIE_VARS['ConfDBCookie'] ?? '';

    if ('' == $configSelect) {
        if ('' == $cookieConfig) {
            $feedback = $conf['defaultConf'];
        } else {
            $feedback = $cookieConfig;
        }
    } else {
        $feedback = $configSelect;
    }

    return $feedback;
}

function getGoodConfigArg($arg)
{
    global $confDB;

    return $confDB[getGoodConfig()][$arg] ?? '';
}

function show_hp()
{
    global $confDB, $HTTP_COOKIE_VARS, $_POST, $conf;

    global $font, $txt, $tplConfig, $version;

    global $main_DB;

    $preselected_cookie = $HTTP_COOKIE_VARS['ConfDBCookie'] ?? '';

    $config_selector = $_POST['config_selector'] ?? '';

    $lang_config = $_POST['lang_config'] ?? '';

    $do_action = $_POST['do_action'] ?? '';

    $tpl_config = $_POST['tpl_config'] ?? '';

    $MySQL_Version = $main_DB->Infos['Full_Version'];

    $preselected_lang = '';

    $preselected_tpl = '';

    $preselected = getGoodConfig();

    if ('setCookieDB' == $do_action) {
        setcookie('ConfDBCookie', $config_selector);

        setcookie('ConfTplCookie', '');

        $preselectedConf = $config_selector;
    }

    if ('setCookieLang' == $do_action) {
        setcookie('ConfLangCookie', $lang_config);

        $preselected_lang = $lang_config;
    }

    if ('setCookieTpl' == $do_action) {
        setcookie('ConfTplCookie', $tpl_config);

        $preselected_tpl = $tpl_config;
    }

    $output = '<SCRIPT language="Javascript">' . "\n";

    $output .= '	function chg_cfg() {' . "\n";

    $output .= '		document.configselect.submit();' . "\n";

    $output .= '	}' . "\n";

    $output .= '	function chg_lang() {' . "\n";

    $output .= '		document.langselect.submit();' . "\n";

    $output .= '	}' . "\n";

    $output .= '	function chg_tpl() {' . "\n";

    $output .= '		document.tplselect.submit();' . "\n";

    $output .= '	}' . "\n";

    $output .= '</SCRIPT>' . "\n";

    $output .= '<TABLE border="0" width=100%>';

    $output .= '<TR>';

    $output .= '    <TD align="center"valign="top">';

    $output .= '        <FONT ' . $font . '>';

    $output .= '        <B>' . $txt['Welcome'] . '<br><br></B>';

    $output .= '        </FONT>';

    $output .= '    </TD>';

    $output .= '</TR><TR>';

    $output .= '    <TD align="center" valign="top">';

    $output .= '        <TABLE><TR><TD align="center"valign="top">';

    $output .= '		<FONT ' . $font . '><B>';

    $output .= '        	' . $txt['setup_tpl'] . '&nbsp;:';

    $output .= '        	</B></FONT>';

    $output .= '        <TD align="center"valign="top">';

    $output .= '        	<FORM action="main.php" name="tplselect" method="POST">';

    $output .= '        	<INPUT type="hidden" name="do_action" value="setCookieTpl">';

    $output .= '        	<SELECT name="tpl_config" onChange="chg_tpl();" class="trous">';

    $output .= select_tpl($preselected_tpl);

    $output .= '        	</SELECT>';

    $output .= '        	</FORM>';

    $output .= '        </TD></TR></TABLE>';

    $output .= '    </TD>';

    $output .= '</TR>';

    $output .= '</TABLE>';

    $output .= show_tips(0);

    return $output;
}

function magic_quote_bypass($arg)
{
    if (function_exists('get_magic_quotes_gpc') && @get_magic_quotes_gpc()) {
        $arg = stripslashes($arg);
    }

    return $arg;
}

function start_timing()
{
    $time_grab = explode(' ', microtime());

    $start_time = $time_grab[1] . mb_substr($time_grab[0], 1);

    return $start_time;
}

function print_timing($start_time)
{
    global $txt;

    $timeparts = explode(' ', microtime());

    $end_time = $timeparts[1] . mb_substr($timeparts[0], 1);

    $timing = number_format($end_time - $start_time, 4);

    return $txt['timed_query'] . ' ' . $timing . ' ' . $txt['secondes'] . '<BR>';
}

function select_database($sql_ref, $select_name, $preselected = '')
{
    $output = '<SELECT name=' . $select_name . ' class="trous">';

    if ('' == getGoodConfigArg('db')) {
        $sql_ref->query('SHOW DATABASES');

        while ($arrDB = $sql_ref->next_record()) {
            if ($preselected == $arrDB[0]) {
                $selected = 'SELECTED';
            } else {
                $selected = '';
            }

            $output .= '<OPTION VALUE=' . $arrDB[0] . ' ' . $selected . '>' . $arrDB[0] . '</OPTION>' . "\n";
        }
    } else {
        $output .= '<OPTION VALUE=' . getGoodConfigArg('db') . '>' . getGoodConfigArg('db') . '</OPTION>' . "\n";
    }

    $output .= '</SELECT>';

    return $output;
}

#####################################
# Build the navigation tree of a DB
# (list only the tables for a given
# DB)
# $sql_ref = SQL link to the base
# $db = the DB which contains the
#       table to show
#####################################
function list_left_table($sql_ref, $db)
{
    global $font, $colors, $txt;

    $img_path = $colors['img_path'];

    $tbl_infos = $sql_ref->getTblsInfos();

    $out = '';

    for ($i = 0; $i < $tbl_infos['Number_Of_Tables']; $i++) {
        $current_tbl = $tbl_infos['Tables_List'][$i];

        $out .= '<tr>' . "\n";

        $out .= '	<td align="left"><img src="' . $img_path . 'img/coin.gif"></td>' . "\n";

        if (0 != $tbl_infos[$current_tbl]['Rows']) {
            $out .= '	<td align="left" nowrap><a href="main.php?db='
                    . urlencode($db)
                    . '&tbl='
                    . urlencode($current_tbl)
                    . '&action=view"><img src="'
                    . $img_path
                    . 'img/tbl.gif" border="0" '
                    . link_status($txt['tbl'] . ' ' . $current_tbl . ' (' . $txt['display_content'] . ')')
                    . '></a><img src="img/vide.gif" width=2 height=1><font '
                    . $font
                    . '><a href="main.php?db='
                    . urlencode($db)
                    . '&tbl='
                    . urlencode($current_tbl)
                    . '" '
                    . link_status($txt['tbl'] . ' ' . $current_tbl)
                    . '>'
                    . $current_tbl
                    . '</a> ('
                    . $tbl_infos[$current_tbl]['Rows']
                    . ')</font></td>'
                    . "\n";

            $out .= '</tr>' . "\n";
        } else {
            $out .= '	<td align="left" nowrap><img src="' . $img_path . 'img/tbl.gif" border="0"  ' . link_status($txt['empty_tbl']) . '><img src="img/vide.gif" width=2 height=1><font ' . $font . '><a href="main.php?db=' . urlencode($db) . '&tbl=' . urlencode($current_tbl) . '"  ' . link_status(
                $txt['tbl'] . ' ' . $current_tbl
            ) . '>' . $current_tbl . '</A> (' . $tbl_infos[$current_tbl]['Rows'] . ')</font></td>' . "\n";

            $out .= '</tr>' . "\n";
        }
    }

    return $out;
}

#####################################
# Find if an element if present
# or not in an array
# => in_array() in php4, php3 compat.
# $elt = element to find in the array
# $arr = the array
#####################################
function is_in_array($elt, $arr)
{
    for ($i = 0, $iMax = count($arr); $i < $iMax; $i++) {
        if ($arr[$i] == $elt) {
            return 1;
        }
    }

    return 0;
}

function get_mysql_version($sql_ref)
{
    @$sql_ref->query("SHOW VARIABLES LIKE 'version'");

    $resultset = $sql_ref->next_record();

    if (is_array($resultset)) {
        $mysql_version = $resultset['Value'];

        $version_array = explode('.', $mysql_version);

        return (int)sprintf('%d%d%02d', $version_array[0], $version_array[1], (int)$version_array[2]);
    }

    return -1;
}

function sql_back_ticks($str, $sql_ref)
{
    if ($sql_ref->Infos['Version'] >= 32306) {
        $str = '`' . $str . '`';
    }

    return $str;
}

#####################################
# Replacement function for
# SHOW CREATE TABLE statement
# $tbl = the table
# $sql_ref = link to the db
#####################################
function show_create_table($tbl, $sql_ref)
{
    $primary = [];

    $primary_part = '';

    $output = 'CREATE TABLE ' . sql_back_ticks($tbl, $sql_ref) . ' (' . "\n";

    $sql_ref->query('SHOW FIELDS FROM ' . sql_back_ticks($tbl, $sql_ref));

    while ($res = $sql_ref->next_record()) {
        $field_name = $res['Field'];

        $field_type = isset($res['Type']) ? ' ' . $res['Type'] : '';

        $field_null = ('YES' == $res['Null']) ? '' : ' NOT NULL ';

        $field_key = $res['Key'];

        $field_default = (isset($res['Default']) && '' != $res['Default']) ? 'DEFAULT \'' . addslashes($res['Default']) . '\' ' : '';

        $field_extra = $res['Extra'];

        $output .= '    ' . sql_back_ticks($field_name, $sql_ref);

        $output .= $field_type . $field_null;

        $output .= $field_default . $field_extra . ",\n";
    }

    ### Key part of the query

    $sql_ref->query('SHOW KEYS FROM ' . sql_back_ticks($tbl, $sql_ref));

    $key_part = '';

    while ($res = $sql_ref->next_record()) {
        $key_name = $res['Key_name'];

        $non_unique = $res['Non_unique'];

        $column_name = $res['Column_name'];

        if ('PRIMARY' == $key_name) {
            $primary[] = $column_name;
        } else {
            if (1 == $non_unique) {
                $key_part .= '    KEY ' . sql_back_ticks($key_name, $sql_ref) . ' (' . sql_back_ticks($column_name, $sql_ref) . '),' . "\n";
            } else {
                $key_part .= '    UNIQUE ' . sql_back_ticks($key_name, $sql_ref) . ' (' . sql_back_ticks($column_name, $sql_ref) . '),' . "\n";
            }
        }
    }

    for ($i = 0, $iMax = count($primary); $i < $iMax; $i++) {
        $primary_part .= sql_back_ticks($primary[$i], $sql_ref);

        if (($i + 1) < count($primary)) {
            $primary_part .= ', ';
        }
    }

    if ('' != $primary_part) {
        $key_part = '    PRIMARY KEY (' . $primary_part . '),' . "\n" . $key_part;
    }

    if ('' != $key_part) {
        $key_part = mb_substr($key_part, 0, -2) . "\n";
    }

    $output .= $key_part;

    $output .= ');' . "\n";

    return $output;
}

function like_table($tbl)
{
    $feedback = stripslashes($tbl);

    $feedback = str_replace('%', '\%', $feedback);

    $feedback = str_replace("'", "\'", $feedback);

    return $feedback;
}

#####################################
# Display a 'return to table XXX' link
# $db = the current database
# $tbl = the table
#####################################
function return_2_table($db, $tbl)
{
    global $txt;

    return '<BR><BR><A href="main.php?db=' . urlencode($db) . '&tbl=' . urlencode($tbl) . '">' . $txt['back_2_tbl'] . ' ' . $tbl . '</A>';
}

#####################################
# Display a 'return to Database XXX' link
# $db = the current database
#####################################
function return_2_db($db)
{
    global $txt;

    return '<BR><BR><A href="main.php?db=' . urlencode($db) . '">' . $txt['back_2_db'] . ' ' . $db . '</A>';
}

function display_bookmarkable_query($query)
{
    global $txt, $colors;

    $feedback = '<FORM action="" method="POST" name="bookmark">';

    $feedback .= '<INPUT type="hidden" name="sql" value="' . base64_encode($query) . '">';

    $feedback .= '</FORM>';

    $feedback .= '<B>' . $txt['sql_query'] . ' :</B><BR>';

    $feedback .= '<A HREF="Javascript:popup(\'popup_bookmark.php?action=add&sql=\'+document.bookmark.sql.value+\'\', \'BookmarkPopup\', 420, 400);"><IMG SRC="'
                 . $colors['img_path']
                 . 'img/bookmark.gif" '
                 . link_status($txt['bkmk_this'])
                 . ' BORDER="0"></A>&nbsp;'
                 . nl2br(htmlentities($query, ENT_QUOTES | ENT_HTML5))
                 . '<BR>';

    return $feedback;
}

function display_field_form($type, $name, $default = '')
{
    switch (1) {
        case eregi('enum', $type):
            $enum_values = explode(',', mb_substr($type, 5, (mb_strlen($type) - 6)));
            $output = '<SELECT NAME="' . $name . '" class="trous">';

            for ($i = 0, $iMax = count($enum_values); $i < $iMax; $i++) {
                # removing the ' around each values

                $display_default_values = mb_substr($enum_values[$i], 1, -1);

                if ($display_default_values == $default) {
                    $selected = 'SELECTED';
                } else {
                    $selected = '';
                }

                $output .= '<OPTION value="' . htmlentities($display_default_values, ENT_QUOTES | ENT_HTML5) . '" ' . $selected . '>' . $display_default_values . '</OPTION>';
            }
            $output .= '</SELECT>';
            break;
        case eregi('set', $type):

            $set_values = explode(',', mb_substr($type, 4, (mb_strlen($type) - 5)));
            $output = '<SELECT NAME="' . $name . '[]" MULTIPLE class="trous">';
            $default = explode(',', $default);

            for ($i = 0, $iMax = count($set_values); $i < $iMax; $i++) {
                # removing the ' around each values

                $display_default_values = mb_substr($set_values[$i], 1, -1);

                if (is_in_array($display_default_values, $default)) {
                    $selected = 'SELECTED';
                } else {
                    $selected = '';
                }

                $output .= '<OPTION value="' . htmlentities($display_default_values, ENT_QUOTES | ENT_HTML5) . '" ' . $selected . '>' . $display_default_values . '</OPTION>';
            }
            $output .= '</SELECT>';
            break;
        case eregi('char', $type):
        case eregi('varchar', $type):
            $var_length = mb_substr($type, mb_strpos($type, '(') + 1, mb_strlen($type) - mb_strpos($type, '(') - 2);
            if ($var_length > 50) {
                $output = '<TEXTAREA name="' . $name . '" rows=5 cols=25 class="trous">' . htmlentities($default, ENT_QUOTES | ENT_HTML5) . '</TEXTAREA>';
            } else {
                $output = '<INPUT type="text" name="' . $name . '" value="' . htmlentities($default, ENT_QUOTES | ENT_HTML5) . '" class="trous">';
            }
            break;
        case eregi('blob', $type):
        case eregi('text', $type):
            $output = '<TEXTAREA name="' . $name . '" rows=5 cols=25 class="trous">' . htmlentities($default, ENT_QUOTES | ENT_HTML5) . '</TEXTAREA>';
            break;
        case eregi('datetime', $type):
            $default = ('0000-00-00 00:00:00' == $default) ? date('Y-m-d H:i:s') : $default;
            $output = $output = '<INPUT type="text" name="' . $name . '" value="' . htmlentities($default, ENT_QUOTES | ENT_HTML5) . '" class="trous">';
            break;
        case eregi('date', $type):
            $default = ('0000-00-00' == $default) ? date('Y-m-d') : $default;
            $output = $output = '<INPUT type="text" name="' . $name . '" value="' . htmlentities($default, ENT_QUOTES | ENT_HTML5) . '" class="trous">';
            break;
        case eregi('time', $type):
            $default = ('00:00:00' == $default) ? date('H:i:s') : $default;
            $output = $output = '<INPUT type="text" name="' . $name . '" value="' . htmlentities($default, ENT_QUOTES | ENT_HTML5) . '" class="trous">';
            break;
        case eregi('year', $type):
            $default = ('0000' == $default && 4 == mb_strlen($default)) ? date('Y') : $default;
            $default = ('00' == $default && 2 == mb_strlen($default)) ? date('y') : $default;
            $output = $output = '<INPUT type="text" name="' . $name . '" value="' . htmlentities($default, ENT_QUOTES | ENT_HTML5) . '" class="trous">';
            break;
        default:
            $output = '<INPUT type="text" name="' . $name . '" value="' . htmlentities($default, ENT_QUOTES | ENT_HTML5) . '" class="trous">';
            break;
    }

    return $output;
}

function priv_str_repeat($str, $count)
{
    $feedback = '';

    for ($i = 0; $i < $count; $i++) {
        $feedback .= $str;
    }

    return $feedback;
}

function show_tips($html, $random = 0, $never = 0, $old_one = 0)
{
    global $HTTP_COOKIE_VARS, $txt, $colors, $font, $conf, $css;

    $css = 12;

    $img_path = $colors['img_path'];

    $tips_cookie = $HTTP_COOKIE_VARS['ConfTipsCookie'] ?? '';

    require __DIR__ . '/lang/tips_' . $txt['config_name'] . '.inc.php';

    if (('never' == $tips_cookie || 'done' == $tips_cookie || !$conf['tipsOfTheDay']) && !$random && !$html) {
        return '';
    }

    $day = date('j');

    if (1 == $random) {
        // mt_srand((double)microtime() * 1000000);

        $randval = random_int(1, count($tips));

        while ($randval == $old_one) {
            $randval = random_int(1, count($tips));
        }

        $tips = $tips[$randval];

        $current_tip = $randval;
    } else {
        $tips = $tips[$day];

        $current_tip = $day;
    }

    if (1 == $html) {
        if (1 == $never) {
            $expiration_date = mktime('23', '59', '59', date('m'), date('d'), date('Y') + 10);

            setcookie('ConfTipsCookie', 'never', $expiration_date);

            echo '<SCRIPT language="Javascript">
					window.close();
				</SCRIPT>';
        }

        $output = '<HTML>
				<HEAD>
				<TITLE>eskuel > '
                  . $txt['tips_day']
                  . '</TITLE>
				<META HTTP-EQUIV="Pragma"  CONTENT="no-cache">
				<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache">
				<META HTTP-EQUIV="Expires" CONTENT="Mon, 06 May 1996 04:57:00 GMT">
				<META HTTP-EQUIV="content-type" CONTENT="text/html; charset='
                  . $txt['charset']
                  . '">
				<style type="text/css">
				<!--
				.trous { background-color: '
                  . $colors['trou_bg']
                  . '; border: 1 '
                  . $colors['trou_border']
                  . ' solid; color: '
                  . $colors['trou_font']
                  . '; font-family: '
                  . $font
                  . '; font-size: '
                  . $css
                  . 'px; scrollbar-3dlight-color:'
                  . $colors['trou_bg']
                  . '; scrollbar-arrow-color:'
                  . $colors['trou_border']
                  . '; scrollbar-base-color:'
                  . $colors['trou_bg']
                  . '; scrollbar-darkshadow-color:'
                  . $colors['trou_bg']
                  . '; scrollbar-face-color:'
                  . $colors['trou_bg']
                  . '; scrollbar-highlight-color:'
                  . $colors['trou_bg']
                  . '; scrollbar-shadow-color:'
                  . $colors['trou_bg']
                  . ' }
				.bosses {  background-color: '
                  . $colors['bosse_bg']
                  . '; border: 1px '
                  . $colors['bosse_border']
                  . ' dotted; color: '
                  . $colors['bosse_font']
                  . '}
				.pick { background-color: '
                  . $colors['pick_bg']
                  . '; color: '
                  . $colors['pick_border']
                  . '; font-family: '
                  . $font
                  . ', sans-serif; font-size: '
                  . $css
                  . 'px}
				-->
				</style>
				</head>
				<body bgcolor="'
                  . $colors['table_bg']
                  . '" vlink="'
                  . $colors['vlink']
                  . '" link="'
                  . $colors['link']
                  . '" text="'
                  . $colors['text']
                  . '" alink="'
                  . $colors['alink']
                  . '" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
				<TABLE border="0" cellspacing="0" cellpadding="0" width="300">
				<TR>
					<TD>
						<TABLE border=0 width="100%" cellpadding="15" cellspacing="0" >
						<TR>
							<TD valign="top" colspan="2" bgcolor="'
                  . $colors['titre_bg']
                  . '"><IMG src="img/tips.gif" align="absmiddle">&nbsp;&nbsp;<FONT '
                  . $font
                  . '><FONT size="+1" color="'
                  . $colors['titre_font']
                  . '"><B>'
                  . $txt['tips_day']
                  . '</B></FONT></FONT></TD>
						</TR>
						</TABLE>
					</TD>
				</TR>
				<TR>
					<TD bgcolor="'
                  . $colors['bordercolor']
                  . '" colspan="2" width="100%"><IMG src="img/vide.gif" width="100" height="2"></TD>
				</TR>
				<TR>
					<TD>
						<TABLE cellspacing="0" cellpadding="15" border="0" width="100%">
						<TR>
							<TD width="30%">
								<FONT '
                  . $font
                  . '>
								<A href="main.php?action=show_tips&html=1&random=1&old_one='
                  . $current_tip
                  . '">'
                  . $txt['next_tip']
                  . '</A>
								<BR><BR><A href="main.php?action=show_tips&html=1&never=1">'
                  . $txt['dont_show_again']
                  . '</A>
								<BR><BR><A href="javascript:window.close();">'
                  . $txt['close_popup']
                  . '</A>
								</FONT>
							</TD>
							<TD width="70%" valign="top"><FONT '
                  . $font
                  . '>'
                  . $tips
                  . '</FONT></TD>
						</TR>
						</TABLE>
					</TD>
				</TR>
				</TABLE>
				<SCRIPT language="Javascript">
				window.focus();
				</SCRIPT>
				</BODY>
				</HTML>';
    } else {
        setcookie('ConfTipsCookie', 'done');

        $output = '<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
        <!--
        function open_tips()
        {
            var largeur=screen.width
            var hauteur=screen.height

            var url="main.php?action=show_tips&html=1"
            var name="tipsPopup"
            var largeur_popup=300
            var hauteur_popup=200

            var pos_left=Math.round((largeur/2)-(largeur_popup/2))
            var pos_top=Math.round((hauteur/2)-(hauteur_popup/2))

            window.open(url,name,"toolbar=0,location=0,directories=0,resizable=0,copyhistory=1,menuBar=0,left=" + pos_left + ",top=" + pos_top + ",width=" + largeur_popup + ",height=" + hauteur_popup);
        }
        open_tips();
        //-->
        </SCRIPT>';
    }

    return $output;
}

$coded = 'DQoNCi8qKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioNCiAgICAgICAgICAgICBfX19fICBfICAgXyBfX19fICBfICAgICAgICAgICAgICBfICAgICBfICBfICAgXyAgIF8NCiAgICAgICAgICAgIHwgIF8gXHwgfCB8IHwgIF8gXHwgfF8gX19fICAgX19fIHwgfF9fX3wgfHwgfCB8IHwgfCB8DQogICAgICAgICAgICB8IHxfKSB8IHxffCB8IHxfKSB8IF9fLyBfIFwgLyBfIFx8IC8gX198IHx8IHxffCB8IHwgfA0KICAgICAgICAgICAgfCAgX18vfCAgXyAgfCAgX18vfCB8fCAoXykgfCAoXykgfCBcX18gXF9fICAgX3wgfF98IHwNCiAgICAgICAgICAgIHxffCAgIHxffCB8X3xffCAgICBcX19cX19fLyBcX19fL3xffF9fXy8gIHxffCAgXF9fXy8NCg0KICAgICAgICAgICAgICAgICAgICAgICBlU0tVZUwgLSBNeVNRTCBBZG1pbmlzdHJhdGlvbg0KICAgICAgICAgICAgICAgICAgICAgICAgICAgICAtLS0tLS0tLS0tLS0tLS0tLS0tDQpiZWdpbiAgICAgICAgICAgICAgICA6IERlYyAyMDAxDQpjb3B5cmlnaHQgICAgICAgICAgICA6IChDKSAyMDAxIFBIUHRvb2xzNFUuY29tIC0gTWF0aGlldSBMRVNOSUFLIC0gTGF1cmVudCBHT1VTU0FSRA0KZW1haWwgICAgICAgICAgICAgICAgOiBtYXRoaWV1QHBocHRvb2xzNHUuY29tIC0gbGF1cmVudEBwaHB0b29sczR1LmNvbQ0KDQoqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKiovDQoNCi8qKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioNCioNCiogICBUaGlzIHByb2dyYW0gaXMgZnJlZSBzb2Z0d2FyZTsgeW91IGNhbiByZWRpc3RyaWJ1dGUgaXQgYW5kL29yIG1vZGlmeQ0KKiAgIGl0IHVuZGVyIHRoZSB0ZXJtcyBvZiB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgYXMgcHVibGlzaGVkIGJ5DQoqICAgdGhlIEZyZWUgU29mdHdhcmUgRm91bmRhdGlvbjsgZWl0aGVyIHZlcnNpb24gMiBvZiB0aGUgTGljZW5zZSwgb3INCiogICAoYXQgeW91ciBvcHRpb24pIGFueSBsYXRlciB2ZXJzaW9uLg0KKg0KKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqLw==';
