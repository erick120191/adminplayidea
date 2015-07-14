<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ajustes extends Base_Controller { 
	/**
	* Nombre:		Ajustes
	* Ubicación:	Almacen>Ajustes
	* Descripción:	Funcionamiento para quitar cantidad de stock
	* @author:		Alejandro Enciso
	* Creación: 	2015-05-19
	*/
	private $modulo;
	private $submodulo;
	private $seccion;
	private $view_content, $uri_view_principal;
	private $path;
	private $icon;
	private $offset, $limit_max;
	private $tab_inicial, $tab = array(), $tab_indice = array();

	public function __construct(){
		parent::__construct();
		$this->vars = new config_vars();
        $this->vars->load_vars();
		$this->modulo 			= 'almacen';
		$this->submodulo        = 'ajustes';
		$this->seccion          = '';
		$this->icon 			= 'fa fa-wrench'; //Icono de modulo
		$this->path 			= $this->modulo.'/'.$this->submodulo.'/'; //almacen/entradas_recepcion/
		$this->view_content 	= 'content';
		$this->limit_max		= 10;
		$this->offset			= 0;
		// Tabs
		$this->tab1 			= 'agregar';
		$this->tab2 			= 'listado';
		$this->tab3 			= 'detalle';
		// DB Model
		$this->load->model($this->modulo.'/'.$this->submodulo.'_model','db_model');		
		$this->load->model($this->modulo.'/catalogos_model','catalogos_model');
		$this->load->model('stock_model','stock_model');

		// Diccionario
		//$this->lang->load($this->modulo.'/'.$this->submodulo,"es_ES");
		// Tabs
		$this->tab_inicial 		= 2;
		$this->tab_indice 		= array(
									 $this->tab1
									,$this->tab2
									,$this->tab3
								);
		for($i=0; $i<=count($this->tab_indice)-1; $i++){
			$this->tab[$this->tab_indice[$i]] = $this->tab_indice[$i];
		}
	}
	public function config_tabs(){
		$tab_1 	= $this->tab1;
		$tab_2 	= $this->tab2;
		$tab_3 	= $this->tab3;
		$path  	= $this->path;
		$pagina =(is_numeric($this->uri_segment_end()) ? $this->uri_segment_end() : "");
		// Nombre de Tabs
		$config_tab['names']    = array(
										 $this->lang_item($tab_1) //agregar
										,$this->lang_item($tab_2) //listado
										,$this->lang_item($tab_3) //detalle
								); 
		// Href de tabs
		$config_tab['links']    = array(
										 $path.$tab_1            //compras/listado_precios/agregar
										,$path.$tab_2.'/'.$pagina //compras/listado_precios/listado/pagina
										,$tab_3                   //detalle
								); 
		// Accion de tabs
		$config_tab['action']   = array(
										 'load_content'
										,'load_content'
										,''
								);
		// Atributos 
		$config_tab['attr']     = array('','', array('style' => 'display:none'));
		return $config_tab;
	}
	private function uri_view_principal(){
		return $this->modulo.'/'.$this->view_content; //compras/content
	}
	public function index(){
		$tabl_inicial 			  = $this->tab_inicial;
		$view_listado    		  = $this->listado();
		$contenidos_tab           = $view_listado;
		$data['titulo_submodulo'] = $this->lang_item($this->modulo);
		$data['titulo_seccion']   = $this->lang_item($this->submodulo);
		$data['icon']             = $this->icon;
		$data['tabs']             = tabbed_tpl($this->config_tabs(),base_url(),$tabl_inicial,$contenidos_tab);	
		//$data['modal']            = $this->modal();

		$js['js'][]  = array('name' => $this->submodulo, 'dirname' => $this->modulo);
		$js['js'][]  = array('name' => 'numeral', 'dirname' => '');
		$this->load_view($this->uri_view_principal(), $data, $js);
	}
	public function listado($offset=0){
		// Crea tabla con listado de ordenes aprobadas 
		$accion 		= $this->tab['listado'];
		$limit 			= $this->limit_max;
		$uri_view 		= $this->modulo.'/'.$accion;
		$url_link 		= $this->modulo.'/'.$this->submodulo.'/'.$accion;
		$buttonTPL 		= '';
		$filtro  = ($this->ajax_post('filtro')) ? $this->ajax_post('filtro') : "";
		$sqlData = array(
			 'buscar' => $filtro
			,'offset' => $offset
			,'limit'  => $limit
		);
		$uri_segment  			  = $this->uri_segment(); 
		$total_rows   			  = count($this->db_model->db_get_data($sqlData));
		$sqlData['aplicar_limit'] = false;
		$list_content 			  = $this->db_model->db_get_data($sqlData);
		$url          			  = base_url($url_link);
		$paginador    			  = $this->pagination_bootstrap->paginator_generate($total_rows, $url, $limit, $uri_segment, array('evento_link' => 'onclick', 'function_js' => 'load_content', 'params_js'=>'1'));
		if($total_rows){
			foreach ($list_content as $value) {
				// Evento de enlace
				// Acciones
				$accion_id 						= $value['id_articulo'];
				$btn_acciones['agregar'] 		= '<span id="ico-articulos_'.$accion_id.'" class="ico_detalle fa fa-search-plus" onclick="detalle('.$accion_id.')" title="'.$this->lang_item("agregar_articulos").'"></span>';
				$acciones = implode('&nbsp;&nbsp;&nbsp;',$btn_acciones);

				$peso_unitario = (substr($value['peso_unitario'], strpos($value['peso_unitario'], "." ))=='.000')?number_format($value['peso_unitario'],0):$value['peso_unitario'];
				$presentacion_x_embalaje = (substr($value['presentacion_x_embalaje'], strpos($value['presentacion_x_embalaje'], "." ))=='.000')?number_format($value['presentacion_x_embalaje'],0):$value['presentacion_x_embalaje'];
				$embalaje = ($value['embalaje'])?$value['embalaje'].' CON ':'';
				$stock = (substr($value['stock'], strpos($value['stock'], "." ))=='.000' && $value['articulo_tipo']!=strtoupper('INSUMO'))?number_format($value['stock'],0).' '.$this->lang_item("pieza_abrev"):$value['stock'].' '.$value['unidad_minima_cve'];
				// Datos para tabla
				$tbl_data[] = array('id'             	=> $value['id_stock'],
									'articulo'  	 	=> $value['articulo'].' - '.$peso_unitario.' '.$value['cl_um'],
									'presentacion'   	=> $embalaje.$presentacion_x_embalaje.' '.$value['presentacion'],
									//'stock'      		=> $stock,
									'articulo_tipo'   	=> $value['articulo_tipo'],
									//'fecha_recepcion'   => $value['fecha_recepcion'],
									'almacenes'   	 	=> $value['almacenes'],
									'gavetas'   	 	=> $value['gavetas'],
									'acciones' 		 	=> $acciones
									);
			}
			// Plantilla
			$tbl_plantilla = array ('table_open'  => '<table id="tbl_grid" class="table table-bordered responsive ">');
			// Titulos de tabla
			$this->table->set_heading(	$this->lang_item("id_stock"),
										$this->lang_item("articulo"),										
										$this->lang_item("presentacion"),
										//$this->lang_item("stock"),
										$this->lang_item("articulo_tipo"),
										//$this->lang_item("fecha_recepcion"),
										$this->lang_item("almacen"),
										$this->lang_item("gaveta"),
										$this->lang_item("acciones")
									);
			// Generar tabla
			$this->table->set_template($tbl_plantilla);
			$tabla = $this->table->generate($tbl_data);
			// XLS
			$buttonTPL = array( 'text'   => $this->lang_item("btn_xlsx"), 
							'iconsweets' => 'iconsweets-excel',
							'href'       => base_url($this->modulo.'/'.$this->submodulo).'/export_xlsx?filtro='.base64_encode($filtro)
							);
		}else{
			$msg   = $this->lang_item("msg_query_null");
			$tabla = alertas_tpl('', $msg ,false);
		}
		$tabData['filtro']    = (isset($filtro) && $filtro!="") ? sprintf($this->lang_item("msg_query_search",false),$total_rows , $filtro) : "";
		$tabData['tabla']     = $tabla;
		$tabData['paginador'] = $paginador;
		$tabData['item_info'] = $this->pagination_bootstrap->showing_items($limit, $offset, $total_rows);
		$tabData['export']    = button_tpl($buttonTPL);

		if($this->ajax_post(false)){
			echo json_encode( $this->load_view_unique($uri_view , $tabData, true));
		}else{
			return $this->load_view_unique($uri_view , $tabData, true);
		}
	}
	public function agregar(){
		$view = $this->tab['agregar'];
		$dropArray = array(
				 'data'		=> $this->catalogos_model->db_get_data_almacen()
				,'value' 	=> 'id_almacen_almacenes'
				,'text' 	=> array('clave_corta','almacenes')
				,'name' 	=> "lts_almacen"
				,'event'    => array('event'       => 'onchange',
			   						 'function'    => 'load_gaveta_pas',
			   						 'params'      => array('this.value'),
			   						 'params_type' => array(0)
								)
				,'class' 	=> "requerido"
			);
		$lts_almacen  = dropdown_tpl($dropArray);
		$tabData['lts_almacen'] = $lts_almacen;
		$uri_view  = $this->modulo.'/'.$this->submodulo.'/'.$this->seccion.'/'.$view;
		if($this->ajax_post(false)){
				echo json_encode( $this->load_view_unique($uri_view ,$tabData, true));
		}else{
			echo json_encode( $this->load_view_unique($uri_view ,$tabData, true,$includes));
		}
	}
	public function load_stock(){
		$id_articulo    =  $this->ajax_post('id_articulo');
		$id_almacen    =  ($this->ajax_post('id_almacen')!=0)?$this->ajax_post('id_almacen'):'';
		$id_pasillo    =  ($this->ajax_post('id_pasillo')!=0)?$this->ajax_post('id_pasillo'):'';
		$id_gavetas    =  ($this->ajax_post('id_gavetas')!=0)?$this->ajax_post('id_gavetas'):'';

		$slqdata=array(
					'id_articulo'=> $id_articulo,
					'id_almacen' => $id_almacen,
					'id_pasillo' => $id_pasillo, 
					'id_gavetas' => $id_gavetas);

		$detalle  = $this->db_model->db_get_data_x_articulo($slqdata);
		$stock=0;
		$stock_um=0;
		for($i=0; count($detalle)>$i;$i++){
			$stock+=$detalle[$i]['stock'];
			$stock_um+=$detalle[$i]['stock_um'];
		}
		$um= $detalle[0]['unidad_minima_cve'];
		$data=array('stock'=> $stock,'stock_um'=>$stock_um, 'u_m_cv'=>$um);
		echo json_encode($data);
	}
	public function load_gaveta_pas(){
		$id_almacen    = $this->ajax_post('id_almacen');
		$datasql=array('id_almacen'=>$id_almacen);
			$dropArray = array(
					 'data'		=> $this->catalogos_model->db_get_data_pasillos_por_almacen($datasql)
					,'value' 	=> 'id_almacen_pasillos'
					,'text' 	=> array('clave_corta','pasillos')
					,'name' 	=> "lts_pasillos"
					,'event'    => array('event'       => 'onchange',
				   						 'function'    => 'load_gaveta',
				   						 'params'      => array('this.value'),
				   						 'params_type' => array(0)
									)
				);
			$dropArray2 = array(
					 'data'		=> $this->catalogos_model->db_get_data_gavetas_por_almacen($datasql)
					,'value' 	=> 'id_almacen_gavetas'
					,'text' 	=> array('clave_corta','gavetas')
					,'name' 	=> "lts_gavetas"
					,'class' 	=> "requerido"
					,'event'    => array('event'       => 'onchange',
				   						 'function'    => 'load_articulos',
				   						 'params'      => array('this.value'),
				   						 'params_type' => array(0)
   									)
				);
		//
			$datasql=array(
					'id_almacen' => $id_almacen,
					'id_pasillo' => '',
					'id_gaveta' => '');
		$dropArray3 = array(
				 'data'		=> $this->db_model->db_get_data_articulos($datasql)
				,'value' 	=> 'id_articulo'
				,'text' 	=> array('cl_um','articulo')
				,'name' 	=> "lts_ajustes"
				,'class' 	=> "requerido"
				,'event'    => array('event'       => 'onchange',
				   						 'function'    => 'load_stock',
				   						 'params'      => array('this.value'),
				   						 'params_type' => array(0)
   									)
			);
		$lts_pasillo  = dropdown_tpl($dropArray);
		$lts_gavetas  = dropdown_tpl($dropArray2);
		$lts_ajustes  = dropdown_tpl($dropArray3);


		$data['pasillos']=$lts_pasillo;
		$data['gavetas']=$lts_gavetas;
		$data['lts_ajustes']=$lts_ajustes;
		echo json_encode($data);
	}
	public function load_gaveta(){
		//$id_pasillo    = $this->ajax_post('id_pasillo');
		//$id_almacen    = $this->ajax_post('id_almacen');
		$id_almacen    =  ($this->ajax_post('id_almacen')!=0)?$this->ajax_post('id_almacen'):'';
		$id_pasillo    =  ($this->ajax_post('id_pasillo')!=0)?$this->ajax_post('id_pasillo'):'';
		$id_gaveta    =  ($this->ajax_post('id_gaveta')!=0)?$this->ajax_post('id_gaveta'):'';
		if($id_pasillo==0){
			$datasql=array('id_almacen'=>$id_almacen);
			$dropArray = array(
						 'data'		=> $this->catalogos_model->db_get_data_gavetas_por_almacen($datasql)
						,'value' 	=> 'id_almacen_gavetas'
						,'text' 	=> array('clave_corta','gavetas')
						,'name' 	=> "lts_gavetas"
						,'class' 	=> "requerido"
						,'event'    => array('event'       => 'onchange',
				   						 'function'    => 'load_articulos',
				   						 'params'      => array('this.value'),
				   						 'params_type' => array(0)
   									)
					);
			$lts_gavetas  = dropdown_tpl($dropArray);
		}else{
			$datasql=array('id_pasillo'=>$id_pasillo);
			$dropArray = array(
						 'data'		=> $this->catalogos_model->db_get_data_gavetas_por_pasillo($datasql)
						,'value' 	=> 'id_almacen_gavetas'
						,'text' 	=> array('clave_corta','gavetas')
						,'name' 	=> "lts_gavetas"
						,'class' 	=> "requerido"
						,'event'    => array('event'       => 'onchange',
				   						 'function'    => 'load_articulos',
				   						 'params'      => array('this.value'),
				   						 'params_type' => array(0)
   									)
					);
			$lts_gavetas  = dropdown_tpl($dropArray);
		}
		$datasql=array(
					'id_almacen' => $id_almacen,
					'id_pasillo' => $id_pasillo,
					'id_gaveta' => '');
		$dropArray3 = array(
				 'data'		=> $this->db_model->db_get_data_articulos($datasql)
				,'value' 	=> 'id_articulo'
				,'text' 	=> array('cl_um','articulo')
				,'name' 	=> "lts_ajustes"
				,'class' 	=> "requerido"
				,'event'    => array('event'       => 'onchange',
				   						 'function'    => 'load_stock',
				   						 'params'      => array('this.value'),
				   						 'params_type' => array(0)
   									)
			);
		$lts_ajustes  = dropdown_tpl($dropArray3);
		$data['lts_gavetas']=$lts_gavetas;
		$data['lts_ajustes']=$lts_ajustes;
		echo json_encode($data);
	}
	public function load_articulos(){
		$id_almacen    =  ($this->ajax_post('id_almacen')!=0)?$this->ajax_post('id_almacen'):'';
		$id_pasillo    =  ($this->ajax_post('id_pasillo')!=0)?$this->ajax_post('id_pasillo'):'';
		$id_gaveta    =  ($this->ajax_post('id_gaveta')!=0)?$this->ajax_post('id_gaveta'):'';

		$datasql=array(
					'id_almacen' => $id_almacen,
					'id_pasillo' => $id_pasillo,
					'id_gaveta' => $id_gaveta);

		$dropArray3 = array(
				 'data'		=> $this->db_model->db_get_data_articulos($datasql)
				,'value' 	=> 'id_articulo'
				,'text' 	=> array('cl_um','articulo')
				,'name' 	=> "lts_ajustes"
				,'class' 	=> "requerido"
				,'event'    => array('event'       => 'onchange',
				   						 'function'    => 'load_stock',
				   						 'params'      => array('this.value'),
				   						 'params_type' => array(0)
   									)
			);
		$lts_ajustes  = dropdown_tpl($dropArray3);
		echo json_encode($lts_ajustes);
	}
	public function detalle(){	
		$id_articulo    =  $this->ajax_post('id_articulo');
		$view 			= $this->tab['detalle'];
		$detalle  		= $this->db_model->db_get_data_x_articulo($id_articulo);
		$btn_save       = form_button(array('class'=>"btn btn-primary",'name' => 'save' , 'onclick'=>'save()','content' => $this->lang_item("btn_guardar") ));
		$stock=0;
		$stock_um=0;
		for($i=0; count($detalle)>$i;$i++){
			$stock+=$detalle[$i]['stock'];
			$stock_um+=$detalle[$i]['stock_um'];
		}
		//echo $stock;
		//die();
		$dropArray = array(
				 'data'		=> $this->catalogos_model->db_get_data_almacen()
				,'value' 	=> 'id_almacen_almacenes'
				,'text' 	=> array('clave_corta','almacenes')
				,'name' 	=> "lts_almacen"
				,'event'    => array('event'       => 'onchange',
			   						 'function'    => 'load_gaveta_pas',
			   						 'params'      => array('this.value'),
			   						 'params_type' => array(0)
								)
				,'class' 	=> "requerido"
			);
		$lts_almacen  = dropdown_tpl($dropArray);
		//DATA
		/*$tabData['id_compras_orden_articulo'] = $detalle[0]['id_compras_orden_articulo'];
		$tabData['id_stock']	     = $detalle[0]['id_stock'];
		$tabData['upc']	 		     = $detalle[0]['upc'];
		$tabData['sku']	 		 	 = $detalle[0]['sku'];
		$tabData['marca']	 	     = $detalle[0]['marca'];
		$tabData['presentacion']	 = $detalle[0]['presentacion'];
		$tabData['lote']			 = $detalle[0]['lote'];
		$tabData['stock']	 	 	 = $detalle[0]['stock'];
		$tabData['stock_um'] 	 	 = $detalle[0]['stock_um'];
		$tabData['unidad_minima_cve']= $detalle[0]['unidad_minima_cve'];		
		$tabData['caducidad']	     = $detalle[0]['caducidad'];
		$tabData['almacenes']	     = $detalle[0]['almacenes'];
		$tabData['pasillos']	     = $detalle[0]['pasillos'];
		$tabData['gavetas']	     	 = $detalle[0]['gavetas'];
		$tabData['id_almacen_origen']= $detalle[0]['id_almacen'];
		$tabData['id_pasillo_origen']= $detalle[0]['id_pasillo'];
		$tabData['id_gaveta_origen'] = $detalle[0]['id_gaveta'];
		$tabData['id_almacen_entradas_recepcion'] = $detalle[0]['id_almacen_entradas_recepcion'];
		$tabData['id_articulo_tipo'] = $detalle[0]['id_articulo_tipo'];
		
		$tabData['button_save']      = $btn_save;*/

		$tabData['stock']	 	 	 = $stock;
		$tabData['stock_um'] 	 	 = $stock_um;
		$tabData['unidad_minima_cve']= $detalle[0]['unidad_minima_cve'];
		$tabData['lts_almacen']	     = $lts_almacen;

		$tabData['upc_lbl']			 = $this->lang_item("upc_lbl",false);
		$tabData['sku_lbl']			 = $this->lang_item("sku_lbl",false);
		$tabData['marca_lbl']		 = $this->lang_item("marca_lbl",false);
		$tabData['presentacion_lbl'] = $this->lang_item("presentacion_lbl",false);
		$tabData['lote_lbl']		 = $this->lang_item("lote_lbl",false);
		$tabData['stock_lbl']		 = $this->lang_item("stock_lbl",false);
		$tabData['stock_um_lbl']	 = $this->lang_item("stock_um_lbl",false);
		$tabData['caducidad_lbl']	 = $this->lang_item("caducidad_lbl",false);
		$tabData['almacen_lbl']		 = $this->lang_item("almacen_lbl",false);
		$tabData['pasillo_lbl']		 = $this->lang_item("pasillo_lbl",false);
		$tabData['gaveta_lbl']		 = $this->lang_item("gaveta_lbl",false);
		$tabData['almacen_origen_lbl']		 = $this->lang_item("almacen_origen_lbl",false);
		$tabData['pasillo_origen_lbl']		 = $this->lang_item("pasillo_origen_lbl",false);
		$tabData['gaveta_origen_lbl']		 = $this->lang_item("gaveta_origen_lbl",false);
		$tabData['origen']		 = $this->lang_item("origen",false);
		$tabData['destino']		 = $this->lang_item("destino",false);
		

		
		$uri_view  = $this->modulo.'/'.$this->submodulo.'/'.$this->seccion.'/'.$view;

		echo json_encode( $this->load_view_unique($uri_view ,$tabData, true));
	}
}
?>