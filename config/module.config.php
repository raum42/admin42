<?php
namespace Admin42;

return array(
    'controllers' => array(
        'invokables' => array(
            __NAMESPACE__ . '\Controller\User'              => __NAMESPACE__ . '\Controller\UserController',
        ),
    ),

    'view_manager' => array(
        'display_not_found_reason'  => true,
        'display_exceptions'        => true,
        'not_found_template'        => 'admin/error/404',
        'exception_template'        => 'admin/error/index',
        'layout'                    => 'admin/layout/layout',
        'template_map'              => array(
            'admin/layout/layout'       => __DIR__ . '/../view/layout/layout.phtml',
            'admin/error/404'           => __DIR__ . '/../view/error/404.phtml',
            'admin/error/index'         => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack'       => array(
            __NAMESPACE__               => __DIR__ . '/../view',
        ),
        'strategies'                => array(
            'ViewJsonStrategy',
        ),
    ),

    'authentication' => array(
        'default' => array(
            'plugins' => array(
                'Core42\Authentication\Plugin\TableGateway' => array(
                    'name' => 'Core42\Authentication\Plugin\TableGateway',
                    'options' => array(
                        'table_gateway' => 'Admin42\User',
                        'identity_column' => 'username',
                        'credential_column' => 'password',
                    ),
                ),
            ),
            'adapter' => 'Core42\Authentication\Plugin\TableGateway',
            'storage' => 'Core42\Authentication\Plugin\TableGateway',
        ),
        'routes' => array(
            'admin' => 'default',
        ),
    ),

    'tablegateway' => array(
        'Admin42\UserTableGateway' => 'Admin42\TableGateway\UserTableGateway',
    ),
);
