<?php

function show_dump_hp($sql_ref, $tbl)
{
    global $colors, $font, $txt, $_POST;

    $dump = $_POST['dump'] ?? '';

    $type = $_POST['type'] ?? '';

    $extra_query = isset($_POST['extra_query']) ? magic_quote_bypass($_POST['extra_query']) : '';

    $selected_structure = 'CHECKED';

    $output = '';

    $output_js = '';

    ### We're coming with an extra query, selecting structure + data by default

    if ('' != $extra_query) {
        $selected_data = 'CHECKED';

        $selected_structure = '';
    } else {
        $selected_data = '';
    }

    $tbl_infos = $sql_ref->getTblsInfos();

    for ($i = 0; $i < $tbl_infos['Number_Of_Tables']; $i++) {
        $tbl_list[$i]['name'] = $tbl_infos['Tables_List'][$i];

        $tbl_list[$i]['status'] = '0';

        if ($tbl_infos['Tables_List'][$i] == $tbl) {
            $current_tbl_index = $i;
        }
    }

    ### The form hasn't been posted, showing the form

    if ('' == $dump) {
        $tbl_check = $_POST['tbl_check'] ?? '';

        if (!is_array($tbl_check) && '' == $tbl) {
            $output = $txt['dump_must_choose_tbl'] . '.';

            $output .= return_2_db($sql_ref->Database);

            return $output;
        }

        ### Are we coming from the db hp or tbl page ?

        ### Are we doing a multiple tbl dump ?

        if (is_array($tbl_check)) {
            ### Multiple tbl dump

            for ($i = 0, $iMax = count($tbl_list); $i < $iMax; $i++) {
                if (isset($tbl_check[$i]) && 1 == $tbl_check[$i]) {
                    $tbl_list[$i]['status'] = 1;
                } else {
                    $tbl_list[$i]['status'] = 0;
                }
            }
        } else {
            $tbl_list[$current_tbl_index]['status'] = 1;
        }

        $mult_select = '<SELECT name="selected_tbl[]" multiple class="trous" size="10">';

        for ($i = 0, $iMax = count($tbl_list); $i < $iMax; $i++) {
            if (1 == $tbl_list[$i]['status']) {
                $selected = 'SELECTED';
            } else {
                $selected = '';
            }

            $mult_select .= '<OPTION value="' . $i . '" ' . $selected . '>' . $tbl_list[$i]['name'] . '</OPTION>';
        }

        $mult_select .= '</SELECT>';

        $output_js = '<SCRIPT language="Javascript">
						function check_txtarea() {
							dld = window.document.frmdump.download;
							if (window.document.frmdump.split.value > 0) {
								window.document.frmdump.sql_dump.disabled = true;
								dld.checked = true;
							}
							else {
								window.document.frmdump.sql_dump.disabled = false;
								dld.checked = false;
							}
						}
						</SCRIPT>';

        $output = '<form action="main.php?db=' . urlencode($sql_ref->Database) . '&tbl=' . urlencode($tbl) . '&action=dump" method=post name="frmdump">';

        $output .= '<TABLE border="1" bgcolor="' . $colors['table_bg'] . '" bordercolor="' . $colors['titre_bg'] . '" cellspacing="0" cellpadding="5" width="100%">';

        $output .= '<TR>';

        $output .= '	<TD valign="top">';

        $output .= '		<FONT ' . $font . '>';

        $output .= '		<INPUT type="radio" name="type" value="0" ' . $selected_structure . ' class="pick"> ' . $txt['dump_structure_only'];

        $output .= '		<BR>';

        $output .= '		<INPUT type="radio" name="type" value="1" class="pick" ' . $selected_data . '> ' . $txt['dump_structure_data'];

        $output .= '		<BR>';

        $output .= '		<INPUT type="radio" name="type" value="2" class="pick"> ' . $txt['dump_data_only'];

        $output .= '		<BR>';

        $output .= '		<INPUT TYPE="checkbox" name="drop_table" value="1" class="pick"> ' . $txt['dump_drop_table'] . ' ?';

        $output .= '		<BR>';

        $output .= '		<INPUT TYPE="checkbox" name="complete_insert" value="1" class="pick"> ' . $txt['dump_complete_insert'] . ' ?';

        $output .= '		<BR>';

        $output .= '		<BR>' . $txt['dump_split'] . ' : <INPUT type="text" name="split" size="4" class="trous" value="0" onBlur="check_txtarea();">';

        $output .= '		<BR>' . $txt['dump_split_expl'];

        $output .= '		</FONT>';

        $output .= '	</TD>';

        $output .= '	<TD valign="top">';

        $output .= '		<FONT ' . $font . '>';

        $output .= '		<INPUT type=radio name=type value=3 class="pick"> ' . $txt['dump_csv'];

        $output .= '		<BR>';

        $output .= '		<TABLE border="0" bgcolor="' . $colors['table_bg'] . '" bordercolor="' . $colors['titre_bg'] . '" cellspacing=0 cellpadding=5>';

        $output .= '		<TR>';

        $output .= '			<TD><FONT ' . $font . '>' . $txt['dump_csv_separator'] . ' : </FONT></TD>';

        $output .= '			<TD><INPUT type="text" name="separator" value=";" class="trous" size=3></TD>';

        $output .= '		</TR>';

        $output .= '		<TR>';

        $output .= '			<TD><FONT ' . $font . '>' . $txt['dump_csv_eol'] . ' : </FONT></TD>';

        $output .= '			<TD><INPUT type="text" name="end_line" value="\n" class="trous" size=3></TD>';

        $output .= '		</TR>';

        $output .= '		</TABLE>';

        $output .= '		</FONT>';

        $output .= '	</TD>';

        $output .= '</TR>';

        $output .= '<TR>';

        $output .= '	<TD valign="top">';

        $output .= '		<FONT ' . $font . '>';

        $output .= '		<B>' . $txt['dump_query'] . ' : </B><BR>';

        $output .= '		<TEXTAREA name="sql_dump" cols=50 rows=10 class="trous">' . $extra_query . '</TEXTAREA><BR>';

        $output .= '		</FONT>';

        $output .= '	</TD>';

        $output .= '	<TD valign="top">';

        $output .= '		<FONT ' . $font . '><B>' . $txt['dump_mult'] . ' :</B><BR>';

        $output .= '		' . $mult_select;

        $output .= '		</FONT>';

        $output .= '	</TD>';

        $output .= '</TR>';

        $output .= '<TR>';

        $output .= '	<TD colspan="2">';

        $output .= '		<FONT ' . $font . '><INPUT TYPE=checkbox name=download value=1 class="pick"> ' . $txt['dump_transmit'] . ' ?</FONT>';

        $output .= '		<BR><DIV align="right"><INPUT type=submit value="' . $txt['execute'] . '" class="bosses"></DIV>';

        $output .= '	</TD>';

        $output .= '</TR>';

        $output .= '</TABLE>';

        $output .= '<INPUT type="hidden" name="dump" value=1>';

        $output .= '</FORM>';
    }

    if (1 == $dump) {
        $output = do_dump($sql_ref, $tbl, $type, 0);
    }

    return $output_js . $output;
}

