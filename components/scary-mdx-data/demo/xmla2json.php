<?php
header('Content-Type: application/json');

$xml = simplexml_load_file('sample.xml');

echo json_encode($xml);
