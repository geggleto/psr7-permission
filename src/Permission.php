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

    public function loadRoutes() {
        $stmt = $this->pdo->prepare("select uri from system_routes");
        $stmt->execute();
        $this->routes = $stmt->fetchAll();
    }

    public function addRoute($uri) {
        $stmt = $this->pdo->prepare("insert into system_routes VALUES (?) ");
        $stmt->execute([$uri]);
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

    /**
     * @param $key
     * @return bool
     */
    public function getPermissions($key) {
        $stmt = $this->pdo->prepare("select uri from permitted_routes where key = ?");
        return $stmt->execute([$key]);
    }

    /**
     * @param       $key
     * @param array $routes
     */
    public function setPermissions($key, $routes = []) {
        $this->pdo->query("delete from permitted_routes where key = ?");
        $stmt = $this->pdo->prepare("insert into `permitted_routes` (key, uri) values(?,?)");
        foreach ($routes as $route) {
            $stmt->execute([$key, $route]);
        }
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
        $this->pdo->query("CREATE TABLE `system_routes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uri` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1");

        $this->pdo->query("CREATE TABLE `permitted_routes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) DEFAULT NULL,
  `uri` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1");
    }

}