<?php
    if($ajaxRequest){
        require_once "../Models/proveedorModels.php";
    }else{
        require_once "./Models/proveedorModels.php";
    }
 
    class proveedorControllers extends proveedorModels{

        public function add_provee_controller(){
            $nombre=mainModel::clean_chain($_POST['nombre']);
            $tipo_documento=mainModel::clean_chain($_POST['tipo_documento']);
            $num_documento=mainModel::clean_chain($_POST['num_documento']);
            $direccion=mainModel::clean_chain($_POST['direccion']);
            $celular=mainModel::clean_chain($_POST['celular']);
            $correo=mainModel::clean_chain($_POST['correo']);
 
            $query1=mainModel::run_simple_query("SELECT proved_nombre FROM proveedor WHERE proved_nombre='$nombre'");
            if ($query1->rowCount()>=1) {
                $alert=[
                    "Alert"=>"simple",
                    "title"=>"Ocurrió un error inesperado",
                    "text"=>"¡Error! Nombre de proveedor ya existente",
                    "icon"=>"error"
                ];
            }else{
                $query2=mainModel::run_simple_query("SELECT proved_numDocumento FROM proveedor WHERE proved_numDocumento='$num_documento'");
                if($query2->rowCount()==1){
                    $alert=[
                        "Alert"=>"simple",
                        "title"=>"Ocurrió un error inesperado",
                        "text"=>"¡Error! Número de documento ya existente",
                        "icon"=>"error"
                    ];
                }else{
                    $query3=mainModel::run_simple_query("SELECT proved_id FROM proveedor");
               
                    $number=($query3->rowCount())+1;
                    $code_provider=mainModel::random_code("PROV-",6,$number);
                    $dataProvider=[
                        "Codigo"=>$code_provider,
                        "Nombre"=>$nombre,
                        "Tipo"=>$tipo_documento,
                        "Numero"=>$num_documento,
                        "Celular"=>$celular,
                        "Correo"=>$correo,
                        "Direccion"=>$direccion
                    ];
                    $saveProvider=proveedorModels::add_provee_model($dataProvider);
                        if ($saveProvider->rowCount()>=1){
                                $alert=[
                                    "Alert"=>"clean",
                                    "title"=>"Operacion exitosa",
                                    "text"=>"Proveedor registrado exitosamente",
                                    "icon"=>"success"
                                ];
                        } else {
                                $alert=[
                                    "Alert"=>"simple",
                                    "title"=>"Ocurrió un error inesperado",
                                    "text"=>"¡Error! No hemos podido registrar proveeor, en este momento",
                                    "icon"=>"error"
                                ];
                        }
                }
           
            }
            return mainModel::sweet_alert($alert);
        }
        public function update_provee_controller(){

            $proved_id=mainModel::clean_chain($_POST['proved_id']);

            $nombre=mainModel::clean_chain($_POST['nombre']);
            $tipo_documento=mainModel::clean_chain($_POST['tipo_documento']);
            $num_documento=mainModel::clean_chain($_POST['num_documento']);
            $direccion=mainModel::clean_chain($_POST['direccion']);
            $celular=mainModel::clean_chain($_POST['celular']);
            $correo=mainModel::clean_chain($_POST['correo']);
    
            $query1=mainModel::run_simple_query("SELECT *FROM proveedor WHERE proved_id='$proved_id'");
            $answer=$query1->fetch();
    
            if($num_documento!=$answer['proved_numDocumento']){
                $query2=mainModel::run_simple_query("SELECT proved_numDocumento FROM proveedor WHERE proved_numDocumento='$num_documento'");
                if ($query2->rowCount()==1) {
                    $alert=[
                        "Alert"=>"simple",
                        "title"=>"Ocurrió un error inesperado",
                        "text"=>"¡Error! Número de documento ya existente",
                        "icon"=>"error"
                    ];
                    return mainModel::sweet_alert($alert);
                    
                }
            }
            $dataProvider=[
                "Nombre"=>$nombre,
                "Tipo"=>$tipo_documento,
                "Numero"=>$num_documento,
                "Celular"=>$celular,
                "Correo"=>$correo,
                "Direccion"=>$direccion,
                "Codigo"=>$proved_id
            ];
    
            if (proveedorModels::update_provee_model($dataProvider)) {
                $alert=[
                    "Alert"=>"clean",
                    "title"=>"Operacion exitosa",
                    "text"=>"Datos actualizado exitosamente",
                    "icon"=>"success"
                ];
            } else {
                $alert=[
                    "Alert"=>"simple",
                    "title"=>"Ocurrió un error inesperado",
                    "text"=>"¡Error! No hemos podido registrar cliente, en este momento",
                    "icon"=>"error"
                ];
            } 
            return mainModel::sweet_alert($alert);
        }
        public function list_provee_controller(){
 
            $query = proveedorModels::list_provee_model();
            $data=Array();
            $i=1;
            while ($reg=$query->fetch()) {
                $data[]=array(
                    "0"=>
                    '<div class="btn-group">
                    <button type="button" class="btn btn-primary btn-sm edit" title="Actualizar" id="'.mainModel::encryption($reg['proved_id']).'">
                    <i class="fa fa-edit fa-xs"></i>
                    </button>'.' '.' 
                    <button type="button" class="btn btn-danger btn-sm delete" title="Eliminar" id="'.mainModel::encryption($reg['proved_id']).'">
                    <i class="fa fa-trash fa-xs"></i>
                    </button></div>',
                    "1"=>$reg['proved_nombre'],
                    "2"=>$reg['proved_tipoDocumento'] ?? '',
"3"=>$reg['proved_numDocumento'] ?? '',

                    "4"=>$reg['proved_celular'],
                    "5"=>$reg['proved_correo'], 
                    "6"=>$reg['proved_direccion']
                );
            }
            $results=array(
                     "sEcho"=>1,
                     "iTotalRecords"=>count($data),
                     "iTotalDisplayRecords"=>count($data),
                     "aaData"=>$data);
            echo json_encode($results);
        }  
        public function delete_provee_controller(){

            $proved_id=mainModel::decryption($_POST['proved_id']);
            $proved_id=mainModel::clean_chain($proved_id);
           
            $query=mainModel::run_simple_query("SELECT compra_id_proveedor  FROM compra WHERE compra_id_proveedor ='$proved_id'");
                if ($query->rowCount()==1){             
                    $alert=[
                        "Alert"=>"simple",
                        "title"=>"Ocurrió un error inesperado",
                        "text"=>"!Error¡ Proveedor asociado a una compra",
                        "icon"=>"error"
                    ];
                } else {
                    $query2=mainModel::run_simple_query("SELECT lote_id_proveedor  FROM lote WHERE lote_id_proveedor ='$proved_id'");
                    if($query2->rowCount()==1){             
                        $alert=[
                            "Alert"=>"simple",
                            "title"=>"Ocurrió un error inesperado",
                            "text"=>"!Error¡ Proveedor asociado a un lote",
                            "icon"=>"error"
                        ];
                    }else{
                        $proveedor=proveedorModels::delete_provee_model($proved_id);                 
                        if ($proveedor->rowCount()>=1) {                 
                            $alert=[
                                    "Alert"=>"simple",
                                    "title"=>"Operacion exitosa",
                                    "text"=>"Proveedor eliminado exitosamente",
                                    "icon"=>"success"
                            ];
                        } else {
            
                            $alert=[
                                "Alert"=>"simple",
                                "title"=>"Ocurrió un error inesperado",
                                "text"=>"¡Error! No hemos podido eliminar proveedor, en este momento",
                                "icon"=>"error"
                            ];
                        }
                    }
                }
     
            return mainModel::sweet_alert($alert);
        }
        public function show_provee_controller(){
            $proved_id=mainModel::decryption($_POST['proved_id']);
            $proved_id=mainModel::clean_chain($proved_id);
            $query=proveedorModels::show_provee_model($proved_id);
            $rspta= $query->fetch();
            echo json_encode($rspta);
        }     
    }