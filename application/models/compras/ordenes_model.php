<?php
class ordenes_model extends Base_Model{

	private $vars;
	private $db1,$db2;
	private $tbl;

	public function __construct(){
		parent::__construct();		
		$this->vars		= new config_vars();
        $this->vars->load_vars('assets/cfg/dbmodel.cfg');
		$this->db1 = $this->vars->db['db1'];
		$this->tbl['sucursales'] = $this->db1.'.'.$this->vars->db['db1_tbl_sucursales'];
        $this->db2 = $this->vars->db['db2'];	
        $this->tbl['administracion_entidades'] = $this->db2.'.'.$this->vars->db['db2_tbl_administracion_entidades'];	
		$this->tbl['compras_articulos'] = $this->db2.'.'.$this->vars->db['db2_tbl_compras_articulos'];
		$this->tbl['compras_articulos_precios'] = $this->db2.'.'.$this->vars->db['db2_tbl_compras_articulos_precios'];
		$this->tbl['compras_lineas'] = $this->db2.'.'.$this->vars->db['db2_tbl_compras_lineas'];
		$this->tbl['compras_marcas'] = $this->db2.'.'.$this->vars->db['db2_tbl_compras_marcas'];
		$this->tbl['compras_ordenes_tipo'] = $this->db2.'.'.$this->vars->db['db2_tbl_compras_ordenes_tipo'];
		$this->tbl['compras_ordenes'] = $this->db2.'.'.$this->vars->db['db2_tbl_compras_ordenes'];
		$this->tbl['compras_ordenes_articulos'] = $this->db2.'.'.$this->vars->db['db2_tbl_compras_ordenes_articulos'];
		$this->tbl['compras_ordenes_estatus'] = $this->db2.'.'.$this->vars->db['db2_tbl_compras_ordenes_estatus'];
		$this->tbl['compras_presentaciones'] = $this->db2.'.'.$this->vars->db['db2_tbl_compras_presentaciones'];
		$this->tbl['compras_proveedores'] = $this->db2.'.'.$this->vars->db['db2_tbl_compras_proveedores'];
		$this->tbl['compras_proveedores_articulos'] = $this->db2.'.'.$this->vars->db['db2_tbl_compras_proveedores_articulos'];
		$this->tbl['compras_um'] = $this->db2.'.'.$this->vars->db['db2_tbl_compras_um'];
		$this->tbl['compras_embalaje'] = $this->db2.'.'.$this->vars->db['db2_tbl_compras_embalaje'];
		$this->tbl['vw_compras_orden_proveedores'] = $this->db2.'.'.$this->vars->db['db2_vw_compras_orden_proveedores'];
		$this->tbl['vw_articulos'] = $this->db2.'.'.$this->vars->db['db2_vw_articulos'];
		$this->tbl['vw_proveedores_articulos'] = $this->db2.'.'.$this->vars->db['db2_vw_proveedores_articulos'];
	}

	public function insert($data=array()){
		// DB Info
		$tbl = $this->tbl;
		// Query
		$existe = $this->row_exist($tbl['compras_ordenes'],array('orden_num ='=> $data['orden_num']));
		if(!$existe){
			$insert = $this->insert_item($tbl['compras_ordenes'], $data);
			return $insert;
		}else{
			return false;
		}
	}
	public function db_update_data($data=array()){
		// DB Info
		$tbl = $this->tbl;
		// Query
		$resultado = false;
		$id_compras_orden   = (isset($data['id_compras_orden']))?$data['id_compras_orden']:false;
		$filtro 			= ($id_compras_orden)?"id_compras_orden='$id_compras_orden'":'';
		if($id_compras_orden){
			$update    = $this->update_item($tbl['compras_ordenes'], $data, 'id_compras_orden', $filtro);
			return $update;
		}
		return $resultado;
	}
	public function db_get_data($data=array()){	
		// DB Info
		$tbl = $this->tbl;
		// Filtro
		$filtro = (isset($data['buscar']))?$data['buscar']:false;
		$limit 			= (isset($data['limit']))?$data['limit']:0;
		$offset 		= (isset($data['offset']))?$data['offset']:0;
		$aplicar_limit 	= (isset($data['aplicar_limit']))?true:false;

		$filtro = ($filtro!="") ? "and (orden_num LIKE '$filtro%' 
							   or razon_social LIKE '$filtro%'
							   or descripcion LIKE '$filtro%'
							   )" 
							: "";
		$limit 			= ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		// Query
		$query="SELECT 
					a.id_compras_orden AS id_compras_orden
					,a.orden_num
					,a.entrega_fecha
					,a.descripcion AS descripcion,
					a.timestamp
					,h.razon_social
					,e.estatus
				from $tbl[compras_ordenes] a 
				LEFT JOIN $tbl[compras_proveedores] h on a.id_proveedor=h.id_compras_proveedor
				LEFT JOIN $tbl[compras_ordenes_estatus] e on a.estatus=e.id_estatus
				WHERE a.estatus = 1 AND 1  $filtro
				GROUP BY orden_num ASC
				$limit";
      	// Execute querie
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}
	public function get_orden_unico($id_compras_orden){
		// DB Info
		$tbl = $this->tbl;
		// Query
		//$query = "SELECT * FROM $tbl[compras_ordenes] WHERE id_compras_orden = $id_compras_orden";
		$query="SELECT *
				from $tbl[compras_ordenes] a 
				WHERE id_compras_orden = $id_compras_orden;";

		$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}
	}
	public function db_get_proveedores($data=array()){
		// DB Info
		$tbl = $this->tbl;
		// Filtro
		$buscar = (isset($data['buscar']))?$data['buscar']:false;
		$filtro = ($buscar) ? "" : "";
		// Limit
		$limit 			= (isset($data['limit']))?$data['limit']:0;
		$offset 		= (isset($data['offset']))?$data['offset']:0;
		$aplicar_limit 	= (isset($data['aplicar_limit']))?true:false;
		$limit 			= ($aplicar_limit) ? "LIMIT $offset ,$limit" : "";
		// Query
		$query = "	SELECT 
						 id_compras_proveedor
						,razon_social
						,nombre_comercial
						,clave_corta
					FROM $tbl[compras_proveedores]
					WHERE 1 $filtro
					GROUP BY clave_corta ASC
					$limit
					";
		// dump_var($query);
      	// Execute querie
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}
	public function db_get_total_rows($data=array()){
		// DB Info
		$tbl = $this->tbl;
		// Filtro
		$buscar = (isset($data['buscar']))?$data['buscar']:false;
		$filtro = ($buscar) ? "and (orden_num LIKE '$buscar%' 
							   or razon_social LIKE '$buscar%'
							   or descripcion LIKE '$buscar%'
							   )" 
							: "";
		// Query
		$query = "	SELECT count(*)
					FROM $tbl[vw_compras_orden_proveedores]
					WHERE 1 $filtro
					GROUP BY orden_num ASC
					";
		 dump_var($query);
      	// Execute querie
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}
	public function db_get_tipo_orden(){
		// DB Info
		$tbl = $this->tbl;
		// Query
		$query = "SELECT * FROM $tbl[compras_ordenes_tipo] WHERE activo= 1";
      	// Execute querie
      	$query = $this->db->query($query);
		if($query->num_rows >= 1){
			return $query->result_array();
		}	
	}
}
?>