<?php


use Cheesecake\Http\Request;
use Cheesecake\Router;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{

    private $Router;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testCanAddGetRoute(): void
    {
        Router::get('path/to/endpoint', 'Path@To', ['endpoint'], ['middleware' => 'foo']);
        $route = Router::get('path/to/endpoint');

        self::assertEquals('GET', $route['type']);
        self::assertEquals('Path@To', $route['endpoint']);
        self::assertIsArray($route['data']);
        self::assertArrayHasKey(0, $route['data']);
        self::assertEquals('endpoint', $route['data'][0]);
        self::assertEquals('foo', $route['options']['middleware']);
    }

    public function testCanNotAddGetRoute(): void
    {
        self::expectException(\Cheesecake\Exception\RouteIsEmptyException::class);

        Router::get('', 'Path@To', ['endpoint'], ['middleware' => 'foo']);
    }

    public function testCanAddPostRoute(): void
    {
        Router::post('path/to/endpoint', 'Path@To', ['endpoint'], ['middleware' => 'foo']);
        $route = Router::post('path/to/endpoint');

        self::assertEquals('POST', $route['type']);
        self::assertEquals('Path@To', $route['endpoint']);
        self::assertIsArray($route['data']);
        self::assertArrayHasKey(0, $route['data']);
        self::assertEquals('endpoint', $route['data'][0]);
        self::assertEquals('foo', $route['options']['middleware']);
    }

    public function testCanAddPutRoute(): void
    {
        Router::put('path/to/endpoint', 'Path@To', ['endpoint'], ['middleware' => 'foo']);
        $route = Router::put('path/to/endpoint');

        self::assertEquals('PUT', $route['type']);
        self::assertEquals('Path@To', $route['endpoint']);
        self::assertIsArray($route['data']);
        self::assertArrayHasKey(0, $route['data']);
        self::assertEquals('endpoint', $route['data'][0]);
        self::assertEquals('foo', $route['options']['middleware']);
    }

    public function testCanAddPatchRoute(): void
    {
        Router::patch('path/to/endpoint', 'Path@To', ['endpoint'], ['middleware' => 'foo']);
        $route = Router::patch('path/to/endpoint');

        self::assertEquals('PATCH', $route['type']);
        self::assertEquals('Path@To', $route['endpoint']);
        self::assertIsArray($route['data']);
        self::assertArrayHasKey(0, $route['data']);
        self::assertEquals('endpoint', $route['data'][0]);
        self::assertEquals('foo', $route['options']['middleware']);
    }

    public function testCanAddDeleteRoute(): void
    {
        Router::delete('path/to/endpoint', 'Path@To', ['endpoint'], ['middleware' => 'foo']);
        $route = Router::delete('path/to/endpoint');

        self::assertEquals('DELETE', $route['type']);
        self::assertEquals('Path@To', $route['endpoint']);
        self::assertIsArray($route['data']);
        self::assertArrayHasKey(0, $route['data']);
        self::assertEquals('endpoint', $route['data'][0]);
        self::assertEquals('foo', $route['options']['middleware']);
    }

    public function testIfRouteMatches(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = 'path/to/endpoint';

        $requestMethod = Request::requestMethod();
        $requestUri = Request::requestUri();

        Router::get('path/to/endpoint', 'Path@To', ['endpoint']);
        $match = Router::match($requestMethod, $requestUri);

        self::assertTrue($match);
    }

    public function testCanGroupRoutes()
    {
        Router::group([
            'prefix' => 'v1/',
            'middleware' => 'foo'
        ], function() {
            Router::get('path/to/endpoint', 'Path@to', ['endpoint']);
            Router::post('path/to/endpoint', 'Path@to', ['endpoint']);
            Router::put('path/to/endpoint', 'Path@to', ['endpoint']);
            Router::patch('path/to/endpoint', 'Path@to', ['endpoint']);
            Router::delete('path/to/endpoint', 'Path@to', ['endpoint']);
        });

        $route = Router::get('v1/path/to/endpoint');

        self::assertEquals('GET', $route['type']);
        self::assertEquals('foo', $route['options']['middleware']);
    }

    public function testCanRoute()
    {
        require_once('PathController.php');

        $this->getMockBuilder(\App\Components\Path\Controller::class)
             //->addMethods(array('to'))
             ->getMock();

        Router::get('path/to/endpoint', 'Path@to', ['endpoint']);

        $route = Router::route('GET', 'path/to/endpoint');

        self::assertIsArray($route);
        self::assertArrayHasKey('controller', $route);
        self::assertArrayHasKey('method', $route);
        self::assertInstanceOf('\App\Components\Path\Controller', $route['controller']);
        self::assertEquals('to', $route['method']);
    }

    public function testMalformedEndpoint()
    {
        self::expectException(\Cheesecake\Exception\MalformedEndpointException::class);

        Router::get('path/to/endpoint', 'Path');
        $route = Router::route('GET', 'path/to/endpoint');
    }

    public function testCanNotRoute()
    {
        self::expectException(\Cheesecake\Exception\RouteNotDefinedException::class);
        self::expectExceptionMessage('Route "v2/path/to/endpoint" not defined');

        Router::get('path/to/endpoint', 'Path@to', ['endpoint']);
        $route = Router::route('GET', 'v2/path/to/endpoint');
    }

    public function testControllerNotExistsException()
    {
        self::expectException(\Cheesecake\Exception\ControllerNotExistsException::class);

        Router::get('foo/bar', 'Foo@bar');
        $route = Router::route('GET', 'foo/bar');
    }

    public function testMethodNotExistsException()
    {
        self::expectException(\Cheesecake\Exception\MethodNotExistsException::class);

        Router::get('path/foo', 'Path@foo');
        $route = Router::route('GET', 'path/foo');
    }

}