function do_dump($sql_ref, $tbl, $type, $query = '')
{
    global $_POST, $txt;

    $mult_tbl = $_POST['selected_tbl'] ?? [];

    if (1 == count($mult_tbl)) {
        $mult_dump = 0;
    } else {
        $mult_dump = 1;
    }

    $tbl_infos = $sql_ref->getTblsInfos();

    for ($i = 0; $i < $tbl_infos['Number_Of_Tables']; $i++) {
        $tbl_list[$i]['name'] = $tbl_infos['Tables_List'][$i];

        if (is_in_array($i, $mult_tbl)) {
            $tbl_2_dump[] = $tbl_infos['Tables_List'][$i];
        }
    }

    if (0 == count($tbl_2_dump)) {
        $output = $txt['dump_must_choose_tbl'] . '.';

        $output .= return_2_db($sql_ref->Database);

        return $output;
    }

    $drop_table = $_POST['drop_table'] ?? '';

    $type = $_POST['type'] ?? '';

    $complete_insert = $_POST['complete_insert'] ?? '';

    $download = $_POST['download'] ?? '';

    $separator = $_POST['separator'] ?? '';

    $end_line = isset($_POST['end_line']) ? stripslashes($_POST['end_line']) : '';

    $sql_dump_query = isset($_POST['sql_dump']) ? magic_quote_bypass($_POST['sql_dump']) : '';

    $split = $_POST['split'] ?? '';

    $options = 0;

    $output = '';

    $tbl2dump = '';

    if ('' != $split && $split > 0 && $type > 0 && 1 == $download) {
        if (1 == $complete_insert) {
            $options += 1;
        }

        if (1 == $drop_table) {
            $options += 2;
        }

        ### Building the table list to pass to the popup

        for ($i = 0, $iMax = count($mult_tbl); $i < $iMax; $i++) {
            $tbl2dump .= ($mult_tbl[$i]);

            if (($i + 1) < count($mult_tbl)) {
                $tbl2dump .= '|';
            }
        }

        $output = '<SCRIPT language="Javascript">
					function mainWindowLink() {
						PopupDump.close();
						window.location = "main.php?db=' . urlencode($sql_ref->Database) . '&tbl=' . urlencode($tbl) . '";
					}
						
					PopupDump = window.open(\'popup_dump.php?navbar=1&offset=0&split=' . $split . '&db=' . urlencode(addslashes($sql_ref->Database)) . '&tbl=' . urlencode(addslashes($tbl2dump)) . '&type=' . $type . '&options=' . $options . '\',\'PopupDump\',\'width=300,height=250,scrollbars=no,resizable=no,status=yes\');
				   </SCRIPT>
				   ' . $txt['dump_in_progress'] . '.
				   <BR><BR>
				   <A href="javascript:mainWindowLink();">' . $txt['close_popup'] . '</A>';

        return $output;
    }

    for ($i = 0, $iMax = count($tbl_2_dump); $i < $iMax; $i++) {
        $current_tbl = $tbl_2_dump[$i];

        ### Cancelling dump query when there's more than 1 table to dump

        if (count($tbl_2_dump) > 1) {
            $sql_dump_query = '';
        }

        if ((1 == $drop_table) && (2 != $type) && (3 != $type)) {
            $output .= 'DROP TABLE IF EXISTS ' . sql_back_ticks($current_tbl, $sql_ref) . ";\n";
        }

        if ((0 == $type) || (1 == $type)) {
            $output .= do_structural_dump($sql_ref, $current_tbl);
        }

        if (1 == $type) {
            $output .= "\n" . do_data_dump($sql_ref, $current_tbl, $complete_insert, 0, 0, $sql_dump_query, $download);
        }

        if (2 == $type) {
            $output .= do_data_dump($sql_ref, $current_tbl, $complete_insert, 0, 0, $sql_dump_query, $download);
        }

        if (3 == $type) {
            $output .= do_csv_dump($sql_ref, $current_tbl, $separator, $end_line, $sql_dump_query);
        }

        $output .= "\n";
    }

    if (1 == $download) {
        $ext = (3 == $type) ? 'csv' : 'sql';

        header('Content-Type: application/octetstream');

        if (count($tbl_2_dump) > 1) {
            $tbl = 'Multi-Dump-' . $sql_ref->Database;
        } else {
            $tbl = $current_tbl;
        }

        header('Content-Disposition: filename="' . $tbl . '.' . $ext . '"');

        header('Pragma: no-cache');

        header('Expires: 0');

        echo $output;

        die();
    }

    return '<font size=+0><PRE>' . $output . '</PRE></font>';
}

function do_structural_dump($sql_ref, $tbl, $tbl_list = '')
{
    ### No SHOW CREATE TABLE statement before 3.23.20

    if ($sql_ref->Infos['Version'] >= 32320) {
        $sql_ref->query('SHOW CREATE TABLE ' . sql_back_ticks($tbl, $sql_ref));

        [, $result] = $sql_ref->next_record();

        $result .= ";\n";
    } else {
        $result = show_create_table($tbl, $sql_ref);
    }

    return $result;
}

function do_data_dump($sql_ref, $tbl, $complete_insert, $offset = 0, $limit = 0, $sql_dump_query = '', $is_dump = 0)
{
    $fields_str = '';

    $output = '';

    $sql_ref->query('SHOW FIELDS FROM ' . sql_back_ticks($tbl, $sql_ref));

    while ($field_name = $sql_ref->next_record()) {
        $fields_str .= sql_back_ticks($field_name[0], $sql_ref) . ',';
    }

    $fields_str = mb_substr($fields_str, 0, -1);

    if ('' == $sql_dump_query) {
        if (0 == $offset && 0 == $limit) {
            $sql_ref->query('SELECT * FROM ' . sql_back_ticks($tbl, $sql_ref));
        } else {
            $sql_ref->query('SELECT * FROM ' . sql_back_ticks($tbl, $sql_ref) . " LIMIT $offset,$limit");
        }
    } else {
        $sql_ref->query($sql_dump_query);
    }

    while ($arr = $sql_ref->next_record(MYSQL_NUM)) {
        $fields_val = '';

        for ($i = 0, $iMax = count($arr); $i < $iMax; $i++) {
            if ($is_dump) {
                if (($i + 1) >= count($arr)) {
                    $fields_val .= '\'' . addslashes(($arr[$i])) . '\'';
                } else {
                    $fields_val .= '\'' . addslashes(($arr[$i])) . '\',';
                }
            } else {
                if (($i + 1) >= count($arr)) {
                    $fields_val .= '\'' . addslashes(htmlentities($arr[$i], ENT_QUOTES | ENT_HTML5)) . '\'';
                } else {
                    $fields_val .= '\'' . addslashes(htmlentities($arr[$i], ENT_QUOTES | ENT_HTML5)) . '\',';
                }
            }
        }

        if (1 == $complete_insert) {
            $output .= 'INSERT INTO ' . sql_back_ticks($tbl, $sql_ref) . ' (' . $fields_str . ') VALUES (' . $fields_val . ');' . "\n";
        } else {
            $output .= 'INSERT INTO ' . sql_back_ticks($tbl, $sql_ref) . ' VALUES (' . $fields_val . ');' . "\n";
        }
    }

    return $output;
}

function do_csv_dump($sql_ref, $tbl, $separator, $end_line, $extra_query = '')
{
    $output = '';

    if ('' != $extra_query) {
        $sql_ref->query($extra_query);
    } else {
        $sql_ref->query('SELECT * FROM ' . sql_back_ticks($tbl, $sql_ref));
    }

    $end_line = str_replace('\n', "\012", $end_line);

    $end_line = str_replace('\r', "\015", $end_line);

    $end_line = str_replace('\t', "\011", $end_line);

    while ($csv_arr = $sql_ref->next_record()) {
        for ($i = 0; $i < count($csv_arr) / 2; $i++) {
            if ($i + 1 >= (count($csv_arr) / 2)) {
                $output .= $csv_arr[$i] . $end_line;
            } else {
                $output .= $csv_arr[$i] . $separator;
            }
        }
    }

    return $output;
}

function incoming_sql($sql_ref)
{
    global $_POST;

    global $color, $font, $txt, $tbl;

    $output = '';

    $incoming = $_POST['incoming'] ?? '';

    @set_time_limit(10000);

    $can_upload = (int)@get_cfg_var('upload_max_filesize');

    if (1 == $incoming) {
        $filename = $_POST['incoming_sql'];

        if (file_exists('./incoming/' . $filename)) {
            $sql_content = file('./incoming/' . $filename);

            $sql_content = implode('', $sql_content);

            $output = do_sql_query($sql_ref, $sql_content, '', '', 1);

            $filename_ss_nb = mb_substr($filename, 0, mb_strrpos($filename, '-'));

            $num_pos = mb_strrpos($filename, '-') + 1;

            $dot_pos = mb_strrpos($filename, '.sql');

            $pos = mb_substr($filename, $num_pos, $dot_pos - $num_pos);

            $pos++;

            if (file_exists('./incoming/' . $filename_ss_nb . '-' . $pos . '.sql')) {
                $output .= '<FORM name="incoming_sql_frm" action="main.php?db=' . urlencode($sql_ref->Database) . '&action=incoming_sql" method="post">';

                $output .= '<TABLE>';

                $output .= '<TR>';

                $output .= '	<TD>';

                $output .= '		<INPUT type="hidden" name="incoming" value=1>';

                $output .= '		<INPUT type="hidden" name="incoming_sql" value="' . $filename_ss_nb . '-' . $pos . '.sql">';

                $output .= '		<INPUT type="submit" class="bosses" value="' . $txt['incoming_sql'] . ' ' . $filename_ss_nb . '-' . $pos . '.sql &gt;&gt;">';

                $output .= '	</TD>';

                $output .= '</TR>';

                $output .= '</TABLE>';

                $output .= '</FORM>';
            }
        }
    } else {
        ### Check : is there an incoming/ dir ?

        if (is_dir('./incoming')) {
            $no_incoming_dir = 0;

            $d = dir('incoming');

            $avl_sql = [];

            while ($entry = $d->read()) {
                if ('.' != $entry && '..' != $entry && eregi('.sql', $entry)) {
                    $avl_sql[] = $entry;
                }
            }

            $d->close();
        } else {
            $no_incoming_dir = 1;
        }

        if (count($avl_sql)) {
            asort($avl_sql);
        } else {
            if ($no_incoming_dir) {
                $output .= $txt['no_incoming_dir'];
            } else {
                $output .= $txt['no_incoming_sql'];
            }

            if ($can_upload) {
                if ('' != $tbl) {
                    $output .= '<BR><BR>' . $txt['upload_csv'] . ' <A href="main.php?db=' . urlencode($sql_ref->Database) . '&tbl=' . urlencode($tbl) . '&action=incoming_csv">' . $txt['on_this_page'] . '</A>';

                    $output .= '<BR><BR>' . $txt['upload_sql'] . ' <A href="main.php?db=' . urlencode($sql_ref->Database) . '&tbl=' . urlencode($tbl) . '&action=incoming_upload_sql">' . $txt['on_this_page'] . '</A>';
                } else {
                    $output .= '<BR><BR>' . $txt['upload_sql'] . ' <A href="main.php?db=' . urlencode($sql_ref->Database) . '&tbl=' . urlencode($tbl) . '&action=incoming_upload_sql">' . $txt['on_this_page'] . '</A>';
                }
            } else {
                $output .= '<BR><BR>' . $txt['upload_not_allowed'] . '.';
            }

            return $output;
        }

        $output = '<FORM name="incoming_sql_frm" action="main.php?db=' . urlencode($sql_ref->Database) . '&action=incoming_sql" method="post">';

        $output .= '<TABLE>';

        $output .= '<TR>';

        $output .= '	<TD>';

        $output .= '		<SELECT name="incoming_sql" class="trous">';

        for ($i = 0, $iMax = count($avl_sql); $i < $iMax; $i++) {
            $output .= '		<OPTION value="' . $avl_sql[$i] . '">' . $avl_sql[$i] . '</OPTION>';
        }

        $output .= '		</SELECT>';

        $output .= '	</TD>';

        $output .= '	<TD>';

        $output .= '		<INPUT type="hidden" name="incoming" value=1>';

        $output .= '		<INPUT type="submit" class="bosses" value="' . $txt['incoming_sql'] . '">';

        $output .= '	</TD>';

        $output .= '</TR>';

        $output .= '</TABLE>';

        $output .= '</FORM>';

        if ($can_upload) {
            if ('' != $tbl) {
                $output .= '<BR><BR>' . $txt['upload_csv'] . ' <A href="main.php?db=' . urlencode($sql_ref->Database) . '&tbl=' . urlencode($tbl) . '&action=incoming_csv">' . $txt['on_this_page'] . '</A>';

                $output .= '<BR><BR>' . $txt['upload_sql'] . ' <A href="main.php?db=' . urlencode($sql_ref->Database) . '&tbl=' . urlencode($tbl) . '&action=incoming_upload_sql">' . $txt['on_this_page'] . '</A>';
            } else {
                $output .= '<BR><BR>' . $txt['upload_sql'] . ' <A href="main.php?db=' . urlencode($sql_ref->Database) . '&tbl=' . urlencode($tbl) . '&action=incoming_upload_sql">' . $txt['on_this_page'] . '</A>';
            }
        }
    }

    return $output;
}

#####################################
# Show the form and insert an
# uploaded sql file
# $sql_ref = SQL link to the base
#####################################
function incoming_upload_sql($sql_ref)
{
    global $txt, $font, $colors, $_POST, $HTTP_POST_FILES, $tbl;

    $incoming = $_POST['incoming'] ?? '';

    $output = '';

    if (1 == $incoming) {
        $tmp_filename = $HTTP_POST_FILES['csv_file']['tmp_name'];

        $tmp_file = file($tmp_filename);

        $tmp_content_file = implode('', $tmp_file);

        $output = do_sql_query($sql_ref, $tmp_content_file, '', '', 1);
    } else {
        $output .= '<form enctype="multipart/form-data" method="post" action="main.php?db=' . urlencode($sql_ref->Database) . '&tbl=' . urlencode($tbl) . '&action=incoming_upload_sql">';

        $output .= '<table border="0" bgcolor="' . $colors['table_bg'] . '" bordercolor="' . $colors['titre_bg'] . '" cellspacing=0 cellpadding=5>';

        $output .= '<tr>';

        $output .= '    <td><font ' . $font . '><b>Fichier : </b></font></td>';

        $output .= '    <td><input type="file" name="csv_file" class="trous"></td>';

        $output .= '</tr>';

        $output .= '</table>';

        $output .= '<input type="submit" value="' . $txt['ok'] . '" class="bosses">';

        $output .= '<input type="hidden" name="incoming" value="1">';

        $output .= '</form>';
    }

    return $output;
}

#####################################
# Show the form and insert a CSV file
# $sql_ref = SQL link to the base
#####################################
function incoming_csv($sql_ref)
{
    global $txt, $font, $colors, $tbl, $_POST, $HTTP_POST_FILES;

    $incoming = $_POST['incoming'] ?? '';

    $output = '';

    if (1 == $incoming) {
        $csv_file_name = $HTTP_POST_FILES['csv_file']['tmp_name'] ?? '';

        $csv_replace = $_POST['csv_replace'] ?? '';

        $csv_terminated = $_POST['csv_terminated'] ?? '';

        $csv_outlined = $_POST['csv_outlined'] ?? '';

        $csv_special_char = $_POST['csv_special_char'] ?? '';

        $csv_opt = $_POST['csv_opt'] ?? '';

        $csv_line_end = $_POST['csv_line_end'] ?? '';

        $csv_columns = $_POST['csv_columns'] ?? '';

        if (eregi('win', PHP_OS)) {
            $sql_query = 'LOAD DATA LOCAL INFILE \'' . addslashes($csv_file_name) . '';
        } else {
            $sql_query = 'LOAD DATA LOCAL INFILE \'' . $csv_file_name;
        }

        $sql_query .= '\' ' . $csv_replace . ' INTO TABLE ' . sql_back_ticks($tbl, $sql_ref);

        if ('' != $csv_terminated
            || '' != $csv_outlined
            || '' != $csv_special_char) {
            $sql_query .= ' FIELDS';
        }

        if ('' != $csv_terminated) {
            $sql_query .= ' TERMINATED BY \'' . addslashes(magic_quote_bypass($csv_terminated)) . '\'';
        }

        if ('' != $csv_outlined) {
            $sql_query .= ' ' . $csv_opt . ' ENCLOSED BY \'' . $csv_outlined . '\'';
        }

        if ('' != $csv_special_char) {
            $sql_query .= ' ESCAPED BY \'' . addslashes(magic_quote_bypass($csv_special_char)) . '\'';
        }

        if ('' != $csv_line_end) {
            $sql_query .= ' LINES TERMINATED BY \'' . addslashes(magic_quote_bypass($csv_line_end)) . '\'';
        }

        if ('' != $csv_columns) {
            $sql_query .= ' (' . magic_quote_bypass($csv_columns) . ')';
        }

        $output = do_sql_query($sql_ref, $sql_query, 0, 0, 1);

        $output .= return_2_table($sql_ref->Database, $tbl);
    } else {
        $output .= '<form enctype="multipart/form-data" method="post" action="main.php?db=' . urlencode($sql_ref->Database) . '&tbl=' . urlencode($tbl) . '&action=incoming_csv">';

        $output .= '<table border="1" bgcolor="' . $colors['table_bg'] . '" bordercolor="' . $colors['titre_bg'] . '" cellspacing=0 cellpadding=5>';

        $output .= '<tr>';

        $output .= '    <td><font ' . $font . '><b>' . $txt['dump_file'] . ' : </b></font></td>';

        $output .= '    <td><input type="file" name="csv_file" class="trous"></td>';

        $output .= '</tr>';

        $output .= '<tr>';

        $output .= '    <td><font ' . $font . '><b>' . $txt['dump_replace'] . ' ?</b></font></td>';

        $output .= '    <td><input type="checkbox" name="csv_replace" value="replace" class="pick"></td>';

        $output .= '</tr>';

        $output .= '<tr>';

        $output .= '    <td><font ' . $font . '><b>' . $txt['dump_fields_terminated'] . ' :</b></font></td>';

        $output .= '    <td><input type="text" name="csv_terminated" value=";" class="trous"></td>';

        $output .= '</tr>';

        $output .= '<tr>';

        $output .= '    <td><font ' . $font . '><b>' . $txt['dump_fields_surrounded'] . ' :</b></font></td>';

        $output .= '    <td><font ' . $font . '><input type="text" name="csv_outlined" value="&quot;" class="trous">';

        $output .= '		(opt : <input type="checkbox" name="csv_opt" value="optionally" class="pick">)</font></td>';

        $output .= '</tr>';

        $output .= '<tr>';

        $output .= '    <td><font ' . $font . '><b>' . $txt['dump_special_char'] . ' :</b></font></td>';

        $output .= '    <td><input type="text" name="csv_special_char" value="\\" class="trous"></td>';

        $output .= '</tr>';

        $output .= '<tr>';

        $output .= '    <td><font ' . $font . '><b>' . $txt['dump_lines_terminated'] . ' :</b></font></td>';

        $output .= '    <td><input type="text" name="csv_line_end" value="\\n" class="trous"></td>';

        $output .= '</tr>';

        $output .= '<tr>';

        $output .= '    <td><font ' . $font . '><b>' . $txt['dump_columns_names'] . ' :</b></font></td>';

        $output .= '    <td><input type="text" name="csv_columns" class="trous"></td>';

        $output .= '</tr>';

        $output .= '</table>';

        $output .= '<input type="hidden" name="incoming" value="1">';

        $output .= '<input type="submit" value="' . $txt['ok'] . '" class="bosses">';

        $output .= '</form>';
    }

    return $output;
}
