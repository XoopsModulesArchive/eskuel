<?php

#####################################
# Show the Home Page of DB
# $sql_ref = SQL link to the base
# $db = Current DB
#####################################
function show_db_hp($sql_ref, $db)
{
    global $txt;

    $output = '<FONT size="-1"><B>' . $txt['db'] . '</B> ' . $db . '</FONT><BR><BR>';

    $output .= show_tbl_list($sql_ref, $db);

    return $output;
}

#####################################
# Show the list of tables in the DB
# $sql_ref = SQL link to the base
# $db = Current DB
#####################################
function show_tbl_list($sql_ref, $db)
{
    global $colors, $font, $txt;

    $img_path = $colors['img_path'];

    $total_records = 0;

    $total_space = 0;

    $tbl_infos = $sql_ref->getTblsInfos();

    if (0 == $tbl_infos['Number_Of_Tables']) {
        return $txt['no_tbl'];
    }

    $output = '<SCRIPT language=javascript>
                    <!--
                    function CA(){
                        for (var i = 0; i < document.tbls_op.elements.length; i++) {
						    var e = document.tbls_op.elements[i];
						    if (e.type==\'checkbox\') {
								    if (document.tbls_op.chkbox_slt.value == "true") {
    									e.checked = true;
								    }
								    else {
    									e.checked = false;
								    }
						    }
					    }
					    if (document.tbls_op.chkbox_slt.value == "true" ){
    						document.tbls_op.chkbox_slt.value = "false";
					    }
					    else {
    						document.tbls_op.chkbox_slt.value = "true";
					    }

				    }
				    function chg_act(val) {
				    	document.tbls_op.action.value = val;
				    	document.tbls_op.submit();	
				    }
				    //-->
				    </SCRIPT>';

    $output .= '<form name="tbls_op" action="main.php?db=' . urlencode($sql_ref->Database) . '" method="post">';

    $output .= '<table border="1" bgcolor="' . $colors['table_bg'] . '" bordercolor="' . $colors['titre_bg'] . '" cellspacing=0 cellpadding=5>';

    $output .= '<tr>';

    $output .= '	<td><font ' . $font . '><b>' . $txt['tbl'] . '</b></font></td>';

    $output .= '	<td><font ' . $font . '><b>' . $txt['records'] . '</b></font></td>';

    ### Size given by SHOW TABLE STATUS >= 3.23.03

    if ($sql_ref->Infos['Version'] >= 32303) {
        $output .= '	<td><font ' . $font . '><b>' . $txt['size'] . '</b></font></td>';
    }

    ### No comments < 3.23.00

    if ($sql_ref->Infos['Version'] >= 32300) {
        $output .= '	<td><font ' . $font . '><B>' . $txt['comments'] . '</b></font></td>';
    }

    $output .= '	<td><font ' . $font . '><b><A href="javascript:CA();">' . $txt['select'] . '</a></b></font></td>';

    $output .= '</tr>';

    for ($i = 0; $i < $tbl_infos['Number_Of_Tables']; $i++) {
        $tbl_name = $tbl_infos['Tables_List'][$i];

        $tbl_records = $tbl_infos[$tbl_name]['Rows'];

        $tbl_comment = ('' == $tbl_infos[$tbl_name]['Comment']) ? '&nbsp;' : $tbl_infos[$tbl_name]['Comment'];

        $tbl_space = ($tbl_infos[$tbl_name]['Data_Length']) + ($tbl_infos[$tbl_name]['Index_Length']);

        $total_records += $tbl_records;

        $total_space += $tbl_space;

        $output .= '<tr>';

        $output .= '	<td><font ' . $font . '><a href="main.php?db=' . urlencode($db) . '&tbl=' . urlencode($tbl_name) . '">' . $tbl_name . '</A></font></td>';

        $output .= '	<td><font ' . $font . '>' . $tbl_records . '</font></td>';

        ### Size given by SHOW TABLE STATUS >= 3.23.03

        if ($sql_ref->Infos['Version'] >= 32303) {
            $output .= '	<td><font ' . $font . '>' . octet_2_kilo($tbl_space) . ' Ko</font></td>';
        }

        if ($sql_ref->Infos['Version'] >= 32300) {
            $output .= '	<td><font ' . $font . '>' . $tbl_comment . '</font></td>';
        }

        $output .= '	<td><input type=checkbox name="tbl_check[' . $i . ']" value="1" class="pick"></td>';

        $output .= '</tr>';
    }

    $output .= '</table>';

    $output .= '<BR><B>' . ucfirst($txt['records2']) . ' :</B> ' . $total_records;

    ### Size given by SHOW TABLE STATUS >= 3.23.03

    if ($sql_ref->Infos['Version'] >= 32303) {
        $output .= '<BR><B>' . $txt['size'] . ' : </B>' . octet_2_kilo($total_space) . ' Ko';
    }

    $output .= '<BR><BR><B>' . $txt['action_mult'] . ' :</B><BR>';

    $output .= '<img src="' . $img_path . 'img/dump.gif" border="0">&nbsp;<A href="javascript:chg_act(\'dump\');">' . $txt['dump_mult'] . '</A>';

    if ($tbl_infos['Version'] >= 32334) {
        $output .= '<BR><img src="' . $img_path . 'img/optimize.gif" border="0">&nbsp;<A href="javascript:chg_act(\'optimize\');">' . $txt['optimize_tbls'] . '</A>';
    }

    $output .= '<BR><img src="' . $img_path . 'img/delete.gif" border="0" width="15">&nbsp;<A href="javascript:chg_act(\'drop\');">' . $txt['delete_tbls'] . '</A>';

    $output .= '<BR><img src="' . $img_path . 'img/empty.gif" border="0" width="15">&nbsp;<A href="javascript:chg_act(\'empty\');">' . $txt['emptyer_tbls'] . '</A>';

    $output .= '<BR><img src="' . $img_path . 'img/tbl_structure.gif" border="0" width="15">&nbsp;<A href="javascript:chg_act(\'desc\');">' . $txt['desc_tbls'] . '</A>';

    $output .= '<INPUT type="hidden" name="action" value="">';

    $output .= '<INPUT type="hidden" name="chkbox_slt" value="true">';

    $output .= '</FORM>';

    return $output;
}

