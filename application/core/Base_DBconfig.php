<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Base_DBconfig extends Base_Controller {
/**
* Descripción: 	Establece el nombre de las bases de datos y de sus tablas para ser usadas por los modelos
* @author: 		Oscar Maldonado - O3M
* Creación: 	2015-05-08
* Modificación:	
*/
	public $dbdata = array();

	 function __construct(){

		$this->dbdata = array(
					 0 => array(
					 	// Base de datos
					 	 'db'				=>	'00_av_system'
					 	 // Tablas
					 	,'tbl_claves'		=>	'sys_claves'
					 	,'tbl_empresas'		=>	'sys_empresas'
					 	,'tbl_modulos'		=>	'sys_modulos'
					 	,'tbl_paises'		=>	'sys_paises'
					 	,'tbl_perfiles'		=>	'sys_perfiles'
					 	,'tbl_personales'	=>	'sys_personales'
					 	,'tbl_secciones'	=>	'sys_secciones'
					 	,'tbl_submodulos'	=>	'sys_submodulos'
					 	,'tbl_sucursales'	=>	'sys_sucursales'
					 	,'tbl_usuarios'		=>	'sys_usuarios'
					 	// Vistas
					 	,'vw_personal'		=>	'vw_personal'
					 	// ,'[tbl_alias]'	=>	'[tabla]'
					 	)
					,1 => array(
						// Base de datos
					 	 'db'								=>	'00_av_mx'
					 	 // Tablas
					 	,'tbl_administracion_entidades'		=>	'av_administracion_entidades'
					 	,'tbl_almacen_almacenes' 			=>	'av_almacen_almacenes'
					 	,'tbl_almacen_gavetas' 			    =>	'av_almacen_gavetas'
					 	,'tbl_compras_articulos'			=>	'av_compras_articulos'
					 	,'tbl_compras_lineas'				=>	'av_compras_lineas'
					 	,'tbl_compras_marcas'				=>	'av_compras_marcas'
					 	,'tbl_compras_ordenes'				=>	'av_compras_ordenes'
					 	,'tbl_compras_ordenes_articulos'	=>	'av_compras_ordenes_articulos'
					 	,'tbl_compras_presentaciones'		=>	'av_compras_presentaciones'
					 	,'tbl_compras_proveedores'			=>	'av_compras_proveedores'
					 	,'tbl_compras_proveedores_articulos'=>	'av_compras_proveedores_articulos'
					 	,'tbl_compras_um'					=>	'av_compras_um'
					 	,'tbl_compras_clientes'				=>	'av_ventas_clientes'
					 	,'tbl_sys_sucursales'				=>	'sys_sucursales'
					 	// Vistas
					 	,'vw_orden_articulos'				=>	'vw_ordenes_con_articulos'
					 	,'vw_articulos'						=>	'vw_articulos'
					 	,'vw_proveedores_articulos'			=>	'vw_proveedores_articulos'
					 	// ,'[tbl_alias]'	=>	'[tabla]'
					 	)
		);			
	}

	public function db_config(){
		return $this->dbdata;
	}
}
?>