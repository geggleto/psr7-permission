<?php
/**
 * Created by PhpStorm.
 * User: Glenn
 * Date: 2015-11-13
 * Time: 2:51 PM
 */

namespace Geggleto\Service;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Permission
{
    /** @var array */
    protected $routes = [];

    protected $permissionKey;

    public function __construct ($routes = [], $permissionKey = 'permissions')
    {
        $this->routes = $routes;
        $this->permissionKey = $permissionKey;
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $requestInterface
     * @param \Psr\Http\Message\ResponseInterface      $responseInterface
     * @param callable                                 $next
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Exception
     */
    public function __invoke (
        ServerRequestInterface $requestInterface,
        ResponseInterface $responseInterface,
        callable $next)
    {
        if (is_array($requestInterface->getAttribute($this->permissionKey))) {
            $uri = $requestInterface->getServerParams()['REQUEST_URI'];
            if (in_array($uri, $requestInterface->getAttribute($this->permissionKey))) {
                return $next($requestInterface, $responseInterface);
            } else {
                throw new \Exception("User does not have permission to view this resource");
            }
        } else {
            throw new \Exception("Permissions Not Loaded");
        }
    }

    /**
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * @return string
     */
    public function getPermissionKey()
    {
        return $this->permissionKey;
    }



}