#####################################
# Function that create a new table
# $sql_ref = SQL link to the base
#####################################
function create_new_table($sql_ref)
{
    global $colors, $font, $txt;

    global $db, $insert, $_POST;

    global $data_types, $data_attributes;

    $output = '';

    $input_error = 0;

    $input_error2 = 0;

    $create_new_table = $_POST['create_new_table'] ?? '';

    $tbl_name = isset($_POST['tbl_name']) ? magic_quote_bypass($_POST['tbl_name']) : '';

    $nb_fields = $_POST['nb_fields'] ?? '';

    $insert = $_POST['insert'] ?? '';

    $input_error = (eregi('"', $tbl_name) || !mb_strlen($tbl_name) || eregi('[\]', $tbl_name) || '' == $nb_fields || 0 == $nb_fields || eregi('<', $tbl_name) || eregi('>', $tbl_name));

    ### Checking if there's an input error in the 2nd page

    if ($create_new_table && 1 == $insert) {
        for ($i = 0; $i < $nb_fields; $i++) {
            $name = $_POST["field_name_$i"] ?? '';

            $input_error2 = $input_error2 || (eregi('"', $name) || eregi('[\]', $name) || eregi('<', $name) || eregi('>', $name));
        }
    }

    if (1 == $create_new_table && 1 == $insert && !$input_error && !$input_error2) {
        ### the table has ben desc'd building it

        $sql_query = 'CREATE TABLE ' . sql_back_ticks($tbl_name, $sql_ref) . ' (';

        $query_tmp = '';

        $arrPrimary = [];

        $arrUnique = [];

        $arrIndex = [];

        $arrFulltext = [];

        for ($i = 0; $i < $nb_fields; $i++) {
            $name = isset($_POST["field_name_$i"]) ? magic_quote_bypass($_POST["field_name_$i"]) : '';

            $type = isset($_POST["field_type_$i"]) ? $data_types[$_POST["field_type_$i"]] : '';

            $length = $_POST["field_length_$i"] ?? '';

            $attributes = isset($_POST["field_attribute_$i"]) ? $data_attributes[$_POST["field_attribute_$i"]] : '';

            $null_allowed = $_POST["field_null_$i"] ?? '';

            $default_value = $_POST["field_default_$i"] ?? '';

            $increment = $_POST["field_increment_$i"] ?? '';

            $primary = $_POST["field_primary_$i"] ?? '';

            $index = $_POST["field_index_$i"] ?? '';

            $unique = $_POST["field_unique_$i"] ?? '';

            $full_text = $_POST["field_fulltext_$i"] ?? '';

            $comments = isset($_POST['comments']) ? magic_quote_bypass($_POST['comments']) : '';

            $length = ('' != $length) ? '(' . $length . ')' : '';

            $null_allowed = ('no' == $null_allowed) ? 'NOT NULL' : '';

            $default_value = addslashes(magic_quote_bypass($default_value));

            $default_value = ('' != $default_value) ? 'DEFAULT \'' . $default_value . '\'' : '';

            $increment = ('yes' == $increment) ? 'AUTO_INCREMENT' : '';

            $sql_query .= sql_back_ticks($name, $sql_ref) . " $type $length $attributes $default_value $null_allowed $increment";

            if (($i + 1) < $nb_fields) {
                $sql_query .= ', ';
            }

            if ('' != $primary) {
                $arrPrimary[] = $name;
            }

            if ('' != $index) {
                $arrIndex[] = $name;
            }

            if ('' != $unique) {
                $arrUnique[] = $name;
            }

            if ('' != $full_text) {
                $arrFulltext[] = $name;
            }
        }

        ### Building the Primary Key SQL part

        $query_tmp = '';

        $nb_arrPrimary = count($arrPrimary);

        for ($i = 0; $i < $nb_arrPrimary; $i++) {
            $query_tmp .= sql_back_ticks($arrPrimary[$i], $sql_ref);

            if (($i + 1) < $nb_arrPrimary) {
                $query_tmp .= ',';
            }
        }

        if ('' != $query_tmp) {
            $sql_query .= ', PRIMARY KEY (' . $query_tmp . ')';
        }

        ### Building the Index key SQL part

        $query_tmp = '';

        $nb_arrIndex = count($arrIndex);

        for ($i = 0; $i < $nb_arrIndex; $i++) {
            $query_tmp .= sql_back_ticks($arrIndex[$i], $sql_ref);

            if (($i + 1) < $nb_arrIndex) {
                $query_tmp .= ',';
            }
        }

        if ('' != $query_tmp) {
            $sql_query .= ', INDEX (' . $query_tmp . ')';
        }

        ### Building the Unique key SQL part

        $query_tmp = '';

        $nb_arrUnique = count($arrUnique);

        for ($i = 0; $i < $nb_arrUnique; $i++) {
            $query_tmp .= sql_back_ticks($arrUnique[$i], $sql_ref);

            if (($i + 1) < $nb_arrUnique) {
                $query_tmp .= ',';
            }
        }

        if ('' != $query_tmp) {
            $sql_query .= ', UNIQUE (' . $query_tmp . ')';
        }

        ### Building the FullText key SQL part

        $query_tmp = '';

        $nb_arrFulltext = count($arrFulltext);

        for ($i = 0; $i < $nb_arrFulltext; $i++) {
            $query_tmp .= sql_back_ticks($arrFulltext[$i], $sql_ref);

            if (($i + 1) < $nb_arrFulltext) {
                $query_tmp .= ',';
            }
        }

        if ('' != $query_tmp) {
            $sql_query .= ', FULLTEXT (' . $query_tmp . ')';
        }

        $sql_query .= ')';

        if ('' != $comments) {
            $sql_query .= ' comment = \'' . addslashes($comments) . '\'';
        }

        $sql_ref->Database = $db;

        $sql_ref->query($sql_query);

        $output .= display_bookmarkable_query($sql_query);

        $output .= '<BR><BR>' . $txt['tbl_created'];

        $output .= return_2_db($db);
    } ### The first form has been filled, going on :o)

    elseif (1 == $create_new_table && !$input_error) {
        if ($input_error2) {
            $output .= '<FONT color="#FF0000"><B>' . $txt['input_invalid'] . '</B></FONT>';
        }

        $output .= '<BR><B>' . $txt['tbl_creation'] . ' :</B> ' . $tbl_name;

        $output .= '<BR><BR>';

        $output .= '<table border="1" bgcolor="' . $colors['table_bg'] . '" bordercolor="' . $colors['bordercolor'] . '" cellspacing=0 cellpadding=0>';

        $output .= '<tr>';

        $output .= '	<td>';

        $output .= '		<FORM action="main.php?db=' . urlencode($sql_ref->Database) . '&action=create_new_table" method="POST">';

        $output .= '		<table border="0" bgcolor="' . $colors['table_bg'] . '" bordercolor="' . $colors['titre_bg'] . '" cellspacing=0 cellpadding=5>';

        $current_mysql_version = get_mysql_version($sql_ref);

        for ($i = 0; $i < $nb_fields; $i++) {
            if ($current_mysql_version >= 32323) {
                $output_fulltext = '<BR><input type="checkbox" name="field_fulltext_' . $i . '" value=1 class="pick"><font ' . $font . '> ' . $txt['fulltext'] . '</font>';
            } else {
                $output_fulltext = '';
            }

            $output .= '<tr>
							<td><font ' . $font . '><b>' . $txt['key'] . ' : </b></font></td>
							<td><font ' . $font . '><b>' . $txt['field_name'] . ' : </b></font></td>
							<td><font ' . $font . '><b>' . $txt['type'] . ' : </b></font></td>
							<td><font ' . $font . '><b>' . $txt['can_be_null'] . ' : </b></font></td>
							<td><font ' . $font . '><b>' . $txt['length'] . ' : </b></font></td>
						</tr>';

            $output .= '<tr>
							<td nowrap rowspan="3" valign="top">
                                    <input type="checkbox" name="field_primary_' . $i . '" value=1 class="pick"><font ' . $font . '> ' . $txt['primary'] . '</font>
							        <BR><input type="checkbox" name="field_index_' . $i . '" value=1 class="pick"><font ' . $font . '> ' . $txt['index'] . '</font>
									<BR><input type="checkbox" name="field_unique_' . $i . '" value=1 class="pick"><font ' . $font . '> ' . $txt['unique'] . '</font>
									' . $output_fulltext . '
                            </td>
							<td><input type="text" name="field_name_' . $i . '" class="trous"></td>
							<td>' . select_type("field_type_$i") . '</td>
							<td><select name="field_null_' . $i . '" class="trous">
									<option value="yes">' . $txt['Yes'] . '</option>
									<option value="no">' . $txt['No'] . '</option>
								</select>
							</td>
							<td><input type="text" name="field_length_' . $i . '" size=5 class="trous"></td>
						</tr>
						<tr>
							<td><font ' . $font . '><b>' . $txt['attribs'] . ' : </b></font></td>
							<td><font ' . $font . '><b>' . $txt['default_value'] . ' : </b></font></td>
							<td colspan="2"><font ' . $font . '><b>' . $txt['auto_incr'] . ' : </b></font></td>
						</tr>
						<tr>
							<td>' . select_attribute("field_attribute_$i") . '</td>
							<td><input type="text" name="field_default_' . $i . '" class="trous"></td>
							<td colspan="2"><select name="field_increment_' . $i . '" class="trous">
									<option value="yes">' . $txt['Yes'] . '</option>
									<option value="no" selected>' . $txt['No'] . '</option>
								</select>
							</td>
						</tr>';

            ### Outputting a separator

            if ($i + 1 < $nb_fields) {
                $output .= '<tr>
								<td colspan="5">
								<table width="100%" border="0" cellspacing="0" cellpadding="0">
								  <tr>
								    <td bgcolor="' . $colors['titre_bg'] . '"><img src="img/vide.gif"></td>
								  </tr>
								</table>
								</td>
							</tr>';
            }
        }

        $output .= '		</table>';

        $output .= '	</td>';

        $output .= '</tr>';

        $output .= '</table>';

        $output .= '<input type="hidden" name="tbl_name" value="' . $tbl_name . '">';

        $output .= '<input type="hidden" name="nb_fields" value="' . $nb_fields . '">';

        $output .= '<input type="hidden" name="action" value="create_new_table">';

        $output .= '<input type="hidden" name="insert" value="1">';

        ### Mysql Comment available > 3.23.0

        if ($sql_ref->Infos['Version'] >= 32300) {
            $output .= '<BR><B>' . $txt['comments'] . ' :</B> <BR><textarea name="comments" rows=3 cols=75 class="trous"></textarea><BR><BR>';
        }

        $output .= '<INPUT type="hidden" name="create_new_table" value="1">';

        $output .= '<input type="submit" value="' . $txt['create_the_tbl'] . '" class="bosses">';

        $output .= '</form>';

        $img_path = $colors['img_path'];

        $output .= '<BR><img src="' . $img_path . 'img/tips_off.gif" width="20" height="20" align="center">&nbsp;<A href="javascript:help(\'data_types\');">' . $txt['help_data_types'] . '</A>';
    } ### First time, no form has been filled

    else {
        if ($input_error && $create_new_table) {
            $output .= '<FONT color="#FF0000"><B>' . $txt['input_invalid'] . '</B></FONT>';
        }

        $output .= '<TABLE border=0 bgcolor="' . $colors['table_bg'] . '">';

        $output .= '<FORM action="main.php?db=' . urlencode($sql_ref->Database) . '&action=create_new_table" method="POST">';

        $output .= '<TR>
						<TD><font ' . $font . '><B>' . $txt['tbl_name'] . ' :</B></font></TD>
						<TD><INPUT type="text" name="tbl_name" class="trous"></TD>
					</TR>
					<TR>
						<TD><font ' . $font . '><B>' . $txt['nb_fields'] . ' :</B></font></TD>
						<TD><INPUT type="text" name="nb_fields" class="trous"></TD>
					</TR>';

        $output .= '</TABLE><br>';

        $output .= '<INPUT type=submit class="bosses" value="' . $txt['create_the_tbl'] . '">';

        $output .= '<INPUT type="hidden" name="create_new_table" value="1">';

        $output .= '</FORM>';
    }

    return $output;
}

