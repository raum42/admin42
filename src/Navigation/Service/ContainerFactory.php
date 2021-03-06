<?php

/*
 * admin42
 *
 * @package admin42
 * @link https://github.com/kiwi-suite/admin42
 * @copyright Copyright (c) 2010 - 2017 kiwi suite (https://www.kiwi-suite.com)
 * @license MIT License
 * @author kiwi suite <tech@kiwi-suite.com>
 */


namespace Admin42\Navigation\Service;

use Admin42\Navigation\Page\Page;
use Core42\Navigation\Container;
use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\Router\RouteMatch;
use Zend\Router\RouteStackInterface;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

class ContainerFactory implements FactoryInterface
{
    /**
     * @var RouteStackInterface
     */
    protected $router;

    /**
     * @var RouteMatch
     */
    protected $routeMatch;

    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string $requestedName
     * @param  null|array $options
     * @throws ServiceNotFoundException if unable to resolve the service
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service
     * @throws ContainerException if any other error occurs
     * @return object
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $this->router = $container->get('Router');
        $this->routeMatch = $container->get('Application')->getMvcEvent()->getRouteMatch();

        $navigation = new Container();

        foreach ($container->get('config')['navigation']['containers'][$requestedName] as $pageSpec) {
            $navigation->addPage($this->createPage($pageSpec));
        }
        $navigation->sort();

        return $navigation;
    }

    /**
     * @param $pageSpec
     * @return Page
     */
    protected function createPage($pageSpec)
    {
        $page = new Page($this->router, $this->routeMatch);
        $page->setIcon((!empty($pageSpec['icon'])) ? $pageSpec['icon'] : null);
        $page->setRoute((!empty($pageSpec['route'])) ? $pageSpec['route'] : null);
        $page->setLabel((!empty($pageSpec['label'])) ? $pageSpec['label'] : null);
        $page->setOrder((!empty($pageSpec['order'])) ? (int) $pageSpec['order'] : null);
        $page->setOptions((!empty($pageSpec['options'])) ? $pageSpec['options'] : []);
        $page->setParams((!empty($pageSpec['params'])) ? $pageSpec['params'] : []);
        $permission = null;
        if (\mb_strlen($page->getRoute())) {
            $permission = 'route/' . $page->getRoute();
        }
        if (\array_key_exists('permission', $pageSpec)) {
            $permission = $pageSpec['permission'];
        }
        $page->setPermission($permission);

        if (!empty($pageSpec['pages'])) {
            foreach ($pageSpec['pages'] as $subPageSpec) {
                $page->addPage($this->createPage($subPageSpec));
            }
        }

        return $page;
    }
}
