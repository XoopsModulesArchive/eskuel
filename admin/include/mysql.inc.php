<?php

/***************************************************************************
 * ____  _   _ ____  _              _     _  _   _   _
 * |  _ \| | | |  _ \| |_ ___   ___ | |___| || | | | | |
 * | |_) | |_| | |_) | __/ _ \ / _ \| / __| || |_| | | |
 * |  __/|  _  |  __/| || (_) | (_) | \__ \__   _| |_| |
 * |_|   |_| |_|_|    \__\___/ \___/|_|___/  |_|  \___/
 *
 * mysql.inc.php  -  A Mysql Class
 * -------------------
 * begin                : Sat Oct 20 2001
 * copyright            : (C) 2002-? PHPtools4U.com - Mathieu LESNIAK
 * email                : support@phptools4u.com
 ***************************************************************************/

/***************************************************************************
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 ***************************************************************************/
class DB
{
    public $Host = '';        // Hostname of our MySQL server
    public $Database = '';    // Logical database name on that server
    public $User = '';    // Database user
    public $Password = '';        // Database user's password
    public $only_DB = '';

    public $Link_ID = 0;        // Result of mysql_connect()
    public $Query_ID = 0;        // Result of most recent $GLOBALS['xoopsDB']->queryF()
    public $Record = [];  // Current $GLOBALS['xoopsDB']->fetchBoth()->result
    public $Row;                   // Current row number
    public $Errno = 0;        // Error state of query
    public $Error = '';

    public $DB_selected;

    public $Infos = ['Number_Of_Tables' => 0, 'Version' => -1];            // avoiding multiple calls to db, storing infos

    # Create a link id to the MySQL database

    public function __construct($arrConfig)
    {
        $this->Host = $arrConfig['HOST'];

        $this->Database = $arrConfig['DB'];

        $this->User = $arrConfig['USER'];

        $this->Password = $arrConfig['PASSWORD'];

        if ('' != $arrConfig['ONLY_DB']) {
            $this->only_DB = $arrConfig['ONLY_DB'];
        }

        $this->connect();
    }

    # Stop the execution of the script

    # in case of error

    # $msg : the message that'll be printed

    public function halt($msg, $design = 0)
    {
        $output = '<FONT COLOR="#FF0000"><B>Erreur MySQL :</B> <BR>' . nl2br($msg) . "<BR>\n";

        $output .= "<B>Erreur MySQL numéro</B>: $this->Errno ($this->Error)<BR><BR></FONT>\n";

        $this->Link_ID = 0;

        if (1 != $design) {
            display_design($output);
        } else {
            setcookie('ConfDBCookie', '');

            display_design($output, 1);
        }

        die();
    }

    # Connect to the MySQL server

    public function connect()
    {
        global $DBType;

        if (0 == $this->Link_ID) {
            $this->Link_ID = @mysql_connect($this->Host, $this->User, $this->Password);

            if (!$this->Link_ID) {
                $this->halt('<BR>Database connection failed.<BR>Please go to <A href="setup.php">setup</A> to fix it<BR><BR>Echec de connexion à la base de donn&eacute;es.<BR>Merci de v&eacute;rifier vos <A href="setup.php">param&egrave;tres de connexion</A><BR>', 1);
            }
        }
    }

    public function select_db($db)
    {
        $this->Database = $db;

        if (0 == $this->Link_ID) {
            $this->connect();
        }

        $SelectResult = mysqli_select_db($GLOBALS['xoopsDB']->conn, $db, $this->Link_ID) or $this->halt('cannot select database <I>' . $this->Database . '</I>');

        if (!$SelectResult) {
            $this->halt('cannot select database <I>' . $this->Database . '</I>');
        } else {
            $this->DB_selected = 1;

            $this->Database = $db;
        }
    }

    # Send a query to the MySQL server

    # $Query_String = the query

    public function query($Query_String)
    {
        //echo "<B>Requête :</B> : <PRE>$Query_String</PRE><BR>";

        if (0 == $this->Link_ID) {
            $this->connect();
        }

        $this->Query_ID = $GLOBALS['xoopsDB']->queryF($Query_String, $this->Link_ID);

        $this->Row = 0;

        $this->Errno = $GLOBALS['xoopsDB']->errno();

        $this->Error = $GLOBALS['xoopsDB']->error();

        if (!$this->Query_ID) {
            $this->halt('Invalid SQL: ' . $Query_String);
        }

        return $this->Query_ID;
    }

    # return the next record of a MySQL query

    # in an array

    public function next_record($type = MYSQL_BOTH)
    {
        $this->Record = $GLOBALS['xoopsDB']->fetchBoth($this->Query_ID, $type);

        $this->Row += 1;

        $this->Errno = $GLOBALS['xoopsDB']->errno();

        $this->Error = $GLOBALS['xoopsDB']->error();

        $stat = is_array($this->Record);

        if (!$stat) {
            $GLOBALS['xoopsDB']->freeRecordSet($this->Query_ID);

            $this->Query_ID = 0;
        }

        return $this->Record;
    }

    # Return the number of rows affected by a query

    # (except insert and delete query)

    public function num_rows()
    {
        return $GLOBALS['xoopsDB']->getRowsNum($this->Query_ID);
    }