#####################################
# Function that split multpl queries
# into an array
# $sql = the queries
#####################################
function split_query($sql)
{
    ### Based on gandon script from PMA 2.2.6.

    $current_char = '';

    $output = [];

    $string_start = '';

    $in_string = false;

    $query_length = mb_strlen($sql);

    for ($pos = 0; $pos < $query_length; ++$pos) {
        $current_char = $sql[$pos];

        if ($in_string) {
            while (1) {
                $pos = mb_strpos($sql, $string_start, $pos);

                if (!$pos) {
                    $output[] = $sql;

                    return $output;
                } elseif ('`' == $string_start || '\\' != $sql[$pos - 1]) {
                    $string_start = '';

                    $in_string = false;

                    break;
                }

                $tmp = 2;

                $back = false;

                while ($pos - $tmp > 0 && '\\' == $sql[$pos - $tmp]) {
                    $back = !$back;

                    $tmp++;
                }

                if ($back) {
                    $string_start = '';

                    $in_string = false;

                    break;
                }

                $pos++;
            }
        } elseif (';' == $current_char) {
            $output[] = mb_substr($sql, 0, $pos);

            $sql = ltrim(mb_substr($sql, min($pos + 1, $query_length)));

            $query_length = mb_strlen($sql);

            if ($query_length) {
                $pos = -1;
            } else {
                return $output;
            }
        } elseif ('"' == $current_char || '\'' == $current_char || '`' == $current_char) {
            $in_string = true;

            $string_start = $current_char;
        } elseif ('#' == $current_char || ' ' == $current_char && $pos > 1 && $sql[$pos - 2] . $sql[$pos - 1] == '--') {
            $comment = (('#' == $sql[$pos]) ? $pos : $pos - 2);

            $comment_end = (mb_strpos(' ' . $sql, "\012", $pos + 2)) ?: mb_strpos(' ' . $sql, "\015", $pos + 2);

            if (!$comment_end) {
                if ($comment > 0) {
                    $output[] = trim(mb_substr($sql, 0, $comment));
                }

                return $output;
            }

            $sql = mb_substr($sql, 0, $comment) . ltrim(mb_substr($sql, $comment_end));

            $query_length = mb_strlen($sql);

            $pos--;
        }
    }

    if (!empty($sql)) {
        $output[] = $sql;
    }

    return $output;
}

