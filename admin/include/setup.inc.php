<?php

//Ce fichier contient des modifications par rapport à la version originale

function return_2_hp()
{
    global $txt;
    return '<BR><A href="setup.php">' . $txt['setup_back_hp'] . '</A>';
}

function return_2_eskuel()
{
    global $txt;
    return '<BR><BR><IMG src="img/home.gif">&nbsp;<A href="main.php">' . $txt['setup_return_eskuel'] . '</A>';
}

function return_string_txtarea($string, $filename)
{
    global $txt;

    $feedback = '<SCRIPT language="Javascript" type="text/javascript">
		function selectAll() {
	  		document.feedback.conf.focus();
	  		document.feedback.conf.select();
		}
		</SCRIPT>';

    $feedback .= '<FONT color="#FF0000">' . $txt['setup_cant_save'];
    $feedback .= ' <B>' . $filename . '</B> ' . $txt['setup_cant_save2'] . '</FONT>';
    $feedback .= '<FORM name="feedback">';
    $feedback .= '<TEXTAREA cols="50" rows="10" name="conf" class="trous">';
    $feedback .= $string;
    $feedback .= '</TEXTAREA>';
    $feedback .= '</FORM>';
    $feedback .= '<A href="javascript:selectAll();">' . $txt['CtrlA'] . '</A><BR><BR>';

    return $feedback;
}

function html_select_tpl($preselected, $frm_name = 'cfg_tpl', $show_default = 1)
{
    global $txt;
    $d      = dir('tpl');
    $output = '<SELECT name="' . $frm_name . '" class="trous">';

    while ($entry = $d->read()) {
        if ($entry != '.' && $entry != '..' && @is_dir('tpl/' . $entry)) {
            $avl_tpl[] = $entry;
        }
    }
    $d->close();
    asort($avl_tpl);
    if ($show_default == 1) {
        $output .= '<OPTION value="">' . $txt['default_value'] . '</OPTION>';
    }
    while (list($key, $val) = each($avl_tpl)) {
        if ($preselected == $val) {
            $selected = 'SELECTED';
        } else {
            $selected = '';
        }
        $output .= '<OPTION value="' . $val . '" ' . $selected . '>' . $val . '</OPTION>';
    }

    $output .= '</SELECT>';
    return $output;
}

function show_setup_hp()
{
    global $txt;

    $output = '<FONT size="1"><IMG src="img/setup.gif">&nbsp;<A href="setup.php?action=mod_globals">' . $txt['setup_mod_globals'] . '</A>';
    $output .= '<BR><BR><IMG src="img/setup.gif">&nbsp;<A href="setup.php?action=tpl_create">' . $txt['setup_tpl_create'] . '</A>';
    $output .= '<BR><IMG src="img/setup.gif">&nbsp;<A href="setup.php?action=tpl_mod">' . $txt['setup_tpl_mod'] . '</A>';
    $output .= '<BR><IMG src="img/setup.gif">&nbsp;<A href="setup.php?action=tpl_del">' . $txt['setup_del_tpl'] . '</A>';
    $output .= '<BR><IMG src="img/setup.gif">&nbsp;<A href="phpinfo.php">PHPInfo()</A>';
    return $output;
}

function modify_globals($conf)
{
    global $txt, $font, $_POST, $confDB;
    $i           = 0;
    $mod_globals = $_POST['mod_globals'] ?? '';

    if ($mod_globals == 1) {
        $conf_new['defaultPerRequest'] = $_POST['val_0'] ?? '';
        $conf_new['defaultConf']       = $_POST['val_1'] ?? '';
        $conf_new['tipsOfTheDay']      = $_POST['val_3'] ?? '';
        $conf_new['siteUrl']           = $_POST['val_4'] ?? '';
        $conf_new['reduceBlob']        = $_POST['val_8'] ?? '';
        $output                        = generate_config('', $conf_new);
    } else {
        $output = '<FORM action="setup.php" method="post" name="frmchgglobal">';
        $output .= '<TABLE border=0>';
        $output .= '<TR>';
        $output .= '	<TD width=200><FONT ' . $font . '><B>' . $txt['setup_defaultPerRequest'] . ' :</B></FONT></TD>';
        $output .= '	<TD><INPUT type="text" name="val_0" value="' . $conf['defaultPerRequest'] . '" class="trous"></TD>';
        $output .= '</TR>';
        $output .= '<TR>';
        $output .= '	<TD><FONT ' . $font . '><B>' . $txt['setup_tipsOfTheDay'] . ' :</B></FONT></TD>';
        $output .= '	<TD><SELECT name="val_3" class="trous">
								<OPTION value="0">' . $txt['No'] . '</OPTION>
								<OPTION value="1" ' . (($conf['tipsOfTheDay']) ? 'SELECTED' : '') . '>' . $txt['Yes'] . '</OPTION>
							</SELECT>
						</TD>';
        $output .= '</TR>';
        $output .= '<TR>';
        $output .= '	<TD width="200"><FONT ' . $font . '><B>' . $txt['setup_nb_char_blob'] . ' :</B></FONT></TD>';
        $output .= '	<TD><INPUT type="text" name="val_8" value="' . $conf['reduceBlob'] . '" class="trous" size="3"></TD>';
        $output .= '</TR>';

        $output .= '</TABLE>';
        $output .= '<INPUT type="hidden" name="action" value="mod_globals">';
        $output .= '<INPUT type="hidden" name="mod_globals" value="1">';
        $output .= '<INPUT type="submit" value="' . $txt['apply'] . '" class="bosses">';
        $output .= '</FORM>';
    }
    $output .= return_2_hp();
    return $output;
}

