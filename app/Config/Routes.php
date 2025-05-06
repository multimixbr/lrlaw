<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Route for LoginControllers
$routes->match(['get', 'post'], '/', 'LoginControllers::index');
$routes->match(['get', 'post'], 'loginControllers/(:any)', 'LoginControllers::$1');

// Routes for HomeControllers
$routes->match(['get', 'post'], 'home', 'HomeControllers::index');

// Routes for Financeiro Controllers
$routes->match(['get', 'post'], 'financeiro/financeiroControllers', 'Financeiro\FinanceiroControllers::index');

$routes->match(['get', 'post'], 'financeiro/financeiroControllers/(:any)', 'Financeiro\FinanceiroControllers::$1');
$routes->match(['get', 'post'], 'financeiro/financeiroControllers/(:any)/(:num)', 'Financeiro\FinanceiroControllers::$1/$2');

$routes->match(['get', 'post'], 'financeiro/parcelasControllers/(:any)', 'Financeiro\ParcelasControllers::$1');
$routes->match(['get', 'post'], 'financeiro/parcelasControllers/(:any)/(:num)', 'Financeiro\ParcelasControllers::$1/$2');

$routes->match(['get', 'post'], 'financeiro/controleControllers', 'Financeiro\ControleControllers::index');
$routes->match(['get', 'post'], 'financeiro/controleControllers/(:any)', 'Financeiro\ControleControllers::$1');
$routes->match(['get', 'post'], 'financeiro/controleControllers/(:any)/(:num)', 'Financeiro\ControleControllers::$1/$2');

$routes->match(['get', 'post'], 'financeiro/rotinaFinanceiraControllers', 'Financeiro\RotinaFinanceiraControllers::index');
$routes->match(['get', 'post'], 'financeiro/rotinaFinanceiraControllers/(:any)', 'Financeiro\RotinaFinanceiraControllers::$1');
$routes->match(['get', 'post'], 'financeiro/rotinaFinanceiraControllers/(:any)/(:num)', 'Financeiro\RotinaFinanceiraControllers::$1/$2');

// Routes for Pessoas Controllers
$routes->match(['get', 'post'], 'pessoas/pessoasControllers', 'Pessoas\PessoasControllers::index');
$routes->match(['get', 'post'], 'pessoas/pessoasControllers/(:any)', 'Pessoas\PessoasControllers::$1');
$routes->match(['get', 'post'], 'pessoas/pessoasControllers/(:any)/(:num)', 'Pessoas\PessoasControllers::$1/$2');

// Routes for Juridico
$routes->match(['get', 'post'],'juridico/ndiControllers', 'Juridico\NDIControllers::index');
$routes->match(['get', 'post'],'juridico/ndiControllers/(:any)', 'Juridico\NDIControllers::$1');

// Routes for config
$routes->match(['get', 'post'], 'config/configControllers', 'Config\ConfigControllers::index');
$routes->match(['get', 'post'], 'config/configControllers/(:any)', 'Config\ConfigControllers::$1');
$routes->match(['get', 'post'], 'config/configControllers/(:any)/(:num)', 'Config\ConfigControllers::$1/$2');

$routes->match(['get', 'post'], 'admin/permissaoControllers', 'Admin\PermissaoControllers::index');
$routes->match(['get', 'post'], 'admin/permissaoControllers/(:any)', 'Admin\PermissaoControllers::$1');
$routes->match(['get', 'post'], 'admin/permissaoControllers/(:any)/(:num)', 'Admin\PermissaoControllers::$1/$2');

$routes->match(['get', 'post'], 'admin/usuariosControllers', 'Admin\UsuariosControllers::index');
$routes->match(['get', 'post'], 'admin/usuariosControllers/(:any)', 'Admin\UsuariosControllers::$1');
$routes->match(['get', 'post'], 'admin/usuariosControllers/(:any)/(:num)', 'Admin\UsuariosControllers::$1/$2');