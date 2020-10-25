<?php

global $xoopsDB, $xoopsConfig, $xoopsModule;
include 'admin_header.php';
xoops_cp_header();
OpenTable();

echo "<h4 style='text-align:left'>" . _OP_MYSQL_TITLE . ' ' . XOOPS_DB_NAME . "</h4>
<table class='outer' width='100%' cellpadding='4' cellspacing='1'>
<tr align='center'><th>" . _OP_MYSQL_TABLE . '</th><th>' . _OP_MYSQL_SIZE . '</th><th>' . _OP_MYSQL_STATUS . '</th><th>' . _OP_MYSQL_SAVE . '</th></tr>';

$db_clean = XOOPS_DB_NAME;
$tot_data = 0;
$tot_idx = 0;
$tot_all = 0;
$total_gain = 0;
$local_query = 'SHOW TABLE STATUS FROM ' . XOOPS_DB_NAME;
$result = @$GLOBALS['xoopsDB']->queryF($local_query);
$count = 0;
if (@$GLOBALS['xoopsDB']->getRowsNum($result)) {
    while (false !== ($row = $GLOBALS['xoopsDB']->fetchBoth($result))) {
        if (0 == $count % 2) {
            $class = 'even';
        } else {
            $class = 'odd';
        }

        $count++;

        $tot_data = $row['Data_length'];

        $tot_idx = $row['Index_length'];

        $total = $tot_data + $tot_idx;

        $total /= 1024;

        $total = round($total, 3);

        $gain = $row['Data_free'];

        $gain /= 1024;

        $total_gain += $gain;

        $gain = round($gain, 3);

        $local_query = 'OPTIMIZE TABLE ' . $row[0];

        $resultat = $GLOBALS['xoopsDB']->queryF($local_query);

        if (0 == $gain) {
            echo "<tr class='$class'><td>" . (string)$row[0] . '</td>' . "<td align='right'>" . (string)$total . ' Kb' . '</td>' . "<td align='right'>" . _OP_MYSQL_RES1 . "</td><td align='right'>0 Kb</td></tr>";
        } else {
            echo "<tr class='$class'><td><b>" . (string)$row[0] . '</b></td>' . "<td align='right'><b>" . (string)$total . ' Kb' . '</b></td>' . "<td align='right'><b>" . _OP_MYSQL_RES2 . "</b></td><td align='right'><b>" . (string)$gain . ' Kb</b></td></tr>';
        }
    }
}
echo '</table>';
echo '</center>';
CloseTable();
echo '<br>';
OpenTable();
$total_gain = round($total_gain, 3);
echo '<center><b>' . _OP_MYSQL_RESULT . '</b><br><br>' . _OP_MYSQL_GAIN . ' : ' . (string)$total_gain . ' Kb<br>';
$sql_query = ('INSERT INTO ' . $xoopsDB->prefix('optimize_gain') . " (gain) VALUES ('$total_gain')");
$result = @$GLOBALS['xoopsDB']->queryF($sql_query);
$sql_query = ('SELECT * FROM ' . $xoopsDB->prefix('optimize_gain') . '');
$result = @$GLOBALS['xoopsDB']->queryF($sql_query);
$histo = 0;
$cpt = 0;
while (false !== ($row = $GLOBALS['xoopsDB']->fetchRow($result))) {
    $histo += $row[0];

    $cpt += 1;
}
echo '' . _OP_MYSQL_COUNTER . " : $cpt<br>" . _OP_MYSQL_TOTALGAIN . " : $histo Kb</center>";
CloseTable();
xoops_cp_footer();
