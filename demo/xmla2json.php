<?php
header('Content-Type: application/json');

$xml = simplexml_load_file('sample.xml');

function parse_axis_info($axis)
{
    $data = array();

    foreach ($axis->HierarchyInfo as $hierarchy) {
        $data[] = array(
            "name" => (string) $hierarchy["name"]
        );
    }
    return $data;
}

function parse_axis($axis)
{
    $tuples = array();

    foreach ($axis->Tuples->Tuple as $tuple) {
        $members = array();
        foreach ($tuple->Member as $member) {
            $members[] = array(
                "Caption" => (string) $member->Caption,
                "LNum" => (int) $member->LNum
            );
        }
        $tuples[] = array(
            "Members" => $members
        );
    }
    return array(
        "Tuples" => $tuples
    );
}

$axixInfo = array();
$axisInfo[0] = parse_axis_info($xml->root->OlapInfo->AxesInfo->AxisInfo[0]);
$axisInfo[1] = parse_axis_info($xml->root->OlapInfo->AxesInfo->AxisInfo[1]);

$axis = array();
$axis[0] = parse_axis($xml->root->Axes->Axis[0]);
$axis[1] = parse_axis($xml->root->Axes->Axis[1]);

$col_count = count($axis[0]["Tuples"]);
$row_count = count($axis[1]["Tuples"]);

function empty_array($count, $val)
{
    $array = array();
    for ($i = 0; $i < $count; $i++) {
        $array[] = $val;
    }
    return $array;
}

$cell = empty_array($row_count * $col_count, "");

foreach ($xml->root->CellData->Cell as $cellData) {
    $ordinal = (int) $cellData["CellOrdinal"];
    $cell[$ordinal] = (string) $cellData->FmtValue;
}

echo(json_encode(array(
    "OlapInfo" => array(
        "AxesInfo" => $axisInfo
    ),
    "Axes" => $axis,
    "CellData" => array(
        "Cell" => $cell
    )
)));