function do_sql_query($sql_ref, $query, $multiple = 0, $incall = 0, $dont_stripslashes = 0)
{
    global $colors, $font, $txt;

    global $conf;

    global $_POST, $tbl;

    global $_GET;

    global $HTTP_COOKIE_VARS;

    $output = '';

    if (0 == $dont_stripslashes) {
        $query = (magic_quote_bypass(trim($query)));
    } else {
        $query = trim($query);
    }

    if (0 == $incall) {
        $splitted_query = split_query($query);
    } else {
        $splitted_query = $query;
    }

    $nb_queries = count($splitted_query);

    if ($nb_queries > 1 && 0 == mb_strlen($splitted_query[$nb_queries - 1])) {
        $nb_queries -= 1;
    }

    if ($nb_queries > 1) {
        $timing = start_timing();

        for ($i = 0; $i <= $nb_queries; $i++) {
            if (isset($splitted_query[$i])) {
                do_sql_query($sql_ref, $splitted_query[$i], 1, 1, 1);
            }
        }

        $output = '<B>' . $txt['multiple_queries'] . ' :</B><BR>' . $nb_queries . ' ' . $txt['done_queries'] . '.';

        $output .= '<BR><BR>' . print_timing($timing);

        $output .= return_2_db($sql_ref->Database);

        return $output;
    }

    ### Removing the trailing ';'

    if (';' == mb_substr($query, mb_strlen($query) - 1, 1)) {
        $query = mb_substr($query, 0, -1);
    }

    ### Setting the type of the query

    if (eregi('^SELECT', $query)) {
        $query_type = 'SELECT';
    } elseif (eregi('^(DELETE|DROP)', $query)) {
        $query_type = 'DELETE';
    } elseif (eregi('^(INSERT|LOAD DATA|REPLACE)', $query)) {
        $query_type = 'INSERT';
    } elseif (eregi('^UPDATE', $query)) {
        $query_type = 'UPDATE';
    } elseif (eregi('^(SHOW|DESCRIBE|EXPLAIN)', $query)) {
        $query_type = 'SHOW';
    } elseif (eregi('^(CHECK|ANALYZE|REPAIR|OPTIMIZE)', $query)) {
        $query_type = 'MAINTAIN';
    } else {
        $query_type = '';
    }

    switch ($query_type) {
        ### Query contains either a 'INSERT' or a 'LOAD DATA' or a 'REPLACE'
        case 'INSERT':
            $sql_ref->query($query);
            $output .= display_bookmarkable_query($query);
            break;
        ### Query contains an 'UPDATE'
        case 'UPDATE':
            if (!eregi('where', $query)) {
                $confirm_query = $_POST['confirm_query'] ?? '';

                if (1 == $confirm_query) {
                    $sql_ref->query($query);

                    $output .= display_bookmarkable_query($query);

                    $output .= return_2_db($sql_ref->Database);
                } else {
                    $frm_action = '?db=' . urlencode($sql_ref->Database) . '&action=do_query';

                    $output = $txt['sure_update'] . ' ?';

                    $output .= '<form action="main.php' . $frm_action . '" method="post">';

                    $output .= '<input type="submit" value="' . $txt['Yes'] . '" class="bosses">&nbsp;<input type=button value="' . $txt['No'] . '" onClick="javascript:history.go(-1);" class="bosses">';

                    $output .= '<input type="hidden" name="extra_query" value="' . htmlentities($query, ENT_QUOTES | ENT_HTML5) . '">';

                    $output .= '<input type="hidden" name="confirm_query" value="1">';

                    $output .= '</form>';
                }
            } else {
                $sql_ref->query($query);

                $output .= display_bookmarkable_query($query);
            }
            break;
        ### Query contains either à 'SHOW' or a 'MAINTAIN'
        case 'SHOW':
        case 'MAINTAIN':
            $output = '';
            $start_timing = start_timing();
            $sql_ref->query($query);

            $output .= display_bookmarkable_query($query);
            if (0 == $sql_ref->num_rows()) {
                return $output . '<BR><BR>' . $txt['no_records'];
            }
            $output .= '<BR><BR><table border="1" bgcolor="' . $colors['table_bg'] . '" bordercolor="' . $colors['titre_bg'] . '" cellspacing=0 cellpadding=5>';

            while (false !== ($arrFields = $sql_ref->fetch_field())) {
                $output .= '<td><font ' . $font . '><b>' . $arrFields->name . '</b></font></td>';
            }
            while ($arrView = $sql_ref->next_record(MYSQL_NUM)) {
                $output .= '<tr onMouseOver="this.bgColor=\'' . $colors['bgcolor'] . '\';" onMouseOut="this.bgColor=\'' . $colors['table_bg'] . '\';">';

                for ($i = 0, $iMax = count($arrView); $i < $iMax; $i++) {
                    if ('' == $arrView[$i]) {
                        $output .= '<td><IMG src="img/vide.gif"></td>';
                    } else {
                        $output .= '<td><font ' . $font . '>' . htmlentities($arrView[$i], ENT_QUOTES | ENT_HTML5) . '</font></td>';
                    }
                }

                $output .= '</tr>';
            }
            $output .= '</TABLE><BR>';
            $output .= print_timing($start_timing);
            if ($tbl) {
                $output .= return_2_table($sql_ref->Database, $tbl);
            }
            break;
        ### Query contains a 'SELECT'
        case 'SELECT':
            $start_timing = start_timing();
            $order_field = $_POST['order_field'] ?? '';
            $last_order = $_POST['last_order'] ?? '';
            $last_field = $_POST['last_field'] ?? '';
            $navAction = $_POST['navAction'] ?? '';
            $nb2show = $_POST['nb2show'] ?? '';
            $limit = $_POST['limit'] ?? '';
            $navSqlOrderBy = $_POST['navSqlOrderBy'] ?? '';
            $dont_reduce_blob = $_POST['dont_reduce_blob'] ?? 0;
            $nb2show = ('' == $nb2show) ? $conf['defaultPerRequest'] : $nb2show;
            $limit = ('' == $limit) ? 0 : $limit;
            $dont_show_forms = 0; // don't show the nav bar form
            $dont_show_field_link = 0; // don't show links on fields name
            $keys_counter = 0;
            $curr_tbl_old = '';
            $dont_show_js_links = 0;
            $blob_array = [];
            $keys_index = [];
            $query_orig = $query;
            $is_blob = 0;

            if ('' == $order_field) {
                $sql_order_by = '';
            } elseif ($last_field == $order_field) {
                $last_order = ('DESC' == $last_order) ? 'ASC' : 'DESC';

                $sql_order_by = 'ORDER BY ' . $order_field . ' ' . $last_order;
            } else {
                $sql_order_by = 'ORDER BY ' . $order_field . ' ASC';

                $last_order = 'ASC';
            }

            $sql_order_by = ('' == $sql_order_by) ? $navSqlOrderBy : $sql_order_by;

            ### Doing the original query and counting the nb of rows
            $sql_ref->query($query_orig);
            $nbEnreg = $sql_ref->num_rows();

            if ($nbEnreg < $nb2show) {
                $nb2show = $nbEnreg;
            }

            switch ($navAction) {
                case 'begin':
                    $limit = 0;
                    $limitfrm = $limit + $nb2show;
                    break;
                case 'prev':
                    $limit_prev = $_POST['limit_prev'] ?? '';
                    $limit = $limit_prev - $nb2show;
                    if ($limit <= 0) {
                        $limit = 0;
                    }
                    $limitfrm = $limit + $nb2show;
                    break;
                case 'next':

                    if ($limit >= $nbEnreg) {
                        $limit -= $nb2show;

                        $nb2show = $nbEnreg - $limit;
                    }

                    if (($limit + $nb2show) >= $nbEnreg) {
                        $nb2show = $nbEnreg - $limit;

                        $limitfrm = $limit;
                    } else {
                        $limitfrm = $limit + $nb2show;
                    }
                    break;
                case 'end':
                    $limit = $nbEnreg - $nb2show;
                    $limitfrm = $limit;
                    break;
                default:
                    $limitfrm = $limit + $nb2show;
                    break;
            }

            if (eregi(' limit ', $query)) {
                $dont_show_forms = 1;

                $sql = $query;

                if (eregi(' order by ', $extra_query)) {
                    $dont_show_field_link = 1;
                }
            } else {
                if (eregi(' order by ', $query)) {
                    $dont_show_field_link = 1;
                } else {
                    $query .= ' ' . $sql_order_by;
                }

                $sql = $query . ' LIMIT ' . $limit . ',' . $nb2show;
            }

            $output_js = '<SCRIPT language="Javascript">';
            $output_js .= 'function field_chg(field) {
	                        document.tbl_view.order_field.value = field;
	                        document.tbl_view.submit();
	                       }
	                       function nav_chg(value,frm) {
	                       	document.forms[frm].navAction.value = value;
	                       	document.forms[frm].submit();
	                       }
                           function dump(frm, tbl) {
                            document.forms[frm].action = "main.php?db=' . $sql_ref->Database . '&tbl="+tbl+"&action=dump";
                            document.forms[frm].submit();
                           }
						   function full_text() {
                       		document.frmBlob.dont_reduce_blob.value = ' . ((0 == $dont_reduce_blob) ? 1 : 0) . ';
                       		document.frmBlob.submit();
                       	   }
                           ';

            $output_js .= '</SCRIPT>';

            $output_top = display_bookmarkable_query($sql);
            $navFormTop = '<FORM name="navFrm" action="main.php?db=' . urlencode($sql_ref->Database) . '&action=do_query" method="post">
						   <TABLE border="0" cellspacing="0"cellpadding="5">
						   <TR>
							 <TD><INPUT type="button" name="start" value="<<" onclick="nav_chg(\'begin\',\'navFrm\')" class="bosses"></TD>
							 <TD><INPUT type="button" name="prev" value="<" onclick="nav_chg(\'prev\',\'navFrm\')" class="bosses"></TD>
							 <TD><FONT ' . $font . '>' . $txt['from'] . ' : </FONT></TD>
							 <TD><INPUT type="text" name="limit" value="' . $limitfrm . '" class="trous"></TD>
							 <TD><FONT ' . $font . '>' . $txt['display'] . ' : </FONT></TD>
							 <TD><INPUT type="text" name="nb2show" value="' . $nb2show . '" class="trous"></TD>
							 <TD><INPUT type="submit" name="next" value=">" onclick="nav_chg(\'next\',\'navFrm\')" class="bosses"></TD>
							 <TD><INPUT type="button" name="end" value=">>" onclick="nav_chg(\'end\',\'navFrm\')" class="bosses"></TD>
						   </TR>
						   </TABLE>
						   <INPUT type="hidden" name="dont_reduce_blob" value="' . $dont_reduce_blob . '">
						   <INPUT type="hidden" name="limit_prev" value="' . $limit . '">
						   <INPUT type="hidden" name="navAction" value="">
						   <INPUT type="hidden" name="navSqlOrderBy" value="' . $sql_order_by . '">
						   <INPUT type="hidden" name="extra_query" value="' . htmlentities($query_orig, ENT_QUOTES | ENT_HTML5) . '">
						   </FORM>';

            $navFormBottom = '<FORM name="navFrmBottom" action="main.php?db=' . urlencode($sql_ref->Database) . '&action=do_query" method="post">
							  <TABLE border="0" cellspacing="0"cellpadding="5">
					 		  <TR>
								<TD><INPUT type="button" name="start" value="<<" onclick="nav_chg(\'begin\',\'navFrmBottom\')" class="bosses"></TD>
								<TD><INPUT type="button" name="prev" value="<" onclick="nav_chg(\'prev\',\'navFrmBottom\')" class="bosses"></TD>
								<TD><FONT ' . $font . '>' . $txt['from'] . ' : </FONT></TD>
								<TD><INPUT type="text" name="limit" value="' . $limitfrm . '" class="trous"></TD>
								<TD><FONT ' . $font . '>' . $txt['display'] . ' : </FONT></TD>
								<TD><INPUT type="text" name="nb2show" value="' . $nb2show . '" class="trous"></TD>
								<TD><INPUT type="submit" name="next" value=">" onclick="nav_chg(\'next\',\'navFrmBottom\')" class="bosses"></TD>
								<TD><INPUT type="button" name="end" value=">>" onclick="nav_chg(\'end\',\'navFrmBottom\')" class="bosses"></TD>
							  </TR>
							  </TABLE>
							  <INPUT type="hidden" name="dont_reduce_blob" value="' . $dont_reduce_blob . '">
							  <INPUT type="hidden" name="limit_prev" value="' . $limit . '">
					 		  <INPUT type="hidden" name="navAction" value="">
							  <INPUT type="hidden" name="navSqlOrderBy" value="' . $sql_order_by . '">
							  <INPUT type="hidden" name="extra_query" value="' . htmlentities($query_orig, ENT_QUOTES | ENT_HTML5) . '">
							  </FORM>';

            ### Doing the query
            $sql_ref->query($sql);

            ### 0 matching rows
            if (0 == $sql_ref->num_rows()) {
                $output .= display_bookmarkable_query($query);

                return $output . '<BR><BR>' . $txt['no_records'];
            }
            $output .= $txt['displaying'] . ' ' . $nb2show . ' / ' . $nbEnreg . ' ' . mb_strtolower($txt['records']);
            if (!$dont_show_forms) {
                $output .= $navFormTop;
            }

            $output .= '<FORM action="main.php?db=' . urlencode($sql_ref->Database) . '&action=do_query" method="post" name="tbl_view">';
            $output .= '<TABLE border="1" bgcolor="' . $colors['table_bg'] . '" bordercolor="' . $colors['titre_bg'] . '" cellspacing="0" cellpadding="5">';
            $output .= '<TR>';

            ### Examining every fields on the current result
            while (false !== ($arrFields = $sql_ref->fetch_field())) {
                ### Testing if there's a blob / text on this result set

                if (eregi('blob', $arrFields->type) || eregi('text', $arrFields->type) || (eregi('string', $arrFields->type) && $arrFields->max_length > $conf['reduceBlob'])) {
                    $is_blob = 1;

                    $blob_array[$keys_counter] = 1;
                }

                if ((1 == $arrFields->primary_key)
                    || (1 == $arrFields->multiple_key)
                    || (1 == $arrFields->unique_key)) {
                    ### The current fields is a key, adding it to $keys_index array

                    $keys_index[$keys_counter] = $arrFields->name;
                }

                ### Storing each fields as key in case $keys_index is empty

                $keys_index_fields[$keys_counter] = $arrFields->name;

                ### Testing if we're are on the same table since the beginning of the while

                $curr_tbl = $sql_ref->field_table($keys_counter);

                if ((($curr_tbl == $curr_tbl_old) || ('' == $curr_tbl_old))
                    && (1 != $dont_show_js_links)) {
                    $dont_show_js_links = 0;

                    $tbl = $curr_tbl;

                    $curr_tbl_old = $curr_tbl;
                } else {
                    $dont_show_js_links = 1;

                    $tbl = '';
                }

                if (1 == $dont_show_field_link) {
                    $output .= '<td><font ' . $font . '><b>' . $arrFields->name . '</b></font></td>';
                } else {
                    $output .= '<td><font ' . $font . '><b><a href="javascript:field_chg(\'' . addslashes($arrFields->name) . '\');">' . $arrFields->name . '</A></b></font></td>';
                }

                $keys_counter++;
            }

            ### No keys in the result, using the fields instead
            if (!count($keys_index)) {
                $keys_index = $keys_index_fields;
            }

            ### Some conditions don't allow us to show the JS links, hiding them
            if (1 != $dont_show_js_links) {
                $output .= '<td>&nbsp;</td>';
            }

            $output .= '</tr>';

            while ($arrView = $sql_ref->next_record(MYSQL_NUM)) {
                $output .= '<tr onMouseOver="this.bgColor=\'' . $colors['bgcolor'] . '\';" onMouseOut="this.bgColor=\'' . $colors['table_bg'] . '\';">';

                $js_link = '';

                for ($i = 0, $iMax = count($arrView); $i < $iMax; $i++) {
                    if (1 != $dont_show_js_links) {
                        if (isset($keys_index[$i])) {
                            if (mb_strlen($js_link)) {
                                $js_link .= ' AND ';
                            }

                            if (!isset($arrView[$i])) {
                                $js_link .= sql_back_ticks($keys_index[$i], $sql_ref) . ' IS NULL';
                            } else {
                                $js_link .= sql_back_ticks($keys_index[$i], $sql_ref) . '=\'' . (addslashes($arrView[$i])) . '\'';
                            }
                        }
                    }

                    if ('' == $arrView[$i] && (isset($arrView[$i]))) {
                        $output .= '<td><IMG src="img/vide.gif"></td>';
                    } else {
                        if (isset($blob_array[$i]) && $conf['reduceBlob'] && !$dont_reduce_blob && (mb_strlen($arrView[$i]) > $conf['reduceBlob'])) {
                            $output .= '<td><font ' . $font . '>' . nl2br(htmlentities(mb_substr($arrView[$i], 0, $conf['reduceBlob']), ENT_QUOTES | ENT_HTML5)) . '...</font></td>';
                        } else {
                            ### Null or not null ? that is the question

                            if (!isset($arrView[$i])) {
                                $output .= '<td><font ' . $font . '><I>NULL</I></font></td>';
                            } else {
                                $output .= '<td><font ' . $font . '>' . nl2br(htmlentities($arrView[$i], ENT_QUOTES | ENT_HTML5)) . '</font></td>';
                            }
                        }
                    }
                }

                $_POST['action'] = 'do_query';

                $modif_link = 'main.php?db=' . urlencode($sql_ref->Database) . '&tbl=' . urlencode($tbl) . '&action=mod_record&query=' . urlencode($js_link);

                $suppr_link = 'main.php?db=' . urlencode($sql_ref->Database) . '&tbl=' . urlencode($tbl) . '&action=sup_record&query=' . urlencode($js_link);

                if (1 != $dont_show_js_links) {
                    $output .= '<td><font ' . $font . '><a href="' . $modif_link . '">' . $txt['modify'] . '</a><br><a href="' . $suppr_link . '">' . $txt['delete'] . '</a></font></td></tr>';
                }
            }

            $output .= '</TABLE>';
            $output .= '<INPUT type="hidden" name="order_field" value="">';
            $output .= '<INPUT type="hidden" name="last_field" value="' . $order_field . '">';
            $output .= '<INPUT type="hidden" name="last_order" value="' . $last_order . '">';
            $output .= '<INPUT type="hidden" name="extra_query" value="' . htmlentities($query_orig, ENT_QUOTES | ENT_HTML5) . '">';
            $output .= '<INPUT type="hidden" name="limit" value="' . $limit . '">';
            $output .= '<INPUT type="hidden" name="nb2show" value="' . $nb2show . '">';
            $output .= '</FORM>';

            $_POST['extra_query'] = $query_orig;
            setcookie('serial', (serialize($_POST)));

            ### We're in a single table, no joint
            if ('' != $tbl) {
                $output_extra = '<BR><img src="' . $colors['img_path'] . 'img/dump.gif" border="0"> <A href="javascript:dump(\'extra_actions_bottom\',\'' . addslashes($tbl) . '\');">' . $txt['dump_this_query'] . '</A>';

                $output_extra_frm = '<FORM action="" method="post" name="extra_actions_bottom">';

                $output_extra_frm .= '<INPUT type="hidden" name="extra_query" value="' . htmlentities($sql, ENT_QUOTES | ENT_HTML5) . '">';

                $output_extra_frm .= '</FORM>';
            }
            ### Have we encountered an type TEXT or BLOB ?
            if ($is_blob && $conf['reduceBlob']) {
                if ('' != $output_extra) {
                    $txt_2_show = (1 == $dont_reduce_blob) ? $txt['hide_full_text'] : $txt['show_full_text'];

                    $output_extra .= '<BR><img src="' . $colors['img_path'] . 'img/show_hide_txt.gif" border="0">&nbsp;<A href="javascript:full_text();">' . $txt_2_show . '</A><BR><BR>';
                } else {
                    $output_extra .= '<BR><img src="' . $colors['img_path'] . 'img/show_hide_txt.gif" border="0">&nbsp;<A href="javascript:full_text();">' . $txt['show_full_text'] . '</A><BR><BR>';
                }

                $output_extra_frm .= '<FORM name="frmBlob" action="main.php?db=' . urlencode($sql_ref->Database) . '&action=do_query" method="post">';

                $is_there_an_extra_query = 0;

                while (list($key, $val) = each($_POST)) {
                    if ('' != $val && !is_array($val)) {
                        if ('extra_query' == $key) {
                            $is_there_an_extra_query = 1;
                        }

                        if ('extra_query' == $key) {
                            $output_extra_frm .= '<INPUT type="hidden" name="' . $key . '" value="' . htmlentities($val, ENT_QUOTES | ENT_HTML5) . '">' . "\n";
                        } elseif ('dont_reduce_blob' != $key) {
                            $output_extra_frm .= '<INPUT type="hidden" name="' . $key . '" value="' . $val . '">' . "\n";
                        }
                    }
                }

                if (!$is_there_an_extra_query) {
                    $output_extra_frm .= '<INPUT type="hidden" name="extra_query" value="' . htmlentities($query, ENT_QUOTES | ENT_HTML5) . '">';
                }

                $output_extra_frm .= '<INPUT type="hidden" name="dont_reduce_blob" value="0">';

                $output_extra_frm .= '</FORM>';
            } else {
                if ('' != $output_extra) {
                    $output_extra .= '<BR><BR>';
                }
            }
            if (!$dont_show_forms) {
                $output .= $navFormBottom;
            }

            $output = $output_js . $output_top . $output_extra . $output . $output_extra . $output_extra_frm;
            $output .= print_timing($start_timing);

            break;
        case 'DELETE':
            ### Getting the serialized var from do_query's SELECT
            $serial_cookie = $HTTP_COOKIE_VARS['serial'] ?? '';
            $confirm_query = $_POST['confirm_query'] ?? '';
            $rec_count = 0;
            $db_count = 0;

            if ('' != $serial_cookie) {
                $serial = unserialize(magic_quote_bypass($serial_cookie));
            } else {
                $serial = [];
            }

            if (1 == $multiple) {
                $confirm_query = 1;
            }
            if (1 == $confirm_query) {
                $sql_ref->query($query);

                $output = '<B>' . $txt['query_done'] . ' : </B><BR>' . $query;

                $serial_action = $serial['action'] ?? '';

                if ('do_query' == $serial_action) {
                    $tbl = '';
                }

                $tbl = $serial['tbl_suppr'] ?? '';

                setcookie('serial', '');

                $hidden_counter = 0;

                $output_return = '';

                $output_return .= '<FORM name="back2future" action="main.php?db=' . urlencode($sql_ref->Database) . '&tbl=' . urlencode($tbl) . '&action=' . $serial_action . '" method="post">';

                if (is_array($serial)) {
                    while (list($key, $val) = each($serial)) {
                        if ('action' != $key && 'tbl_suppr' != $key && !is_array($val)) {
                            $hidden_counter++;

                            $output_return .= '<INPUT type="hidden" name="' . $key . '" value="' . htmlentities($val, ENT_QUOTES | ENT_HTML5) . '">';
                        }
                    }
                }

                $output_return .= '</FORM>';

                $output_return .= '<A href="javascript:document.back2future.submit()">' . $txt['back'] . '</A>';

                $output .= ($hidden_counter > 0) ? $output_return : '<BR><BR><A href="main.php?db=' . urlencode($sql_ref->Database) . '&tbl=' . urlencode($tbl) . '&action=' . $serial_action . '">' . $txt['back'] . '</A>';
            } else {
                $db_count = 0;

                if (eregi('DROP( )*TABLE( )*IF( )*EXISTS', $query)) {
                    $sql_ref->query($query);

                    $output .= display_bookmarkable_query($query);

                    break;
                }

                if (eregi('DROP( )*TABLE', $query)) {
                    $sql_length = mb_strpos(mb_strtoupper($query), 'TABLE');

                    $tbl_name = trim(mb_substr($query, $sql_length + 5, mb_strlen($query)));

                    if ('`' == mb_substr($tbl_name, 0, 1) && '`' == mb_substr($tbl_name, mb_strlen($tbl_name) - 1, 1)) {
                        $tbl_name = mb_substr($tbl_name, 1, -1);
                    }

                    $tmp_query = 'SELECT COUNT(*) FROM ' . sql_back_ticks($tbl_name, $sql_ref);

                    $sql_ref->query($tmp_query);

                    [$nbEnreg] = $sql_ref->next_record();

                    $rec_count = 1;
                }

                if (eregi('DROP( )*DATABASE', $query)) {
                    $frm_action = '?action=do_query';

                    $sql_length = mb_strpos(mb_strtoupper($query), 'DATABASE');

                    $db_name = trim(mb_substr($query, $sql_length + 8, mb_strlen($query)));

                    if ('`' == mb_substr($db_name, 0, 1) && '`' == mb_substr($db_name, mb_strlen($db_name) - 1, 1)) {
                        $db_name = mb_substr($db_name, 1, -1);
                    }

                    $nbEnreg = 0;

                    $nbRows = 0;

                    if ($sql_ref->Infos['Version'] >= 32303) {
                        $sql_ref->query('SHOW TABLE STATUS FROM ' . sql_back_ticks($db_name, $sql_ref));

                        while ($sql_result = $sql_ref->next_record()) {
                            $nbRows += $sql_result['Rows'];

                            $nbEnreg++;
                        }
                    } else {
                        $res = mysql_list_tables($db_name);

                        while (false !== ($row = $GLOBALS['xoopsDB']->fetchRow($res))) {
                            $sql_ref->query('SELECT COUNT(*) FROM ' . $row[0]);

                            [$tmp] = $sql_ref->next_record();

                            $nbRows += $tmp;

                            $nbEnreg++;
                        }
                    }

                    $db_count = 1;

                    $rec_count = 1;
                } else {
                    $frm_action = '?db=' . urlencode($sql_ref->Database) . '&action=do_query';
                }

                if (eregi('DELETE([ *])*FROM', $query)) {
                    if (eregi('WHERE', $query)) {
                        $where = mb_stristr($query, 'WHERE');

                        $sql_length = mb_strpos(mb_strtoupper($query), 'FROM');

                        $where_pos = mb_strlen($query) - mb_strlen($where);

                        $tbl_name = trim(mb_substr($query, $sql_length + 4, $where_pos - 11));

                        if ('`' == mb_substr($tbl_name, 0, 1) && '`' == mb_substr($tbl_name, mb_strlen($tbl_name) - 1, 1)) {
                            $tbl_name = mb_substr($tbl_name, 1, -1);
                        }

                        $sql_ref->query('SELECT COUNT(*) FROM ' . sql_back_ticks($tbl_name, $sql_ref) . " $where");

                        [$nbEnreg] = $sql_ref->next_record();

                        $rec_count = 1;
                    } else {
                        $sql_length = mb_strpos(mb_strtoupper($query), 'FROM');

                        $tbl_name = trim(mb_substr($query, $sql_length + 4, mb_strlen($query) - 11));

                        if ('`' == mb_substr($tbl_name, 0, 1) && '`' == mb_substr($tbl_name, mb_strlen($tbl_name) - 1, 1)) {
                            $tbl_name = mb_substr($tbl_name, 1, -1);
                        }

                        $sql_ref->query('SELECT COUNT(*) FROM ' . sql_back_ticks($tbl_name, $sql_ref));

                        [$nbEnreg] = $sql_ref->next_record();

                        $rec_count = 1;
                    }
                }

                $output = $txt['confirm_query'] . ' :<BR><B>' . $query . '</B> ?';

                if (1 == $rec_count) {
                    $output .= '<BR><Font color="#FF0000">' . $txt['query_is_about'] . ' ' . $nbEnreg;

                    if (1 == $db_count) {
                        $output .= ' ' . $txt['tbl2'] . ' ' . $txt['and'] . ' ' . $nbRows . ' ' . mb_strtolower($txt['records2']) . '.</font>';
                    } else {
                        $output .= ' ' . mb_strtolower($txt['records2']) . '.</font>';
                    }
                }

                $output .= '<FORM action="main.php' . $frm_action . '" method="post">';

                $output .= '<INPUT type=submit value="' . $txt['Yes'] . '" class="bosses">&nbsp;<input type=button value="' . $txt['No'] . '" onClick="javascript:history.go(-1);" class="bosses">';

                $output .= '<INPUT type=hidden name="extra_query" value="' . htmlentities($query, ENT_QUOTES | ENT_HTML5) . '">';

                $output .= '<INPUT type="hidden" name="serial" value="' . $serial . '">';

                $output .= '<INPUT type=hidden name="confirm_query" value="1">';

                $output .= '</FORM>';
            }
            break;
        default:
            ### Default case, doing the query
            $sql_ref->query($query);
            $output .= display_bookmarkable_query($query);
            break;
    }

    return $output;
}