    # Return the number of affected rows

    # by a UPDATE, INSERT or DELETE query

    public function affected_rows()
    {
        return $GLOBALS['xoopsDB']->getAffectedRows($this->Link_ID);
    }

    # Return the id of the last inserted element

    public function insert_id()
    {
        return $GLOBALS['xoopsDB']->getInsertId($this->Link_ID);
    }

    # Optimize a table

    # $tbl_name : the name of the table

    public function optimize($tbl_name)
    {
        $this->connect();

        $this->Query_ID = @$GLOBALS['xoopsDB']->queryF("OPTIMIZE TABLE $tbl_name", $this->Link_ID);
    }

    public function fetch_field()
    {
        return mysql_fetch_field($this->Query_ID);
    }

    public function field_seek($offset)
    {
        return mysql_field_seek($this->Query_ID, $offset);
    }

    public function field_table($offset)
    {
        return mysql_field_table($this->Query_ID, $offset);
    }

    # Free the memory used by a result

    public function clean_results()
    {
        if (0 != $this->Query_ID) {
            mysql_freeresult($this->Query_ID);
        }
    }

    # Close the link to the MySQL database

    public function close()
    {
        if (0 != $this->Link_ID) {
            $GLOBALS['xoopsDB']->close($this->Link_ID);
        }
    }

    public function storeTblsInfos()
    {
        @$this->query("SHOW VARIABLES LIKE 'version'");

        $resultset = $this->next_record();

        if (is_array($resultset)) {
            $mysql_version = $resultset['Value'];

            $version_array = explode('.', $mysql_version);

            $this->Infos['Version'] = (int)sprintf('%d%d%02d', $version_array[0], $version_array[1], (int)$version_array[2]);

            $this->Infos['Full_Version'] = $mysql_version;
        } else {
            $this->Infos['Version'] = -1;
        }

        if ($this->Infos['Version'] > 32303) {
            $this->query('SHOW TABLE STATUS FROM `' . $this->Database . '`');

            $tbls_counter = 0;

            while ($tmp_var = $this->next_record()) {
                $this->Infos['Tables_List'][] = $tmp_var['Name'];

                $this->Infos[$tmp_var['Name']]['Type'] = $tmp_var['Type'];

                $this->Infos[$tmp_var['Name']]['Row_Format'] = $tmp_var['Row_format'];

                $this->Infos[$tmp_var['Name']]['Rows'] = $tmp_var['Rows'];

                $this->Infos[$tmp_var['Name']]['Avg_Row_Length'] = $tmp_var['Avg_row_length'];

                $this->Infos[$tmp_var['Name']]['Data_Length'] = $tmp_var['Data_length'];

                $this->Infos[$tmp_var['Name']]['Max_Data_Length'] = $tmp_var['Max_data_length'];

                $this->Infos[$tmp_var['Name']]['Index_Length'] = $tmp_var['Index_length'];

                $this->Infos[$tmp_var['Name']]['Data_Free'] = $tmp_var['Data_free'];

                $this->Infos[$tmp_var['Name']]['Auto_Increment'] = $tmp_var['Auto_increment'] ?? '';

                $this->Infos[$tmp_var['Name']]['Create_Time'] = $tmp_var['Create_time'];

                $this->Infos[$tmp_var['Name']]['Update_Time'] = $tmp_var['Update_time'] ?? '';

                $this->Infos[$tmp_var['Name']]['Check_Time'] = $tmp_var['Check_time'] ?? '';

                $this->Infos[$tmp_var['Name']]['Comment'] = $tmp_var['Comment'];

                $tbls_counter++;
            }

            $this->Infos['Number_Of_Tables'] = $tbls_counter;
        } else {
            $result = mysql_list_tables($this->Database);

            $tbls_counter = 0;

            if ('' != $this->Database) {
                while (false !== ($row = $GLOBALS['xoopsDB']->fetchRow($result))) {
                    $this->Infos['Tables_List'][] = $row[0];

                    $this->query('SELECT COUNT(*) FROM ' . sql_back_ticks($row[0], $this));

                    [$this->Infos[$row[0]]['Rows']] = $this->next_record();

                    $this->Infos[$row[0]]['Type'] = '';

                    $this->Infos[$row[0]]['Row_Format'] = '';

                    $this->Infos[$row[0]]['Avg_Row_Length'] = '';

                    $this->Infos[$row[0]]['Data_Length'] = '';

                    $this->Infos[$row[0]]['Max_Data_Length'] = '';

                    $this->Infos[$row[0]]['Index_Length'] = '';

                    $this->Infos[$row[0]]['Data_Free'] = '';

                    $this->Infos[$row[0]]['Auto_Increment'] = '';

                    $this->Infos[$row[0]]['Create_Time'] = '';

                    $this->Infos[$row[0]]['Update_Time'] = '';

                    $this->Infos[$row[0]]['Check_Time'] = '';

                    $this->Infos[$row[0]]['Comment'] = '';

                    $tbls_counter++;
                }

                $this->Infos['Number_Of_Tables'] = $tbls_counter;
            }
        }
    }

    public function getTblsInfos()
    {
        return $this->Infos;
    }
}
