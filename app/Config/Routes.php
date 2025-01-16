<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Route for LoginControllers
$routes->get('/', 'LoginControllers::index');
$routes->match(['get', 'post'], 'loginControllers/(:any)', 'LoginControllers::$1');

// Routes for HomeControllers
$routes->get('home', 'HomeControllers::index');

// Routes for Financeiro Controllers
$routes->get('financeiro/financeiroControllers', 'Financeiro\FinanceiroControllers::index');
$routes->match(['get', 'post'], 'financeiro/financeiroControllers/(:any)', 'Financeiro\FinanceiroControllers::$1');
$routes->match(['get', 'post'], 'financeiro/financeiroControllers/(:any)/(:num)', 'Financeiro\FinanceiroControllers::$1/$2');
$routes->match(['get', 'post'], 'financeiro/parcelasControllers/(:any)', 'Financeiro\ParcelasControllers::$1');
$routes->match(['get', 'post'], 'financeiro/parcelasControllers/(:any)/(:num)', 'Financeiro\ParcelasControllers::$1/$2');

// Routes for ConfigControllers
$routes->match(['get', 'post'], 'config/configControllers/(:any)', 'Config\ConfigControllers::$1');

// Routes for Pessoas Controllers
$routes->match(['get', 'post'],'pessoas/pessoasControllers', 'Pessoas\PessoasControllers::index');
$routes->match(['get', 'post'], 'pessoas/pessoasControllers/(:any)', 'Pessoas\PessoasControllers::$1');

// Routes for Juridico
$routes->match(['get', 'post'],'juridico/ndiControllers', 'Juridico\NDIControllers::index');
$routes->match(['get', 'post'],'juridico/ndiControllers/(:any)', 'Juridico\NDIControllers::$1');

// Routes for config
$routes->get('config/configControllers', 'Config\ConfigControllers::index');
$routes->match(['get', 'post'], 'config/configControllers/(:any)', 'Config\ConfigControllers::$1');
$routes->match(['get', 'post'], 'config/configControllers/(:any)/(:num)', 'Config\ConfigControllers::$1/$2');