function tpl_create($tpl_mod = '')
{
    global $font, $txt, $_POST, $colors, $action;

    $vide        = 'img/vide.gif';    //Image vide
    $HSPACE      = 0;            //Espace Horizontal du <span>
    $VSPACE      = 0;            //Espace Vertical du <span>
    $WIDTH       = 12;            //Largeur du <span>
    $HEIGHT      = 12;            //Hauteur du <span>
    $BORDER      = 1;            //Bordure du <span>
    $CELLSPACING = 5;            //Cellspacing, quoi...
    $tpl_create  = $_POST['tpl_create'] ?? '';
    $disabled    = '';
    $altern_img  = '';
    $selected_2  = '';
    $selected_3  = '';
    $output      = '';

    if ($tpl_mod != '') {
        require __DIR__ . '/tpl/' . $tpl_mod . '/colors.inc.php';
        $disabled   = 'disabled';
        $altern_img = ($use_other_img) ? 'CHECKED' : '';

        if ($tpl_type == 0) {
            $selected_3 = 'SELECTED';
        } elseif ($tpl_type == 1) {
            $selected_2 = 'SELECTED';
        }
    } else {
        while (list($key, $val) = each($colors)) {
            ${$key} = $val;
        }
    }

    if ($tpl_create == 1) {
        $output .= generate_tpl($action);
    } else {
        $output = '<TABLE border="0" align="center" cellspacing="15">';
        $output .= '<TR>';
        $output .= '	<TD valign="top">';
        $output .= '		<form name="picker">';
        $output .= '		<font ' . $font . '>' . $txt['colorselect'] . ' : </font>';
        $output .= picker('general', 'picker', '10');
        $output .= '		</form>';
        $output .= '		<form name="recap" method="post" action="setup.php?action=' . $action . '">';
        $output .= '		<font ' . $font . '>' . $txt['name'] . ' : <INPUT type="text" name="tpl_name" value="' . $tpl_mod . '" class="trous" ' . $disabled . '>';
        $output .= '		<BR>' . $txt['color_altern_img'] . ' : <INPUT type="checkbox" name="tpl_altern" class="pick" ' . $altern_img . '>';
        $output .= '        <BR><FONT ' . $font . '>' . $txt['setup_tpl_type'] . ' : ';
        $output .= '        <SELECT name="tpl_type" class="trous">
                            <OPTION value="0" ' . $selected_3 . '>' . $txt['setup_3_columns'] . '</OPTION>
                            <OPTION value="1" ' . $selected_2 . '>' . $txt['setup_2_columns'] . '</OPTION>
                            </SELECT>';

        $output .= '		</font>';
        $output .= '	</TD>';
        $output .= '	<TD valign="top">';
        $output .= '<script language="javascript">
						function show_preview() {
							var arg = "";
							var nme;
							for (i = 1; i <= 17 ; i++) {
								nme = "n" + i;
								arg += "n" + i + "=" + escape(document.recap.elements[nme].value);
								arg += "&";	
								

							}
							arg += "tpl_type=" + document.recap.tpl_type.value;
							previewPopup = window.open(\'setup.php?action=tpl_preview&\'+ arg,\'previewPopup\',\'width=350,height=350,scrollbars=no,resizable=no,status=no\');
						}
						function r(arg) {
							document.recap.elements[arg].value = document.picker.general.value;
							if (document.getElementById) {
								document.getElementById(\'apercu\'+arg).style.background=document.picker.general.value;
							}
							else if (document.layers){
								document.layers[\'apercu\'+arg].bgColor=document.picker.general.value;
							} else
							{
								document.all[\'apercu\'+arg].style.background=document.picker.general.value;
							}
						}
						function c(arg) {
							if (document.layers){
								document.layers[\'apercu\'+arg].bgColor=document.recap.elements[arg].value;
							} else
							{
								document.all[\'apercu\'+arg].style.background=document.recap.elements[arg].value;
							}
						}
				</script>';

        $output .= '<table border="0" cellspacing="10" cellpadding="0">
					<tr> 
						<td colspan="3" nowrap><font ' . $font . '>' . $txt['color_bg'] . ' :</font></td>
						<td rowspan="12" bgcolor="' . $colors['bordercolor'] . '"><img src="' . $vide . '" width=1></td>
						<td colspan="3" nowrap><font ' . $font . '>' . $txt['color_tbl_bg'] . ' :</font></td>
				    	<td rowspan="12" bgcolor="' . $colors['bordercolor'] . '"><img src="' . $vide . '" width=1></td>
				    	<td colspan="3" nowrap><font ' . $font . '>' . $txt['color_bosse_bg'] . ' :</font></td>
					</tr>
					<tr> 
						<td><input type="button" value="=&gt;" class="bosses" onclick="r(\'n1\');"></td>
				    	<td><input type="text" name="n1" size="7" class="trous" onblur="c(\'n1\');" value="' . $bgcolor . '"></td>
				    	<td><span id="apercun1" style="position:relative; background-color: ' . $bgcolor . '; layer-background-color: ' . $bgcolor . ';"><img src="' . $vide . '" width=' . $WIDTH . ' height=' . $HEIGHT . ' border=' . $BORDER . ' hspace=' . $HSPACE . ' vspace=' . $VSPACE . ' ></span></td>
				    	<td><input type="button" value="=&gt;" class="bosses" onclick="r(\'n2\');"></td>
				    	<td><input type="text" name="n2" size="7" class="trous" onblur="c(\'n2\');" value="' . $table_bg . '"></td>
				    	<td><span id="apercun2" style="position:relative; background-color: ' . $table_bg . '; layer-background-color: ' . $table_bg . ';"><img src="' . $vide . '" width=' . $WIDTH . ' height=' . $HEIGHT . ' border=' . $BORDER . ' hspace=' . $HSPACE . ' vspace=' . $VSPACE . ' ></span></td>
				    	<td><input type="button" value="=&gt;" class="bosses" onClick="r(\'n13\');"></td>
				    	<td><input type="text" name="n13" size="7" class="trous" onblur="c(\'n13\');" value="' . $bosse_bg . '"></td>
				    	<td><span id="apercun13" style="position:relative; background-color: ' . $bosse_bg . '; layer-background-color: ' . $bosse_bg . ';"><img src="' . $vide . '" width=' . $WIDTH . ' height=' . $HEIGHT . ' border=' . $BORDER . ' hspace=' . $HSPACE . ' vspace=' . $VSPACE . ' ></span></td>
					</tr>
					<tr> 
					 	<td colspan="3" nowrap><font ' . $font . '>' . $txt['color_text'] . ' :</font></td>
				    	<td colspan="3" nowrap><font ' . $font . '>' . $txt['color_titre_bg'] . ' :</font></td>
				    	<td colspan="3" nowrap><font ' . $font . '>' . $txt['color_bosse_border'] . ' :</font></td>
					</tr>
					<tr> 
    					<td><input type="button" value="=&gt;" class="bosses" onclick="r(\'n8\');"></td>
    					<td><input type="text" name="n8" size="7" class="trous" onblur="c(\'n8\');" value="' . $text . '"></td>
    					<td><span id="apercun8" style="position:relative; background-color: ' . $text . '; layer-background-color: ' . $text . ';"><img src="' . $vide . '" width=' . $WIDTH . ' height=' . $HEIGHT . ' border=' . $BORDER . ' hspace=' . $HSPACE . ' vspace=' . $VSPACE . ' ></span></td>
    					<td><input type="button" value="=&gt;" class="bosses" onClick="r(\'n3\');"></td>
    					<td><input type="text" name="n3" size="7" class="trous" onblur="c(\'n3\');" value="' . $titre_bg . '"></td>
    					<td><span id="apercun3" style="position:relative; background-color: ' . $titre_bg . '; layer-background-color: ' . $titre_bg . ';"><img src="' . $vide . '" width=' . $WIDTH . ' height=' . $HEIGHT . ' border=' . $BORDER . ' hspace=' . $HSPACE . ' vspace=' . $VSPACE . ' ></span></td>
    					<td><input type="button" value="=&gt;" class="bosses" onClick="r(\'n15\');"></td>
    					<td><input type="text" name="n15" size="7" class="trous" onblur="c(\'n15\');" value="' . $bosse_border . '"></td>
    					<td><span id="apercun15" style="position:relative; background-color: ' . $bosse_border . '; layer-background-color: ' . $bosse_border . ';"><img src="' . $vide . '" width=' . $WIDTH . ' height=' . $HEIGHT . ' border=' . $BORDER . ' hspace=' . $HSPACE . ' vspace=' . $VSPACE . ' ></span></td>
					</tr>
  					<tr> 
    					<td colspan="3" nowrap><font ' . $font . '>' . $txt['color_link'] . ' :</font></td>
    					<td colspan="3" nowrap><font ' . $font . '>' . $txt['color_border'] . ' :</font></td>
    					<td colspan="3" nowrap><font ' . $font . '>' . $txt['color_bosse_font'] . ' :</font></td>
  					</tr>
  					<tr> 
    					<td><input type="button" value="=&gt;" class="bosses" onclick="r(\'n7\');"></td>
    					<td><input type="text" name="n7" size="7" class="trous" onblur="c(\'n7\');" value="' . $link . '"></td>
    					<td><span id="apercun7" style="position:relative; background-color: ' . $link . '; layer-background-color: ' . $link . ';"><img src="' . $vide . '" width=' . $WIDTH . ' height=' . $HEIGHT . ' border=' . $BORDER . ' hspace=' . $HSPACE . ' vspace=' . $VSPACE . ' ></span></td>
    					<td><input type="button" value="=&gt;" class="bosses" onClick="r(\'n5\');"></td>
    					<td><input type="text" name="n5" size="7" class="trous" onblur="c(\'n5\');" value="' . $bordercolor . '"></td>
    					<td><span id="apercun5" style="position:relative; background-color: ' . $bordercolor . '; layer-background-color: ' . $bordercolor . ';"><img src="' . $vide . '" width=' . $WIDTH . ' height=' . $HEIGHT . ' border=' . $BORDER . ' hspace=' . $HSPACE . ' vspace=' . $VSPACE . ' ></span></td>
    					<td><input type="button" value="=&gt;" class="bosses" onClick="r(\'n14\');"></td>
    					<td><input type="text" name="n14" size="7" class="trous" onblur="c(\'n14\');" value="' . $bosse_font . '"></td>
    					<td><span id="apercun14" style="position:relative; background-color: ' . $bosse_font . '; layer-background-color: ' . $bosse_font . ';"><img src="' . $vide . '" width=' . $WIDTH . ' height=' . $HEIGHT . ' border=' . $BORDER . ' hspace=' . $HSPACE . ' vspace=' . $VSPACE . ' ></span></td>
  					</tr>
  					<tr> 
    					<td colspan="3" nowrap><font ' . $font . '>' . $txt['color_vlink'] . ' :</font></td>
    					<td colspan="3" nowrap><font ' . $font . '>' . $txt['color_trou_bg'] . ' :</font></td>
    					<td colspan="3" nowrap><font ' . $font . '>' . $txt['color_pick_bg'] . ' :</font></td>
  					</tr>
  					<tr> 
    					<td><input type="button" value="=&gt;" class="bosses" onclick="r(\'n6\');"></td>
    					<td><input type="text" name="n6" size="7" class="trous" onblur="c(\'n6\');" value="' . $vlink . '"></td>
    					<td><span id="apercun6" style="position:relative; background-color: ' . $vlink . '; layer-background-color: ' . $vlink . ';"><img src="' . $vide . '" width=' . $WIDTH . ' height=' . $HEIGHT . ' border=' . $BORDER . ' hspace=' . $HSPACE . ' vspace=' . $VSPACE . ' ></span></td>
    					<td><input type="button" value="=&gt;" class="bosses" onClick="r(\'n10\');"></td>
    					<td><input type="text" name="n10" size="7" class="trous" onblur="c(\'n10\');" value="' . $trou_bg . '"></td>
    					<td><span id="apercun10" style="position:relative; background-color: ' . $trou_bg . '; layer-background-color: ' . $trou_bg . ';"><img src="' . $vide . '" width=' . $WIDTH . ' height=' . $HEIGHT . ' border=' . $BORDER . ' hspace=' . $HSPACE . ' vspace=' . $VSPACE . ' ></span></td>
    					<td><input type="button" value="=&gt;" class="bosses" onClick="r(\'n16\');"></td>
    					<td><input type="text" name="n16" size="7" class="trous" onblur="c(\'n16\');" value="' . $pick_bg . '"></td>
    					<td><span id="apercun16" style="position:relative; background-color: ' . $pick_bg . '; layer-background-color: ' . $pick_bg . ';"><img src="' . $vide . '" width=' . $WIDTH . ' height=' . $HEIGHT . ' border=' . $BORDER . ' hspace=' . $HSPACE . ' vspace=' . $VSPACE . ' ></span></td>
  					</tr>
  					<tr> 
    					<td colspan="3" nowrap><font ' . $font . '>' . $txt['color_alink'] . ' :&nbsp;</font></td>
    					<td colspan="3" nowrap><font ' . $font . '>' . $txt['color_trou_border'] . ' :&nbsp;</font></td>
    					<td colspan="3" nowrap><font ' . $font . '>' . $txt['color_pick_border'] . ' :&nbsp;</font></td>
  					</tr>
  					<tr> 
    					<td><input type="button" value="=&gt;" class="bosses" onClick="r(\'n9\');"></td>
    					<td><input type="text" name="n9" size="7" class="trous" onblur="c(\'n9\');" value="' . $alink . '"></td>
    					<td><span id="apercun9" style="position:relative; background-color: ' . $alink . '; layer-background-color: ' . $alink . ';"><img src="' . $vide . '" width=' . $WIDTH . ' height=' . $HEIGHT . ' border=' . $BORDER . ' hspace=' . $HSPACE . ' vspace=' . $VSPACE . ' ></span></td>
    					<td><input type="button" value="=&gt;" class="bosses" onClick="r(\'n12\');"></td>
    					<td><input type="text" name="n12" size="7" class="trous" onblur="c(\'n12\');" value="' . $trou_border . '"></td>
    					<td><span id="apercun12" style="position:relative; background-color: ' . $trou_border . '; layer-background-color: ' . $trou_border . ';"><img src="' . $vide . '" width=' . $WIDTH . ' height=' . $HEIGHT . ' border=' . $BORDER . ' hspace=' . $HSPACE . ' vspace=' . $VSPACE . ' ></span></td>
    					<td><input type="button" value="=&gt;" class="bosses" onClick="r(\'n17\');"></td>
    					<td><input type="text" name="n17" size="7" class="trous" onblur="c(\'n17\');" value="' . $pick_border . '"></td>
    					<td><span id="apercun17" style="position:relative; background-color: ' . $pick_border . '; layer-background-color: ' . $pick_border . ';"><img src="' . $vide . '" width=' . $WIDTH . ' height=' . $HEIGHT . ' border=' . $BORDER . ' hspace=' . $HSPACE . ' vspace=' . $VSPACE . ' ></span></td>
  					</tr>
  					<tr> 
    					<td colspan="3" nowrap><font ' . $font . '>' . $txt['color_titre_font'] . ' :&nbsp;</font></td>
    					<td colspan="3" nowrap><font ' . $font . '>' . $txt['color_trou_font'] . ' :&nbsp;</font></td>
    					<td colspan="3" nowrap>&nbsp;</td>
  					</tr>
  					<tr> 
    					<td><input type="button" value="=&gt;" class="bosses" onClick="r(\'n4\');"></td>
    					<td><input type="text" name="n4" size="7" class="trous" onBlur="c(\'n4\');" value="' . $titre_font . '"></td>
    					<td><span id="apercun4" style="position:relative; background-color: ' . $titre_font . '; layer-background-color: ' . $titre_font . ';"><img src="' . $vide . '" width=' . $WIDTH . ' height=' . $HEIGHT . ' border=' . $BORDER . ' hspace=' . $HSPACE . ' vspace=' . $VSPACE . ' ></span></td>
    					<td><input type="button" value="=&gt;" class="bosses" onClick="r(\'n11\');"></td>
    					<td><input type="text" name="n11" size="7" class="trous" onBlur="c(\'n11\');" value="' . $trou_font . '"></td>
    					<td><span id="apercun11" style="position:relative; background-color: ' . $trou_font . '; layer-background-color: ' . $trou_font . ';"><img src="' . $vide . '" width=' . $WIDTH . ' height=' . $HEIGHT . ' border=' . $BORDER . ' hspace=' . $HSPACE . ' vspace=' . $VSPACE . ' ></span></td>
    					<td colspan=3>&nbsp;</td>
  					</tr>
					</table>';
        $output .= '	</TD>';
        $output .= '</TR>';
        $output .= '</TABLE>';
        $output .= '<TABLE width="100%">';
        $output .= '<TR>';
        $output .= '	<TD align="right" width="50%">';
        $output .= '		<input type="button" value="' . $txt['color_preview'] . '" onClick="show_preview();" class="bosses">';
        $output .= '	</TD>';
        $output .= '	<TD width="50%">';
        $output .= '		<input type="submit" value="' . $txt['record'] . '" class="bosses" onload="update();">';
        $output .= '	</TD>';
        $output .= '</TR>';
        $output .= '</TABLE>';
        $output .= '<INPUT type="hidden" name="tpl_name_disabled" value="' . $tpl_mod . '">';
        $output .= '<INPUT type="hidden" name="tpl_mod" value="1">';
        $output .= '<INPUT type="hidden" name="tpl_create" value="1">';
        $output .= '</form>';
    }
    $output .= return_2_hp();
    return $output;
}

function tpl_preview(&$new_colors)
{
    global $_GET, $font, $css, $txt;
    $css          = '12';
    $tpl_title_bg = $_GET['tpl_title_bg'] ?? '';
    $tpl_type     = $_GET['tpl_type'] ?? '';

    for ($i = 1; $i <= 17; $i++) {
        ${"n$i"} = $_GET["n$i"];
    }
    $new_colors = [
        'name'         => '',
        'bgcolor'      => $n1,
        'table_bg'     => $n2,
        'titre_bg'     => $n3,
        'titre_font'   => $n4,
        'titre_img'    => $tpl_title_bg,
        'bordercolor'  => $n5,
        'vlink'        => $n6,
        'link'         => $n7,
        'text'         => $n8,
        'alink'        => $n9,
        'trou_bg'      => $n10,
        'trou_border'  => $n12,
        'trou_font'    => $n11,
        'bosse_bg'     => $n13,
        'bosse_font'   => $n14,
        'bosse_border' => $n15,
        'pick_bg'      => $n16,
        'pick_border'  => $n17,
        'tpl_type'     => $tpl_type,

    ];

    $output = ' <font ' . $font . '>' . $txt['preview_text'] . '</font><br>
			<font ' . $font . '>' . $txt['preview_link'] . '</font><br>
			<font ' . $font . '>' . $txt['preview_vlink'] . '</font><br>
			<BR><form>
			<input type="text" class="trous" value="' . $txt['preview_input'] . '"><br><font ' . $font . '>' . $txt['preview_pick'] . '</font> : 
			<input type="checkbox" name="checkbox" value="checkbox" class="pick"><br>
			<input type="button" name="Submit" value="' . $txt['preview_button'] . '" class="bosses">
			</form>
			<script language="javascript">
			window.focus();
			</script>';

    return $output;
}

function tpl_mod()
{
    global $txt, $font, $colors, $_POST;

    $tpl_mod = $_POST['tpl_mod'] ?? '';
    $output  = '';

    if ($tpl_mod == 1) {
        $tpl = $_POST['tpl'] ?? '';;
        $output = tpl_create($tpl);
    } else {
        $output .= '<IMG src="img/setup.gif">&nbsp;' . $txt['setup_tpl_mod'] . ' :';
        $output .= '<FORM action="setup.php?action=tpl_mod" method="post">';
        $output .= html_select_tpl('', 'tpl', 0);
        $output .= '<INPUT type="submit" value="' . $txt['ok'] . '" class="trous">';
        $output .= '<INPUT type="hidden" name="tpl_mod" value="1">';
        $output .= '</FORM>';
        $output .= return_2_hp();
    }

    return $output;
}

function tpl_del()
{
    global $txt, $colors, $_POST;

    $del_tpl      = $_POST['del_tpl'] ?? '';
    $confirm      = $_POST['confirm'] ?? '';
    $tpl_2_delete = $_POST['tpl_2_delete'] ?? '';
    $output       = '';

    ### Can't delete a file (unlink) on platform != linux
    if (!eregi('linux', PHP_OS)) {
        return $txt['setup_not_supported_windows'];
    }
    ### Initializing del_error var to 0 ==> if =1 error occured
    $del_error = 0;

    if ($del_tpl == 1 && $confirm == 1) {
        if (is_writable('./tpl/' . $tpl_2_delete)) {
            $d = dir("./tpl/$tpl_2_delete");

            while ($entry = $d->read()) {
                if ($entry != '.' && $entry != '..') {
                    if (is_dir('./tpl/' . $tpl_2_delete . '/' . $entry)) {
                        $e                        = dir("./tpl/$tpl_2_delete/$entry");
                        $avl_files[]['directory'] = 'tpl/' . $tpl_2_delete . '/' . $entry;
                        while ($entry2 = $e->read()) {
                            if ($entry2 != '.' && $entry2 != '..') {
                                if (is_writable('./tpl/' . $tpl_2_delete . '/' . $entry . '/' . $entry2)) {
                                    $avl_files[]['file'] = './tpl/' . $tpl_2_delete . '/' . $entry . '/' . $entry2;
                                } else {
                                    $del_error = 1;
                                }
                            }
                        }
                        $e->close();
                    } elseif (is_writable('./tpl/' . $tpl_2_delete . '/' . $entry)) {
                        $avl_files[]['file'] = './tpl/' . $tpl_2_delete . '/' . $entry;
                    } else {
                        $del_error = 1;
                    }
                }
            }

            $d->close();
        } else {
            $del_error = 1;
        }
        if (!$del_error) {
            for ($i = count($avl_files) - 1; $i >= 0; $i--) {
                if ($avl_files[$i]['file'] != '') {
                    unlink($avl_files[$i]['file']);
                } elseif ($avl_files[$i]['directory'] != '') {
                    rmdir($avl_files[$i]['directory']);
                }
            }
            rmdir('tpl/' . $tpl_2_delete);
            $output .= $txt['setup_tpl'] . ' <B>' . $tpl_2_delete . '</B> ' . $txt['been_deleted'] . '.<BR>';
        } else {
            $output = $txt['error_occured'] . '.';
        }
    } elseif ($del_tpl == 1) {
        $output = '<FORM action="setup.php?action=tpl_del" method="post">';
        $output .= $txt['setup_sure'] . ' <B>' . $tpl_2_delete . '</B> ?';
        $output .= '<BR><INPUT type="submit" value="' . $txt['Yes'] . '" class="bosses"> <input type=button value="' . $txt['No'] . '" onClick="javascript:history.go(-1);" class="bosses">';
        $output .= '<INPUT type="hidden" name="del_cfg" value="1">';
        $output .= '<INPUT type="hidden" name="confirm" value="1">';
        $output .= '<INPUT type="hidden" name="del_tpl" value="1">';
        $output .= '<INPUT type="hidden" name="tpl_2_delete" value="' . $_POST['tpl_2_delete'] . '">';
        $output .= '</FORM>';
    } else {
        $output .= '<FONT size="1"><BR><IMG src="img/setup.gif">&nbsp;' . $txt['setup_del_tpl'] . ' :';
        $output .= '<FORM action="setup.php?action=tpl_del" method="post" name="frmselectcfg">';
        $output .= html_select_tpl('', 'tpl_2_delete', 0);
        $output .= '<INPUT type="hidden" name="del_tpl" value="1">';
        $output .= '&nbsp;<INPUT type="submit" value="' . $txt['ok'] . '" class="bosses">';
        $output .= '</FORM>';
    }
    $output .= return_2_hp();

    return $output;
}

function generate_config($confDB_new = '', $conf_new = '', $install = 0)
{
    global $txt, $HTTP_COOKIE_VARS, $confDB;
    @require __DIR__ . '/config.inc.php';

    $confDBCookie    = $HTTP_COOKIE_VARS['ConfDBCookie'] ?? '';
    $old_config_name = $confDB[$confDBCookie]['name'] ?? '';

    ### Modifying only the new settings
    $confDB_new      = ($confDB_new == '') ? $confDB : $confDB_new;
    $conf_new        = ($conf_new == '') ? $conf : $conf_new;
    $new_config_name = $confDB_new[$confDBCookie]['name'] ?? '';

    ### The config name has been changed (ie deleting a conf, etc), deleting the cookie
    if ($old_config_name != $new_config_name) {
        setcookie('ConfDBCookie', '');
    }
    $conf_output = "<?php\n\n";
    ### First part : generating the DB config //Modifications pour xoops par albnn@netcourrier.com

    $conf_output .= 'include "../../../mainfile.php"; //Ajout pour xoops par albnn@netcourrier.com' . "\n\n";
    $conf_output .= '### eskuel\'s configuration file' . "\n";
    $conf_output .= '### Configuration number 0' . "\n";
    $conf_output .= '$confDB[\'0\'][\'name\'] = $xoopsConfig[\'dbname\'];' . "\n";
    $conf_output .= '$confDB[\'0\'][\'host\'] = $xoopsConfig[\'dbhost\'];' . "\n";
    $conf_output .= '$confDB[\'0\'][\'user\'] = $xoopsConfig[\'dbuname\'];' . "\n";
    $conf_output .= '$confDB[\'0\'][\'password\'] = $xoopsConfig[\'dbpass\'];' . "\n";
    $conf_output .= '$confDB[\'0\'][\'db\'] = $xoopsConfig[\'dbname\'];' . "\n";
    $conf_output .= '$confDB[\'0\'][\'tpl\'] = \'' . $confDB_new[0]['tpl'] . '\';' . "\n";
    $conf_output .= "\n\n";

    ### Second part : generating the globals var conf

    $conf_new['defaultConf'] = 0; //Modifié pour xoops par albnn@netcourrier.com

    while (list($key, $val) = each($conf_new)) {
        //Modifications pour xoops par albnn@netcourrier.com
        if ($key == 'siteUrl') {
            $conf_output .= '$conf[\'' . $key . '\'] = XOOPS_URL;' . "\n";
        } else {
            $conf_output .= '$conf[\'' . $key . '\'] = \'' . $val . '\';' . "\n";
        }
    }
    $conf_output .= "\n";
    $conf_output .= '?>';

    ### The file is writeable, overwriting it
    if (is_writable('config.inc.php')) {
        $fp = fopen('config.inc.php', 'w');
        fwrite($fp, $conf_output);
        fclose($fp);
        $output = $txt['setup_config_saved'];
        $output .= '<BR>';
    } ### The file is not writeable
    else {
        $output .= return_string_txtarea($conf_output, 'config.inc.php');
    }
}

return $output;
}

function generate_tpl($type)
{
    global $_POST, $txt, $font, $colors;

    for ($i = 1; $i <= 17; $i++) {
        ${"n$i"} = $_POST["n$i"] ?? '';
    }
    $tpl_name   = (isset($_POST['tpl_name_disabled']) && $_POST['tpl_name_disabled'] != '') ? $_POST['tpl_name_disabled'] : $_POST['tpl_name'];
    $tpl_altern = $_POST['tpl_altern'] ?? '';
    $tpl_altern = ($tpl_altern == 'on') ? 1 : 0;
    $tpl_type   = $_POST['tpl_type'];
    $output     = '';

    $bgcolor      = $n1;
    $table_bg     = $n2;
    $titre_bg     = $n3;
    $titre_font   = $n4;
    $bordercolor  = $n5;
    $vlink        = $n6;
    $link         = $n7;
    $text         = $n8;
    $alink        = $n9;
    $trou_bg      = $n10;
    $trou_border  = $n12;
    $trou_font    = $n11;
    $bosse_bg     = $n13;
    $bosse_font   = $n14;
    $bosse_border = $n15;
    $pick_bg      = $n16;
    $pick_border  = $n17;

    ### Generating the template

    $conf_output = "<?php\n\n";
    $conf_output .= "// Template : $tpl_name\n\n";
    $conf_output .= '$font = \'' . $font . '\';' . "\n";
    $conf_output .= '$bgcolor = \'' . $bgcolor . '\';' . "\n";
    $conf_output .= '$table_bg = \'' . $table_bg . '\';' . "\n";
    $conf_output .= '$titre_bg = \'' . $titre_bg . '\';' . "\n";
    $conf_output .= '$titre_font = \'' . $titre_font . '\';' . "\n";
    $conf_output .= '$bordercolor = \'' . $bordercolor . '\';' . "\n";
    $conf_output .= '$vlink = \'' . $vlink . '\';' . "\n";
    $conf_output .= '$link = \'' . $link . '\';' . "\n";
    $conf_output .= '$text = \'' . $text . '\';' . "\n";
    $conf_output .= '$alink = \'' . $alink . '\';' . "\n";
    $conf_output .= '$trou_bg = \'' . $trou_bg . '\';' . "\n";
    $conf_output .= '$trou_border = \'' . $trou_border . '\';' . "\n";
    $conf_output .= '$trou_font = \'' . $trou_font . '\';' . "\n";
    $conf_output .= '$bosse_bg = \'' . $bosse_bg . '\';' . "\n";
    $conf_output .= '$bosse_font = \'' . $bosse_font . '\';' . "\n";
    $conf_output .= '$bosse_border = \'' . $bosse_border . '\';' . "\n";
    $conf_output .= '$pick_bg = \'' . $pick_bg . '\';' . "\n";
    $conf_output .= '$pick_border = \'' . $pick_border . '\';' . "\n";
    $conf_output .= '$use_other_img = ' . $tpl_altern . ';' . "\n";
    $conf_output .= '$tpl_type = ' . $tpl_type . ';' . "\n";
    $conf_output .= "\n\n?>";

    ### Checking if the template have a name :o)
    if ($tpl_name == '') {
        $output .= $txt['setup_give_name_skin'] . '<BR><BR><A href="javascript:history.go(-1);">' . $txt['back'] . '</A><BR>';
        return $output;
    }

    $config_dir  = 'tpl/' . $tpl_name;
    $config_file = $config_dir . '/colors.inc.php';

    if ($type != 'tpl_mod') {
        ### If we are creating the template
        if (is_dir($config_dir)) {
            ### The directory already exist
            $output .= $txt['setup_another_name'] . '.<BR><BR><A href="javascript:history.go(-1);">' . $txt['back'] . '</A><BR>';
            return $output;
        }

        ### Trying to create the directory
        if (@mkdir('tpl/' . $tpl_name, 0755)) {
            ### creating colors.inc.php
            if ($fp = @fopen($config_file, 'w')) {
                fwrite($fp, $conf_output);
                fclose($fp);
                $output = $txt['setup_config_saved'];
                $output .= '<BR>';
            } else {
                ### something got wrong, outputting as a file
                $output .= return_string_txtarea($conf_output, $config_file);
                return $output;
            }
        } else {
            ### something got wrong, outputting as a file
            $output .= return_string_txtarea($conf_output, $config_file);
            return $output;
        }
    } else {
        ### Modifying the template
        if (is_writable($config_file)) {
            $fp = fopen($config_file, 'w');
            fwrite($fp, $conf_output);
            fclose($fp);
            $output = $txt['setup_config_saved'];
            $output .= '<BR>';
        } else {
            ### something got wrong, outputting as a file
            $output .= return_string_txtarea($conf_output, $config_file);
            return $output;
        }
    }

    return $output;
}


