<?php
    if($ajaxRequest){
        require_once "../Models/presentacionModels.php";
    }else{
        require_once "./Models/presentacionModels.php";
    }
 
    class presentacionControllers extends presentacionModels{

        public function add_present_controller(){
            $presentacion=mainModel::clean_chain($_POST['presentacion']); 
            $query1=mainModel::run_simple_query("SELECT present_nombre FROM presentacion WHERE present_nombre='$presentacion'");
            if ($query1->rowCount()>=1) {
                $alert=[
                    "Alert"=>"simple",
                    "title"=>"Ocurrió un error inesperado",
                    "text"=>"¡Error! Nombre de presentacion ya existente",
                    "icon"=>"error"
                ];
            }else{
                $query2=mainModel::run_simple_query("SELECT present_id FROM presentacion");
                $number=($query2->rowCount())+1;
                $code_present=mainModel::random_code("PRE-",6,$number);
                $dataPresentacion=[
                    "Code"=>$code_present,
                    "Presentacion"=>$presentacion
                ];

                $savePresentacion=presentacionModels::add_present_model($dataPresentacion);
                    if ($savePresentacion->rowCount()>=1) {
                            $alert=[
                                "Alert"=>"clean",
                                "title"=>"Operacion exitosa",
                                "text"=>"Presentacion registrada exitosamente",
                                "icon"=>"success"
                            ];
                    } else {
                            $alert=[
                                "Alert"=>"simple",
                                "title"=>"Ocurrió un error inesperado",
                                "text"=>"¡Error! No hemos podido registrar presentacion, en este momento",
                                "icon"=>"error"
                            ];
                    }
            }
            return mainModel::sweet_alert($alert);
        }
        public function update_present_controller(){

            $present_id=mainModel::clean_chain($_POST['present_id']);
            $presentacion=mainModel::clean_chain($_POST['presentacion']); 

            $query1=mainModel::run_simple_query("SELECT present_nombre FROM presentacion WHERE present_nombre='$presentacion'");
            if ($query1->rowCount()>=1) {
                $alert=[
                    "Alert"=>"simple",
                    "title"=>"Ocurrió un error inesperado",
                    "text"=>"¡Error! Nombre de presentacion ya existente",
                    "icon"=>"error"
                ];
                return mainModel::sweet_alert($alert);
            
            }else{
                $dataPresent=[
                    "Presentacion"=>$presentacion,
                    "Code"=>$present_id
                ];
                if (presentacionModels::update_present_model($dataPresent)) {
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
                        "text"=>"¡Error! No hemos podido actualizar presentacion, en este momento",
                        "icon"=>"error"
                    ];
                }
            }
       
            return mainModel::sweet_alert($alert);
        }
        public function list_present_controller(){
            $query = presentacionModels::list_present_model();
            $data=Array();
            $i=1;
            while ($reg=$query->fetch()) {
                $data[]=array(
                    "0"=>
                    '<div class="btn-group"><button class="btn btn-primary btn-sm edit"   title="Actualizar" id="'.mainModel::encryption($reg['present_id']).'">
                    <i class="fa fa-edit fa-xs"></i>
                    </button>'.' '.' 
                    <button type="button" class="btn btn-danger btn-sm delete"   title="Eliminar" id="'.mainModel::encryption($reg['present_id']).'">
                    <i class="fa fa-trash fa-xs"></i>
                    </button></div>',
                    "1"=>$reg['present_codigo'],
                    "2"=>$reg['present_nombre']
                );
                $i++; 
            }
            $results=array(
                     "sEcho"=>1,
                     "iTotalRecords"=>count($data),
                     "iTotalDisplayRecords"=>count($data),
                     "aaData"=>$data);
            echo json_encode($results);
        }  
        public function delete_present_controller(){
            $present_id=mainModel::decryption($_POST['present_id']);
            $present_id=mainModel::clean_chain($present_id);
           
            $query=mainModel::run_simple_query("SELECT prod_id_present  FROM producto WHERE prod_id_present ='$present_id'");
                if ($query->rowCount()==1){             
                    $alert=[
                        "Alert"=>"simple",
                        "title"=>"Ocurrió un error inesperado",
                        "text"=>"¡Error! Presentacion asociada a un producto",
                        "icon"=>"error"
                    ];
                } else {
                
                    $category=presentacionModels::delete_present_model($present_id);                 
                    if ($category->rowCount()>=1) {                 
                        $alert=[
                            "Alert"=>"clean",
                            "title"=>"Operacion exitosa",
                            "text"=>"Presentacion eliminada exitosamente",
                            "icon"=>"success"
                        ];
                    } else {
           
                        $alert=[
                            "Alert"=>"simple",
                            "title"=>"Ocurrió un error inesperado",
                            "text"=>"¡Error! No hemos podido eliminar presentacion, en este momento",
                            "icon"=>"error"
                        ];
                    }
                }
     
            return mainModel::sweet_alert($alert);
        }
        public function show_present_controller(){
            $present_id=mainModel::decryption($_POST['present_id']);
            $present_id=mainModel::clean_chain($present_id);
            $query=presentacionModels::show_present_model($present_id);
            $rspta= $query->fetch();
            echo json_encode($rspta); 
        }     
    }