#####################################
# Function that create a new DB
# $sql_ref = SQL link to the base
#####################################
function create_db($sql_ref)
{
    global $_POST, $colors, $font, $txt;

    $output = '';

    $create_db = $_POST['create_db'] ?? '';

    $db_2_create = isset($_POST['db_2_create']) ? magic_quote_bypass($_POST['db_2_create']) : '';

    ### Check if the entry is correct

    $input_error = (eregi('"', $db_2_create) || !mb_strlen($db_2_create) || eregi('[\]', $db_2_create) || eregi('<', $db_2_create) || eregi('>', $db_2_create));

    if (1 == $create_db && !$input_error) {
        $sql_ref->query('CREATE DATABASE ' . sql_back_ticks($db_2_create, $sql_ref));

        $output = $txt['the_db'] . ' ' . $db_2_create . ' ' . $txt['has_been_created'] . '.';

        $output .= return_2_db($db_2_create);
    } else {
        ### Incorrect entry, display an error message

        if ($input_error && 1 == $create_db) {
            $output .= '<FONT color="#FF0000">' . $txt['input_invalid'] . '</FONT>';
        }

        $output = '<form action="main.php?action=create_db" method="post">';

        $output .= '<B>' . $txt['db_name'] . ' : </B><input type="text" name="db_2_create" class="trous"><BR><BR>';

        $output .= '<input type="submit" value="' . $txt['create_the_db'] . '" class="bosses">';

        $output .= '<input type="hidden" name="create_db" value="1">';

        $output .= '</form>';
    }

    return $output;
}

