<?php
/**
 * list routes
 */

$this->router->add('login', "POST", '/login', 'AuthController:login');
$this->router->add('registration', "POST", '/reg', 'AuthController:registration');
$this->router->add('logout', 'get', '/logout', 'AuthController:logout');

$this->router->add('showOrders', 'get', '/orders', 'OrderController:index', 'auth');
$this->router->add('addOrders', 'post', '/orders/add', 'OrderController:store', 'auth');
$this->router->add('updateOrders', 'patch', '/orders/{id:int}', 'OrderController:update', 'auth');
$this->router->add('deleteOrders', 'delete', '/orders/{id:int}', 'OrderController:delete', 'auth');
$this->router->add('setAllOrders', 'post', '/orders/set', 'OrderController:set', 'auth');
$this->router->add('setOneOrder', 'get', '/orders/setOne/{id:int}', 'OrderController:setOne', 'auth');

