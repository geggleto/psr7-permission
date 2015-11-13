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
    /** @var \PDO */
    protected $pdo;

    /** @var array */
    protected $routes = [];

    /**
     * Permission constructor.
     *
     * @param \PDO $pdo
     */
    public function __construct (\PDO $pdo)
    {
        $this->pdo = $pdo;

        $this->loadRoutes();
    }

    protected function loadRoutes() {
        $stmt = $this->pdo->prepare("select * from system_routes");
        $stmt->execute();
        $this->routes = $stmt->fetchAll();
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
        if (is_array($requestInterface->getAttribute('permissions'))) {
            $uri = $requestInterface->getServerParams()['REQUEST_URI'];
            if (in_array($uri, $requestInterface->getAttribute('permissions'))) {
                return $next($requestInterface, $responseInterface);
            } else {
                throw new \Exception("User does not have permission to view this resource");
            }
        } else {
            throw new \Exception("Permissions Not Loaded");
        }
    }


    public function getPermissions($key) {
        $stmt = $this->pdo->prepare("select * from permitted_routes where key = ?");
        return $stmt->execute([$key]);
    }

    public function setPermissions($key, $routes) {

    }

    /**
     * @return array
     */
    public function getRoutes ()
    {
        return $this->routes;
    }

    /**
     * @return \PDO
     */
    public function getPdo ()
    {
        return $this->pdo;
    }


    public function migrate() {
        $stmt = $this->pdo->prepare("");
    }

}