#####################################
# Function that optimize severals
# tables
# $sql_ref = SQL link to the base
# $selected_tbl = array of the tables
#	to optimize
#####################################
function optimize_mult_tables($sql_ref, $selected_tbl)
{
    global $txt;

    $output = '';

    if (0 == count($selected_tbl)) {
        return $txt['dump_must_choose_tbl'] . return_2_db($sql_ref->Database);
    }

    $tbl_infos = $sql_ref->getTblsInfos();

    $first = 1;

    for ($i = 0; $i < $tbl_infos['Number_Of_Tables']; $i++) {
        $current_selected_tbl = $selected_tbl[$i] ?? '';

        if ($current_selected_tbl) {
            if (1 != $first) {
                $output .= '<BR><BR>';
            } else {
                $first = 0;
            }

            $output .= do_sql_query($sql_ref, 'OPTIMIZE TABLE ' . sql_back_ticks($tbl_infos['Tables_List'][$i], $sql_ref));
        }
    }

    $output .= return_2_db($sql_ref->Database);

    return $output;
}

#####################################
# Function that drop severals tables
# $sql_ref = SQL link to the base
# $selected_tbl = array of the tables
#	to drop
#####################################
function drop_mult_tables($sql_ref, $selected_tbl)
{
    global $txt, $_POST;

    $confirm_query = $_POST['confirm_query'] ?? '';

    if (0 == count($selected_tbl) && 1 != $confirm_query) {
        return $txt['dump_must_choose_tbl'] . return_2_db($sql_ref->Database);
    }

    if (1 == $confirm_query) {
        $query = isset($_POST['extra_query']) ? magic_quote_bypass($_POST['extra_query']) : '';

        $sql_ref->query($query);

        $output = '<B>' . $txt['query_done'] . ' : </B><BR>' . $query;

        $output .= return_2_db($sql_ref->Database);
    } else {
        $tbl_infos = $sql_ref->getTblsInfos();

        $rows_counter = 0;

        $start = 1;

        $query = '';

        for ($i = 0; $i < $tbl_infos['Number_Of_Tables']; $i++) {
            $curr_tbl = $tbl_infos['Tables_List'][$i];

            $curr_selected_tbl = $selected_tbl[$i] ?? '';

            if (1 == $curr_selected_tbl) {
                $rows_counter += $tbl_infos[$curr_tbl]['Rows'];

                if (1 == $start) {
                    $start = 0;

                    $query .= 'DROP TABLE ' . sql_back_ticks($curr_tbl, $sql_ref) . ', ';
                } else {
                    $query .= sql_back_ticks($curr_tbl, $sql_ref) . ', ';
                }
            }
        }

        $query = mb_substr($query, 0, -2);

        $output = $txt['confirm_query'] . ' :<BR><B>' . nl2br($query) . '</B> ?';

        $output .= '<BR><Font color="#FF0000">' . $txt['query_is_about'] . ' ' . count($selected_tbl);

        $output .= ' ' . $txt['tbl2'] . ' ' . $txt['and'] . ' ' . $rows_counter . ' ' . mb_strtolower($txt['records2']) . '.</font>';

        $output .= '<FORM action="main.php?db=' . urlencode($sql_ref->Database) . '&action=drop" method="post">';

        $output .= '<INPUT type="submit" value="' . $txt['Yes'] . '" class="bosses">&nbsp;<input type=button value="' . $txt['No'] . '" onClick="javascript:history.go(-1);" class="bosses">';

        $output .= '<INPUT type="hidden" name="extra_query" value="' . htmlentities($query, ENT_QUOTES | ENT_HTML5) . '">';

        $output .= '<INPUT type="hidden" name="confirm_query" value="1">';

        $output .= '</FORM>';
    }

    return $output;
}

