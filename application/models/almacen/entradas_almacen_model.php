<?php
class entradas_almacen_model extends Base_Model{
	public function db_get_data($data=array()){	
		// DB Info
		$tbl = $this->tbl;
		// Filtro
		$filtro = (isset($data['buscar']))?$data['buscar']:false;
		$limit 			= (isset($data['limit']))?$data['limit']:0;
		$offset 		= (isset($data['offset']))?$data['offset']:0;
		$aplicar_limit 	= (isset($data['aplicar_limit']))?true:false;

		$filtro = ($filtro!="") ? "and (f.almacenes LIKE '%$filtro%' 
									   or d.articulo LIKE '%$filtro%'
									   or e.presentacion LIKE '%$filtro%'
									   or g.gavetas LIKE '%$filtro%'
							   		)" : "";
		$limit 			= ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		// Query
		$query="SELECT 
					a.id_stock,
					a.id_almacen,
					a.id_pasillo,
					a.id_gaveta,
					a.id_almacen_entradas_recibir,
					a.id_compras_orden_articulo,
					a.id_articulo_tipo,
					a.stock,
					a.timestamp as fecha_recepcion,
					c.id_articulo,
					d.articulo,
					e.presentacion,
					f.almacenes,
					g.gavetas
				from $tbl[almacen_stock] a 
				LEFT JOIN $tbl[compras_ordenes_articulos] b on a.id_compras_orden_articulo=b.id_compras_orden_articulo
				LEFT JOIN $tbl[compras_articulos_precios] c on b.id_compras_articulo_precios=c.id_compras_articulo_precios
				LEFT JOIN $tbl[compras_articulos] d on c.id_articulo=d.id_compras_articulo
				LEFT JOIN $tbl[compras_presentaciones] e on c.id_presentacion=e.id_compras_presentacion
				LEFT JOIN $tbl[almacen_almacenes] f on a.id_almacen=f.id_almacen_almacenes
				LEFT JOIN $tbl[almacen_gavetas] g on a.id_gaveta=g.id_almacen_gavetas
			WHERE a.id_almacen = 1 $filtro
			$limit";
		/*
	FALTAN LOS FILTROS Y LA BUSQUEDA*/
	  	// Execute querie

	  	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	public function get_data_unico($id_compras_orden_articulo){
		$tbl = $this->tbl;
		// Query
		$query = "SELECT 
					a.id_compras_orden_articulo, 
					a.id_compras_orden, 
					a.id_compras_articulo_precios, 
					b.upc, 
					b.sku,
					b.id_articulo,
					b.id_marca,
					c.marca,
					d.presentacion,
					e.lote,
					e.stock,
					e.id_stock,
					e.caducidad
				FROM 
					$tbl[compras_ordenes_articulos] a
				LEFT JOIN $tbl[compras_articulos_precios] b ON a.id_compras_articulo_precios=b.id_compras_articulo_precios
				LEFT JOIN $tbl[compras_marcas] c ON b.id_marca=c.id_compras_marca
				LEFT JOIN $tbl[compras_presentaciones] d ON b.id_presentacion=d.id_compras_presentacion
				LEFT JOIN $tbl[almacen_stock] e ON a.id_compras_orden_articulo=e.id_compras_orden_articulo
				WHERE a.id_compras_orden_articulo = $id_compras_orden_articulo";
				//echo $query;
		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	public function db_update_alma_gav_pas($data=array()){
		// DB Info
		$tbl = $this->tbl;
		// Query
		$id_stock = (isset($data['id_stock']))?$data['id_stock']:false;
		$condicion = ($id_stock)?"id_stock='$id_stock'":'';
		$update    = $this->update_item($tbl['almacen_stock'], $data, 'id_stock', $condicion);
		return $update;
	}
}
?>