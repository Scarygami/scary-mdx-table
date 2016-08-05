<?php
header('Content-Type: application/json');

$xml = simplexml_load_file('sample.xml');

function parse_axis_info($axis)
{
    $data = array();

    foreach ($axis->HierarchyInfo as $hierarchy) {
        $data[] = (string) $hierarchy["name"];
    }
    return $data;
}

function parse_axis($axis)
{
    $data = array();

    foreach ($axis->Tuples->Tuple as $tuple) {
        $members = array();
        foreach($tuple->Member as $member) {
            $members[] = array(
                "name" => (string) $member->Caption,
                "level" => (int) $member->LNum
            );
        }
        $data[] = $members;
    }
    return $data;
}

$colsInfo = parse_axis_info($xml->root->OlapInfo->AxesInfo->AxisInfo[0]);
$rowsInfo = parse_axis_info($xml->root->OlapInfo->AxesInfo->AxisInfo[1]);

$cols = parse_axis($xml->root->Axes->Axis[0]);
$rows = parse_axis($xml->root->Axes->Axis[1]);

$col_count = count($cols);
$row_count = count($rows);

function empty_array($count, $val)
{
    $array = array();
    for ($i = 0; $i < $count; $i++) {
        $array[] = $val;
    }
    return $array;
}

$cells = empty_array($row_count, empty_array($col_count, 0));

foreach($xml->root->CellData->Cell as $cell)
{
    $ordinal = (int) $cell["CellOrdinal"];
    $row = floor($ordinal / $col_count);
    $col = $ordinal - $row * $col_count;
    $cells[$row][$col] = (float) $cell->Value;
}


echo(json_encode(array(
    "rowsInfo" => $rowsInfo,
    "rows" => $rows,
    "colsInfo" => $colsInfo,
    "cols" => $cols,
    "cells" => $cells
)));