#####################################
# Function that empty severals tables
# $sql_ref = SQL link to the base
# $selected_tbl = array of the tables
#	to empty
#####################################
function empty_mult_tables($sql_ref, $selected_tbl)
{
    global $txt, $_POST;

    $confirm_query = $_POST['confirm_query'] ?? '';

    $query = '';

    if (0 == count($selected_tbl) && 1 != $confirm_query) {
        return $txt['dump_must_choose_tbl'] . return_2_db($sql_ref->Database);
    }

    if (1 == $confirm_query) {
        ### Getting the query(ies)

        $query = magic_quote_bypass($_POST['extra_query']);

        $queries_list = explode("\n", $query);

        for ($i = 0, $iMax = count($queries_list); $i < $iMax; $i++) {
            ### Doing the query(ies)

            if ('' != $queries_list[$i]) {
                $sql_ref->query($queries_list[$i]);
            }
        }

        $query = str_replace("\n", ";\n", $query);

        $output = '<B>' . $txt['query_done'] . ' : </B><BR>' . nl2br($query);

        $output .= return_2_db($sql_ref->Database);
    } else {
        $tbl_infos = $sql_ref->getTblsInfos();

        $rows_counter = 0;

        for ($i = 0; $i < $tbl_infos['Number_Of_Tables']; $i++) {
            $curr_tbl = $tbl_infos['Tables_List'][$i];

            $current_selected_tbl = $selected_tbl[$i] ?? '';

            if (1 == $current_selected_tbl) {
                ### Counting rows affected by the delete

                $rows_counter += $tbl_infos[$curr_tbl]['Rows'];

                ### Building the query(ies)

                $query .= 'DELETE FROM ' . sql_back_ticks($curr_tbl, $sql_ref) . "\n";
            }
        }

        $output = $txt['confirm_query'] . ' :<BR><B>' . nl2br($query) . '</B> ?';

        $output .= '<BR><Font color="#FF0000">' . $txt['query_is_about'] . ' ';

        $output .= $rows_counter . ' ' . mb_strtolower($txt['records2']) . '.</font>';

        $output .= '<form action="main.php?db=' . urlencode($sql_ref->Database) . '&action=empty" method="post">';

        $output .= '<input type=submit value="' . $txt['Yes'] . '" class="bosses">&nbsp;<input type=button value="' . $txt['No'] . '" onClick="javascript:history.go(-1);" class="bosses">';

        $output .= '<input type=hidden name="extra_query" value="' . htmlentities($query, ENT_QUOTES | ENT_HTML5) . '">';

        $output .= '<input type=hidden name="confirm_query" value="1">';

        $output .= '</form>';
    }

    return $output;
}

