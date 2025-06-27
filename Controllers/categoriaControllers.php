<?php
    if($ajaxRequest){
        require_once "../Models/categoriaModels.php";
    }else{
        require_once "./Models/categoriaModels.php";
    }
 
    class categoriaControllers extends categoriaModels{

        public function add_cate_controller(){
            $categoria=mainModel::clean_chain($_POST['categoria']); 
            $query1=mainModel::run_simple_query("SELECT tipo_nombre FROM tipo_producto WHERE tipo_nombre='$categoria'");
            if ($query1->rowCount()>=1) {
                $alert=[
                    "Alert"=>"simple",
                    "title"=>"Ocurrió un error inesperado",
                    "text"=>"¡Error! Nombre de categoria ya existente",
                    "icon"=>"error"
                ];
            }else{
                $query2=mainModel::run_simple_query("SELECT tipo_id FROM tipo_producto");
                $number=($query2->rowCount())+1;
                $code_category=mainModel::random_code("CATE-",6,$number);
                $dataCategory=[
                    "Code"=>$code_category,
                    "Category"=>$categoria
                ];

                $saveCategory=categoriaModels::add_cate_model($dataCategory);
                    if ($saveCategory->rowCount()>=1) {
                            $alert=[
                                "Alert"=>"clean",
                                "title"=>"Operacion exitosa",
                                "text"=>"Categoria registrada exitosamente",
                                "icon"=>"success"
                            ];
                    } else {
                            $alert=[
                                "Alert"=>"simple",
                                "title"=>"Ocurrió un error inesperado",
                                "text"=>"¡Error! No hemos podido registrar categoría, en este momento",
                                "icon"=>"error"
                            ];
                    }
            }
            return mainModel::sweet_alert($alert);
        }
        public function update_cate_controller(){

            $tipo_id=mainModel::clean_chain($_POST['tipo_id']);
            $categoria=mainModel::clean_chain($_POST['categoria']); 

            $query1=mainModel::run_simple_query("SELECT tipo_nombre FROM tipo_producto WHERE tipo_nombre='$categoria'");
            if ($query1->rowCount()>=1) {
                $alert=[
                    "Alert"=>"simple",
                    "title"=>"Ocurrió un error inesperado",
                    "text"=>"¡Error! Nombre de categoria ya existente",
                    "icon"=>"error"
                ];
                return mainModel::sweet_alert($alert);
                
            }else{
                $dataCate=[
                    "Category"=>$categoria,
                    "Code"=>$tipo_id
                ];
                if (categoriaModels::update_cate_model($dataCate)) {
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
                        "text"=>"¡Error! No hemos podido actualizar categoría, en este momento",
                        "icon"=>"error"
                    ];
                }
            }
       
            return mainModel::sweet_alert($alert);
        }
        public function list_cate_controller(){
            $query = categoriaModels::list_cate_model();
            $data=Array();
            while ($reg=$query->fetch()) {
                $data[]=array(
                    "0"=>                  
                    '<div class="btn-group"><button   title="Actualizar" class="btn btn-primary btn-sm edit" title="Edit" id="'.mainModel::encryption($reg['tipo_id']).'">
                    <i class="fa fa-edit fa-xs"></i>
                    </button>'.' '.' 
                    <button type="button"   title="Eliminar" class="btn btn-danger btn-sm delete" title="Remove" id="'.mainModel::encryption($reg['tipo_id']).'">
                        <i class="fa fa-trash fa-xs"></i>
                    </button></div>',
                    "1"=>$reg['tipo_codigo'],
                    "2"=>$reg['tipo_nombre']
                );
            }
            $results=array(
                     "sEcho"=>1,
                     "iTotalRecords"=>count($data),
                     "iTotalDisplayRecords"=>count($data),
                     "aaData"=>$data);
            echo json_encode($results);
        }  
        public function delete_cate_controller(){
            $tipo_id=mainModel::decryption($_POST['tipo_id']);
            $tipo_id=mainModel::clean_chain($tipo_id);
           
            $query=mainModel::run_simple_query("SELECT prod_id_tipo FROM producto WHERE prod_id_tipo='$tipo_id'");
                if ($query->rowCount()==1){             
                    $alert=[
                        "Alert"=>"simple",
                        "title"=>"Ocurrió un error inesperado",
                        "text"=>"¡Error! Categoría asociada a un producto",
                        "icon"=>"error"
                    ];
                } else {
                
                    $category=categoriaModels::delete_cate_model($tipo_id);                 
                    if ($category->rowCount()>=1) {                 
                        $alert=[
                                "Alert"=>"clean",
                                "title"=>"Operacion exitosa",
                                "text"=>"Categoria eliminada exitosamente",
                                "icon"=>"success"
                        ];
                    } else {
                        $alert=[
                            "Alert"=>"simple",
                            "title"=>"Ocurrió un error inesperado",
                            "text"=>"¡Error! No hemos podido eliminar categoría, en este momento",
                            "icon"=>"error"
                        ];
                    }
                }
     
            return mainModel::sweet_alert($alert);
        }
        public function show_cate_controller(){
            $tipo_id=mainModel::decryption($_POST['tipo_id']);
            $tipo_id=mainModel::clean_chain($tipo_id);
            $query=categoriaModels::show_cate_model($tipo_id);
            $rspta= $query->fetch();
            echo json_encode($rspta); 
        }     
    }