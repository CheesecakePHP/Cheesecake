<?php

use Cheesecake\Http\Response;

require_once(rtrim($_SERVER['DOCUMENT_ROOT'], '/') .'/vendor/autoload.php');

$Crust = require_once('src/bootstrap.php');
$result = $Crust->run();

Response::sendHeader(Response::HPPT_CONTENT_TYPE_JSON);
echo json_encode($result);
