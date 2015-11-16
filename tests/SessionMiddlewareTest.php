<?php
use Slim\Http\Environment;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\RequestBody;
use Slim\Http\UploadedFile;
use Slim\Http\Uri;

/**
 * Created by PhpStorm.
 * User: Glenn
 * Date: 2015-11-13
 * Time: 2:08 PM
 */
class PermissionMiddlewareTest extends \PHPUnit_Framework_TestCase
{
    public function requestFactory($uri = '/', $queryString = '')
    {
        $env = Environment::mock();
        $env['REQUEST_URI'] = $uri;
        $uri = Uri::createFromString('https://example.com:443/'.$queryString);
        $headers = Headers::createFromEnvironment($env);
        $cookies = ['user' => 'john',
                    'id'   => '123',];
        $serverParams = $env->all();
        $body = new RequestBody();
        $uploadedFiles = UploadedFile::createFromEnvironment($env);
        $request = new Request('GET',
            $uri,
            $headers,
            $cookies,
            $serverParams,
            $body,
            $uploadedFiles);

        return $request;

    }

    public function testPermissionMiddleware() {
        $permissionMiddleware = new \Geggleto\Service\Permission([
            '/'
        ]);

        $request = $this->requestFactory();
        $request = $request->withAttribute($permissionMiddleware->getPermissionKey(), [
           '/'
        ]);

        $response = $permissionMiddleware($request, new \Slim\Http\Response(),
            function (\Psr\Http\Message\ServerRequestInterface $req, $res) {
           return $res;
        });
        $this->assertInstanceOf(\Slim\Http\Response::class, $response);
    }

    public function testPermissionMiddlewareWithFail() {
        $permissionMiddleware = new \Geggleto\Service\Permission([
            '/'
        ]);

        $request = $this->requestFactory();
        $request = $request->withAttribute($permissionMiddleware->getPermissionKey(), [
            '/abc'
        ]);

        $this->setExpectedException('Exception');

        $response = $permissionMiddleware($request, new \Slim\Http\Response(),
            function (\Psr\Http\Message\ServerRequestInterface $req, $res) {
                return $res;
            });
    }

}
