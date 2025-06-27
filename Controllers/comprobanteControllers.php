<?php
    if($ajaxRequest){
        require_once "../Models/comprobanteModels.php";
    }else{
        require_once "./Models/comprobanteModels.php";
    }
 
    class comprobanteControllers extends comprobanteModels{

        public function add_voucher_controller(){
            $nombre=mainModel::clean_chain($_POST['nombre']); 
            $letraSerie=mainModel::clean_chain($_POST['letraSerie']); 
            $serie=mainModel::clean_chain($_POST['serie']); 
            $numeroSerie=mainModel::clean_chain($_POST['numeroSerie']); 
            $query1=mainModel::run_simple_query("SELECT comprobante_nombre FROM comprobante WHERE comprobante_nombre='$nombre'");
            if ($query1->rowCount()>=1) {
                $alert=[
                    "Alert"=>"simple",
                    "title"=>"Ocurrió un error inesperado",
                    "text"=>"¡Error! Nombre de comprobante ya existente",
                    "icon"=>"error"
                ];
            }else{
              
                $dataVoucher=[
                    "Nombre"=>$nombre,
                    "LetraSerie"=>$letraSerie,
                    "Serie"=>$serie,
                    "Numero"=>$numeroSerie,
                    "Estado"=>"1"
                ];

                $saveVoucher=comprobanteModels::add_voucher_model($dataVoucher);
                    if ($saveVoucher->rowCount()>=1) {
                            $alert=[
                                "Alert"=>"clean",
                                "title"=>"Operacion exitosa",
                                "text"=>"Comprobante registrado exitosamente",
                                "icon"=>"success"
                            ];
                    } else {
                            $alert=[
                                "Alert"=>"simple",
                                "title"=>"Ocurrió un error inesperado",
                                "text"=>"¡Error! No hemos podido registrar comprobante, en este momento",
                                "icon"=>"error"
                            ];
                    }
            }
            return mainModel::sweet_alert($alert);
        }
        public function update_voucher_controller(){

            $comprobante_id=mainModel::clean_chain($_POST['comprobante_id']);
            $nombre=mainModel::clean_chain($_POST['nombre']); 
            $letraSerie=mainModel::clean_chain($_POST['letraSerie']); 
            $serie=mainModel::clean_chain($_POST['serie']); 
            $numeroSerie=mainModel::clean_chain($_POST['numeroSerie']); 

            $query1=mainModel::run_simple_query("SELECT comprobante_nombre FROM comprobante WHERE comprobante_nombre='$nombre'");
            if ($query1->rowCount()>=1) {
                $alert=[
                    "Alert"=>"simple",
                    "title"=>"Ocurrió un error inesperado",
                    "text"=>"¡Error! Nombre de comprobante ya existente",
                    "icon"=>"error"
                ];
                return mainModel::sweet_alert($alert);
                
            }else{
                $dataVoucher=[
                    "Nombre"=>$nombre,
                    "LetraSerie"=>$letraSerie,
                    "Serie"=>$serie,
                    "Numero"=>$numeroSerie,
                    "Codigo"=>$comprobante_id
                ];
                if (comprobanteModels::update_voucher_model($dataVoucher)) {
                    $alert=[
                        "Alert"=>"clean",
                        "title"=>"Operacion exitosa",
                        "text"=>"Datos actualizados exitosamente",
                        "icon"=>"success"
                    ];
                } else {
                    $alert=[
                        "Alert"=>"simple",
                        "title"=>"Ocurrió un error inesperado",
                        "text"=>"¡Error! No hemos podido actualizar comprobante, en este momento",
                        "icon"=>"error"
                    ];
                }
            }
       
            return mainModel::sweet_alert($alert);
        }
        public function list_voucher_controller(){
            $query = comprobanteModels::list_voucher_model();
            $data=Array();
            while ($reg=$query->fetch()) {
                $data[]=array(
                    "0"=>($reg['comprobante_estado'])?                  
                    '<div class="btn-group"><button   title="Actualizar" class="btn btn-primary btn-sm edit" id="'.mainModel::encryption($reg['comprobante_id']).'">
                    <i class="fa fa-edit fa-xs"></i>
                    </button>'.' '.' 
                    <button type="button"   title="Desactivar" class="btn btn-warning btn-sm desactivate" id="'.mainModel::encryption($reg['comprobante_id']).'">
                        <i class="fa fa-ban fa-xs"></i>
                    </button>'.' '.' 
                    <button type="button"   title="Eliminar" class="btn btn-danger btn-sm delete" id="'.mainModel::encryption($reg['comprobante_id']).'">
                        <i class="fa fa-trash fa-xs"></i>
                    </button></div>':
                    '<div class="btn-group"><button   title="Actualizar" class="btn btn-primary btn-sm edit" id="'.mainModel::encryption($reg['comprobante_id']).'">
                    <i class="fa fa-edit fa-xs"></i>
                    </button>'.' '.' 
                    <button type="button"   title="Activar" class="btn btn-info btn-sm activate" id="'.mainModel::encryption($reg['comprobante_id']).'">
                        <i class="fa fa-check fa-xs"></i>
                    </button>'.' '.' 
                    <button type="button"   title="Eliminar" class="btn btn-danger btn-sm delete" id="'.mainModel::encryption($reg['comprobante_id']).'">
                        <i class="fa fa-trash fa-xs"></i>
                    </button></div>',
                    "1"=>$reg['comprobante_nombre'],
                    "2"=>($reg['comprobante_letraSerie'] ?? '') . ($reg['comprobante_serie'] ?? '') . '-' . ($reg['comprobante_numero'] ?? ''),

                    "3"=>($reg['comprobante_estado'])?'<span class="badge badge-success">Activado</span>':'<span class="badge badge-danger">Desactivado</span>' 
                );
            }
            $results=array(
                     "sEcho"=>1,
                     "iTotalRecords"=>count($data),
                     "iTotalDisplayRecords"=>count($data),
                     "aaData"=>$data);
            echo json_encode($results);
        }  
        public function activate_voucher_controller(){

            $comprobante_id=mainModel::decryption($_POST['comprobante_id']);
            $comprobante_id=mainModel::clean_chain($comprobante_id);
            $rspta=comprobanteModels::activate_voucher_model($comprobante_id);
            if ($rspta->rowCount()>=1) {                 
                $alert=[
                        "Alert"=>"simple",
                        "title"=>"Activado",
                        "text"=>"Comprobante activado exitosamente",
                        "icon"=>"success"
                ];
            } else {
   
                $alert=[
                    "Alert"=>"simple",
                    "title"=>"Ocurrió un error inesperado",
                    "text"=>"¡Error! No hemos podido activar comprobante, en este momento",
                    "icon"=>"error"
                ];
            } 
            return mainModel::sweet_alert($alert);        
        } 
        public function deactivate_voucher_controller(){

            $comprobante_id=mainModel::decryption($_POST['comprobante_id']);
            $comprobante_id=mainModel::clean_chain($comprobante_id);
            $rspta=comprobanteModels::deactivate_voucher_model($comprobante_id);
            if ($rspta->rowCount()>=1) {                 
                $alert=[
                        "Alert"=>"simple",
                        "title"=>"Desactivado",
                        "text"=>"Comprobante desactivado exitoxamente",
                        "icon"=>"success"
                ];
            } else {
   
                $alert=[
                    "Alert"=>"simple",
                    "title"=>"Ocurrió un error inesperado",
                    "text"=>"¡Error! No hemos podido desactivar comprobante, en este momento",
                    "icon"=>"error"
                ];
            } 
            return mainModel::sweet_alert($alert);       
        } 
        public function delete_voucher_controller(){
            $comprobante_id=mainModel::decryption($_POST['comprobante_id']);
            $comprobante_id=mainModel::clean_chain($comprobante_id);
           
            $query=mainModel::run_simple_query("SELECT venta_id_comprobante FROM venta WHERE venta_id_comprobante='$comprobante_id'");
                if ($query->rowCount()==1){             
                    $alert=[
                        "Alert"=>"simple",
                        "title"=>"Ocurrió un error inesperado",
                        "text"=>"¡Error! Comprobante asociada a una venta",
                        "icon"=>"error"
                    ];
                } else {
                
                    $comprobante=comprobanteModels::delete_voucher_model($comprobante_id);                 
                    if ($comprobante->rowCount()>=1) {                 
                        $alert=[
                                "Alert"=>"clean",
                                "title"=>"Operacion exitosa",
                                "text"=>"Comprobante eliminado exitosamente",
                                "icon"=>"success"
                        ];
                    } else {
                        $alert=[
                            "Alert"=>"simple",
                            "title"=>"Ocurrió un error inesperado",
                            "text"=>"¡Error! No hemos podido eliminar comprobante, en este momento",
                            "icon"=>"error"
                        ];
                    }
                }
     
            return mainModel::sweet_alert($alert);
        }
        public function show_voucher_controller(){
            $comprobante_id=mainModel::decryption($_POST['comprobante_id']);
            $comprobante_id=mainModel::clean_chain($comprobante_id);
            $query=comprobanteModels::show_voucher_model($comprobante_id);
            $rspta= $query->fetch();
            echo json_encode($rspta); 
        }     
    }