#####################################
# Function that open the desc popup
# $selected_tbl = array of the tables
#	to desc
#####################################
function open_desc_mult_tables($selected_tbl)
{
    global $txt, $db;

    $nb_selected_tbl = count($selected_tbl);

    if (!$nb_selected_tbl) {
        return $txt['dump_must_choose_tbl'] . return_2_db($db);
    }

    $selected_tbl = serialize($selected_tbl);

    $output = '<SCRIPT language="Javascript">
				popupStruc = window.open("main.php?db=' . urlencode($db) . '&action=desc_mult_tables_popup&selected_tbl=' . urlencode($selected_tbl) . '","PopupDesc","width=600,height=250,scrollbars=yes,resizable=yes,status=yes");
				function mainWindowLink() {
				        popupStruc.close();
						window.location = "main.php?db=' . urlencode($db) . '";
					}
				</SCRIPT>';

    $output .= $txt['desc_in_progress'];

    $output .= '<BR><BR><A href="javascript:mainWindowLink();">' . $txt['close_popup'] . '</A>';

    return $output;
}

#####################################
# Function that display a txt
# describe on multiple tables
# $sql_ref = SQL link to the base
# $selected_tbl = array of the tables
#	to desc
#####################################
function desc_mult_tables($sql_ref, $selected_tbl, $out_type = 0)
{
    global $txt, $font;

    $db = $sql_ref->Database;

    $selected_tbl = magic_quote_bypass($selected_tbl);

    if (0 == $out_type) {
        $output = '<HTML>
		            <HEAD>
		                <TITLE>eskuel</TITLE>
                    </HEAD>
					<BODY>
					<FONT ' . $font . '><A href="main.php?db=' . urlencode($db) . '&action=desc_mult_tables_popup&selected_tbl=' . urlencode($selected_tbl) . '&out_type=1">Enregistrer ce fichier</A></FONT><BR>';

        $output .= '<PRE>';
    } else {
        $output = '';
    }

    $selected_tbl = unserialize($selected_tbl);

    $tbl_infos = $sql_ref->getTblsInfos();

    $counter = 0;

    for ($i = 0; $i < $tbl_infos['Number_Of_Tables']; $i++) {
        $max_length[0] = 5;

        $max_length[1] = 4;

        $max_length[2] = 4;

        $max_length[3] = 3;

        $max_length[4] = 7;

        $max_length[5] = 5;

        if (isset($selected_tbl[$i])) {
            $sql_ref->query('DESCRIBE ' . sql_back_ticks($tbl_infos['Tables_List'][$i], $sql_ref));

            $result[$counter]['name'] = $tbl_infos['Tables_List'][$i];

            while ($sql_result = $sql_ref->next_record(MYSQL_NUM)) {
                $result[$counter][] = $sql_result;

                $max_length[0] = max($max_length[0], mb_strlen($sql_result[0]));

                $max_length[1] = max($max_length[1], mb_strlen($sql_result[1]));

                $max_length[2] = max($max_length[2], mb_strlen($sql_result[2]));

                $max_length[3] = max($max_length[3], mb_strlen($sql_result[3]));

                $max_length[4] = max($max_length[4], mb_strlen($sql_result[4]));

                $max_length[5] = max($max_length[5], mb_strlen($sql_result[5]));
            }

            $max_length[0] += 2;

            $max_length[1] += 2;

            $max_length[2] += 2;

            $max_length[3] += 2;

            $max_length[4] += 2;

            $max_length[5] += 2;

            ### Columns header

            $header = '+' . priv_str_repeat('-', $max_length[0]) . '+' . priv_str_repeat('-', $max_length[1]);

            $header .= '+' . priv_str_repeat('-', $max_length[2]) . '+' . priv_str_repeat('-', $max_length[3]);

            $header .= '+' . priv_str_repeat('-', $max_length[4]) . '+' . priv_str_repeat('-', $max_length[5]);

            $header .= '+';

            $display_length = mb_strlen($header);

            $header .= "\n";

            $header .= '| Field' . priv_str_repeat(' ', $max_length[0] - 6);

            $header .= '| Type' . priv_str_repeat(' ', $max_length[1] - 5);

            $header .= '| Null' . priv_str_repeat(' ', $max_length[2] - 5);

            $header .= '| Key' . priv_str_repeat(' ', $max_length[3] - 4);

            $header .= '| Default' . priv_str_repeat(' ', $max_length[4] - 8);

            $header .= '| Extra' . priv_str_repeat(' ', $max_length[5] - 6);

            $header .= '|';

            $title_bar = '+' . priv_str_repeat('-', $display_length - 2) . '+';

            $title_bar .= "\n";

            $title_bar .= '| ' . $result[$counter]['name'] . priv_str_repeat(' ', $display_length - mb_strlen($result[$counter]['name']) - 4) . ' |';

            $title_bar .= "\n";

            $title_bar .= '+' . priv_str_repeat('-', $display_length - 2) . '+';

            $title_bar .= "\n";

            $output .= $title_bar . $header;

            $output .= "\n";

            $output .= '+' . priv_str_repeat('-', $max_length[0]) . '+' . priv_str_repeat('-', $max_length[1]);

            $output .= '+' . priv_str_repeat('-', $max_length[2]) . '+' . priv_str_repeat('-', $max_length[3]);

            $output .= '+' . priv_str_repeat('-', $max_length[4]) . '+' . priv_str_repeat('-', $max_length[5]);

            $output .= '+';

            $output .= "\n";

            for ($j = 0; $j < count($result[$counter]) - 1; $j++) {
                $curr = $result[$counter][$j];

                for ($k = 0; $k <= 5; $k++) {
                    $output .= '+ ' . $curr[$k] . priv_str_repeat(' ', $max_length[$k] - mb_strlen($curr[$k]) - 1);

                    if (5 == $k) {
                        $output .= '+';
                    }
                }

                $output .= "\n";
            }

            $output .= '+' . priv_str_repeat('-', $max_length[0]) . '+' . priv_str_repeat('-', $max_length[1]);

            $output .= '+' . priv_str_repeat('-', $max_length[2]) . '+' . priv_str_repeat('-', $max_length[3]);

            $output .= '+' . priv_str_repeat('-', $max_length[4]) . '+' . priv_str_repeat('-', $max_length[5]);

            $output .= '+';

            $output .= "\n\n";

            $counter++;
        }
    }

    if (0 == $out_type) {
        $output .= '</PRE></BODY></HTML>';
    } else {
        header('Content-Type: application/text');

        header('Content-Disposition: filename="Structure-' . $db . '.txt"');

        header('Pragma: no-cache');

        header('Expires: 0');
    }

    echo $output;
}
