<?php
require_once(rtrim($_SERVER['DOCUMENT_ROOT'], '/') .'/vendor/autoload.php');

use Cheesecake\Crust;
use Cheesecake\Exception\ControllerNotExistsException;
use Cheesecake\Exception\MethodNotExistsException;
use Cheesecake\Exception\RouteNotDefinedException;
use Cheesecake\Http\Request;
use Cheesecake\Http\Response;
use Cheesecake\Routing\Router;

try {
    $dir = new DirectoryIterator('src/routes/');

    foreach ($dir as $fileinfo) {
        if (!$fileinfo->isDot()) {
            include_once($fileinfo->getPathname());
        }
    }

    $CheesecakeCrust = new Crust(Router::route(Request::requestMethod(), Request::requestUri()));

    Response::sendHeader(Response::HTTP_STATUS_200_OK);

    $result = $CheesecakeCrust->run();
}
catch (ControllerNotExistsException | MethodNotExistsException | RouteNotDefinedException $e) {
    Response::sendHeader(Response::HTTP_STATUS_404_NOT_FOUND);

    $result = [
        'error' => [
            'code' => 404,
            'message' => 'Not Found'
        ]
    ];
}
catch (Exception $e) {
    Response::sendHeader(Response::HTTP_STATUS_500_INTERNAL_SERVER_ERROR);

    $result = [
        'error' => [
            'code' => $e->getCode() . '-root',
            'message' => $e->getMessage()
        ]
    ];
}

Response::sendHeader(Response::HPPT_CONTENT_TYPE_JSON);

echo json_encode($result);
