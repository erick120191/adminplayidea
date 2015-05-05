<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

/*DEFAULT*/
$route['default_controller']       = 'login/index';
$route['404_override']             = 'error404';
$route['inicio']                   = 'inicio/index';
/********/

/*LOGIN*/
$route['login']                    = 'login/index';
$route['login/authentication']     = 'login/authentication';
$route['login/valindando']         = 'login/redireccion';
$route['logout']                   = 'login/logout';
/*******/

/*COMPRAS*/

/*Catalogo Presentaciones*/
$route['compras/catalogos/presentaciones']                               = 'compras/presentaciones/index';
$route['compras/catalogos/presentaciones/listado_presentaciones/(:num)'] = 'compras/presentaciones/listado_presentaciones/$1';
$route['compras/catalogos/presentaciones/listado_presentaciones']        = 'compras/presentaciones/listado_presentaciones';

/*Catalogo Lineas*/

$route['compras/catalogos/lineas']                       = 'compras/lineas/index';
$route['compras/catalogos/lineas/listado_lineas/(:num)'] = 'compras/lineas/listado_lineas/$1';
$route['compras/catalogos/lineas/listado_lineas']        = 'compras/lineas/listado_lineas';


/*Catalogo de U.M.*/
$route['compras/catalogos/um']            = 'compras/catalogo_um/um';
$route['compras/catalogos/um/(:num)']     = 'compras/catalogo_um/um/$1';
$route['compras/catalogos/agregar_um']    = 'compras/catalogo_um/agregar_um';
$route['compras/catalogos/detalle_um']    = 'compras/catalogo_um/detalle_um';
$route['compras/catalogos/actualizar_um'] = 'compras/catalogo_um/actualizar_um';

/*Catalogo de Marcas*/
$route['compras/catalogos/marcas']            = 'compras/catalogo_marcas/marcas';
$route['compras/catalogos/marcas/(:num)']     = 'compras/catalogo_marcas/marcas/$1';
$route['compras/catalogos/agregar_marcas']    = 'compras/catalogo_marcas/agregar_marcas';
$route['compras/catalogos/detalle_marcas']    = 'compras/catalogo_marcas/detalle_marcas';
$route['compras/catalogos/actualizar_marcas'] = 'compras/catalogo_marcas/actualizar_marcas';




/* End of file routes.php */
/* Location: ./application/config/routes.php */