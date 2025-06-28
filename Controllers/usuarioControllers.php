<?php
    if($ajaxRequest){
        require_once "../Models/usuarioModels.php";
    }else{
        require_once "./Models/usuarioModels.php";
    }
 
    class usuarioControllers extends usuarioModels{

        public function add_user_controller(){
            $dni=mainModel::clean_chain($_POST['dni']);
            $nombre=mainModel::clean_chain($_POST['nombre']);
            $apellido=mainModel::clean_chain($_POST['apellido']);
            $celular=mainModel::clean_chain($_POST['celular']);
            $usuario=mainModel::clean_chain($_POST['usuario']);
            $clave=mainModel::clean_chain($_POST['clave']);
            $genero=mainModel::clean_chain($_POST['genero']);
            $fnacimiento=mainModel::clean_chain($_POST['fnacimiento']);
            $profesion=mainModel::clean_chain($_POST['profesion']);
            $cargo=mainModel::clean_chain($_POST['cargo']);
            $descripcion=mainModel::clean_chain($_POST['descripcion']);
            $imagen=mainModel::clean_chain($_POST['imagen']);

            if($imagen == ""){
                if($genero=="Masculino"){
                    $foto="../Assets/images/avatar/masculino.png";
                }else{
                    $foto="../Assets/images/avatar/femenino.png";
                }
            }
            
            if($clave !=""){
                $query1=mainModel::run_simple_query("SELECT usuario_dni FROM usuario WHERE usuario_dni='$dni'");
                if($query1->rowCount()==1){
                    $alert=[
                        "Alert"=>"simple",
                        "title"=>"Ocurrió un error inesperado",
                        "text"=>"¡Error! Número de dni existente",
                        "icon"=>"error"
                    ];
                }else{
              
                        $query3=mainModel::run_simple_query("SELECT usuario_login FROM usuario WHERE usuario_login='$usuario'");
                        if($query3->rowCount()>=1){
                            $alert=[
                                "Alert"=>"simple",
                                "title"=>"Ocurrió un error inesperado",
                                "text"=>"¡Error! Nombre de usuario ya existente",
                                "icon"=>"error"
                            ];
                        }else{
                            $query4=mainModel::run_simple_query("SELECT usuario_id FROM usuario");
                            $number=($query4->rowCount())+1;
                            $codigo=mainModel::random_code("USU-",6,$number);
                            $clave_encrip=mainModel::encryption($clave);

                            $dataAccount=[
                                "Codigo"=>$codigo,
                                "Nombre"=>$nombre,
                                "Apellido"=>$apellido,
                                "Dni"=>$dni,
                                "Nacimiento"=>$fnacimiento,
                                "Profesion"=>$profesion,
                                "Celular"=>$celular,
                                "Genero"=>$genero,
                                "Cargo"=>$cargo,
                                "Descripcion"=>$descripcion,
                                "Login"=>$usuario,
                                "Clave"=>$clave_encrip,
                                "Foto"=>$foto,
                                "Estado"=>"1"
                            ];

                            $saveAccount=usuarioModels::add_user_model($dataAccount);
                            if($saveAccount->rowCount()>=1){                                      
                    
                                    $alert=[
                                        "Alert"=>"clean",
                                        "title"=>"Operacion exitosa",
                                        "text"=>"Usuario registrado exitosamente",
                                        "icon"=>"success"
                                    ];
                              
                            }else{
                                $alert=[
                                    "Alert"=>"simple",
                                    "title"=>"Ocurrió un error inesperado",
                                    "text"=>"¡Error! No hemos podido registrar usuario, en este momento",
                                    "icon"=>"error"
                                ];
                            }
                        }
                }
            }else{
                $alert=[
                    "Alert"=>"simple",
                    "title"=>"Ocurrio un error inesperado",
                    "text"=>"La nuevas contraseñas no coinciden, por favor verifique los datos e intente nuevamente.",
                    "icon"=>"error"
                ];
                return mainModel::sweet_alert($alert);
                
            }
            return mainModel::sweet_alert($alert);
        }
        public function update_user_controller(){

            $usuario_id=mainModel::clean_chain($_POST['usuario_id']);
    
            $dni=mainModel::clean_chain($_POST['dni']);
            $nombre=mainModel::clean_chain($_POST['nombre']);
            $apellido=mainModel::clean_chain($_POST['apellido']);
            $celular=mainModel::clean_chain($_POST['celular']);
            $username=mainModel::clean_chain($_POST['usuario']);
            $clave=mainModel::clean_chain($_POST['clave']);   
            $genero=mainModel::clean_chain($_POST['genero']);
            $fnacimiento=mainModel::clean_chain($_POST['fnacimiento']);
            $profesion=mainModel::clean_chain($_POST['profesion']);
            $cargo=mainModel::clean_chain($_POST['cargo']);
            $descripcion=mainModel::clean_chain($_POST['descripcion']);
            $imagen=mainModel::clean_chain($_POST['imagen']);
    
      
            $query1=mainModel::run_simple_query("SELECT * FROM usuario WHERE usuario_id='$usuario_id'");
            $usuario=$query1->fetch();
    
            if($clave !=""){
                $password=mainModel::encryption($clave);
            }else{
                $password=$usuario['usuario_contrasena'];
            }
            if($dni!=$usuario['usuario_dni']){
                $query2=mainModel::run_simple_query("SELECT usuario_dni FROM usuario WHERE usuario_dni='$dni'");
                if ($query2->rowCount()==1) {
                    $alert=[
                        "Alert"=>"simple",
                        "title"=>"Ocurrió un error inesperado",
                        "text"=>"¡Error! Número de dni existente",
                        "icon"=>"error"
                    ];
                    return mainModel::sweet_alert($alert);
                    
                }
            }
            $dataUser=[
                "Nombre"=>$nombre,
                "Apellido"=>$apellido,
                "Dni"=>$dni,
                "Nacimiento"=>$fnacimiento,
                "Profesion"=>$profesion,
                "Celular"=>$celular,
                "Genero"=>$genero,
                "Cargo"=>$cargo,
                "Descripcion"=>$descripcion,
                "Login"=>$username,
                "Clave"=>$password,
                "Foto"=>$imagen,
                "Codigo"=>$usuario_id

             
            ];
    
            if (usuarioModels::update_user_model($dataUser)) {
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
                    "text"=>"¡Error! No hemos podido registrar producto, en este momento",
                    "icon"=>"error"
                ];
            }
            return mainModel::sweet_alert($alert);
        }
        public function update_perfil_controller(){

            $usuario_id=mainModel::clean_chain($_POST['usuario_id']);
    
            $dni=mainModel::clean_chain($_POST['dni']);
            $nombre=mainModel::clean_chain($_POST['nombre']);
            $apellido=mainModel::clean_chain($_POST['apellido']);
            $celular=mainModel::clean_chain($_POST['celular']);
            $profesion=mainModel::clean_chain($_POST['profesion']);
            $descripcion=mainModel::clean_chain($_POST['descripcion']);

    
            $query1=mainModel::run_simple_query("SELECT * FROM usuario WHERE usuario_id='$usuario_id'");
            $usuario=$query1->fetch();

            if($dni!=$usuario['usuario_dni']){
                $query2=mainModel::run_simple_query("SELECT usuario_dni FROM usuario WHERE usuario_dni='$dni'");
                if ($query2->rowCount()==1) {
                    $alert=[
                        "Alert"=>"simple",
                        "title"=>"Ocurrió un error inesperado",
                        "text"=>"¡Error! Número de dni existente",
                        "icon"=>"error"
                    ];
                    return mainModel::sweet_alert($alert);
                    
                }
            }
            $dataUser=[
                "Nombre"=>$nombre,
                "Apellido"=>$apellido,
                "Dni"=>$dni,
                "Profesion"=>$profesion,
                "Celular"=>$celular,
                "Descripcion"=>$descripcion,
                "Codigo"=>$usuario_id
            ];
    
            if (usuarioModels::update_perfil_model($dataUser)) {
                session_start(['name'=>'STR']);
                $_SESSION['name_str']=$nombre;
                $_SESSION['lastname_str']=$apellido;
                $_SESSION['mobile_str']=$celular;
                $_SESSION['description_str']=$descripcion;
                $_SESSION['profession_str']=$profesion;

                $alert=[
                    "Alert"=>"recharge",
                    "title"=>"Operacion exitosa",
                    "text"=>"Perfil Actualizado exitosamente",
                    "icon"=>"success"
                ];
            } else {
                $alert=[
                    "Alert"=>"simple",
                    "title"=>"Ocurrió un error inesperado",
                    "text"=>"¡Error! No hemos podido actualizar perfil, en este momento",
                    "icon"=>"error"
                ];
            }
            return mainModel::sweet_alert($alert);
        }
        public function update_password_controller(){
            $usuario_idi=mainModel::clean_chain($_POST['usuario_idi']);
    
            $passwordOld=mainModel::clean_chain($_POST['passwordOld']);
            $passwordOldc=mainModel::encryption($passwordOld);
            $passwordNew=mainModel::encryption($_POST['passwordNew']);

            $query=mainModel::run_simple_query("SELECT * FROM usuario WHERE usuario_id='$usuario_idi'");
            $usuario=$query->fetch();

            if($passwordOldc!=$usuario['usuario_contrasena']){
                $alert=[
                    "Alert"=>"simple",
                    "title"=>"Ocurrió un error inesperado",
                    "text"=>"¡Error! La contraseña actual no coincide",
                    "icon"=>"error"
                ];
                return mainModel::sweet_alert($alert);
                
            }
            $dataUser=[
                "Clave"=>$passwordNew,
                "Codigo"=>$usuario_idi
            ];
    
            if (usuarioModels::update_password_model($dataUser)) {

                $alert=[
                    "Alert"=>"simple",
                    "title"=>"Operacion exitosa",
                    "text"=>"Contraseña actualizada exitosamente",
                    "icon"=>"success"
                ];
            } else {
                $alert=[
                    "Alert"=>"simple",
                    "title"=>"Ocurrió un error inesperado",
                    "text"=>"¡Error! No hemos podido actualizar contraseña, en este momento",
                    "icon"=>"error"
                ];
            }
            return mainModel::sweet_alert($alert);
           
        }
        public function list_user_controller(){
          
            $query = usuarioModels::list_user_model();
            $data=Array();
            while ($reg=$query->fetch()) {
                $data[]=array(
                    "0"=>($reg['usuario_estado'])?
                    '<div class="btn-group"><button class="btn btn-primary btn-sm edit"   title="Actualizar" id="'.mainModel::encryption($reg['usuario_id']).'">
                    <i class="fa fa-edit fa-xs"></i></button>'.' '.'
                    <button type="button" class="btn btn-danger btn-sm delete"   title="Eliminar" id="'.mainModel::encryption($reg['usuario_id']).'">
                    <i class="fa fa-trash fa-xs"></i>
                    </button></div>
                    ':'
                    <div class="btn-group"><button type="button" class="btn btn-danger btn-sm delete"   title="Eliminar" id="'.mainModel::encryption($reg['usuario_id']).'">
                    <i class="fa fa-trash fa-xs"></i>
                    </button></div>',
                    "1"=>$reg['usuario_dni'],
                    "2"=>$reg['usuario_nombre']." ".$reg['usuario_apellido'],
                    "3"=>$reg['usuario_fechanacimiento'],
                    "4"=>$reg['usuario_profesion'],
                    "5"=>$reg['usuario_cargo'],
                    "6"=>$reg['usuario_celular'],
                    "7"=>$reg['usuario_genero'],
                    "8"=>($reg['usuario_estado'])?
                    '<button type="button"   title="Descativar" class="btn btn-success btn-sm desactivate" id="'.mainModel::encryption($reg['usuario_id']).'" style="cursor:pointer;">Activo</button>':
                    '<button type="button"   title="Activar" class="btn btn-danger btn-sm activate" id="'.mainModel::encryption($reg['usuario_id']).'" style="cursor:pointer;">Desactivado</button>'
                );
            }
            $results=array(
                     "sEcho"=>1,
                     "iTotalRecords"=>count($data),
                     "iTotalDisplayRecords"=>count($data),
                     "aaData"=>$data);
            echo json_encode($results);
        }  
        public function activate_user_controller(){

            $usuario_id=mainModel::decryption($_POST['usuario_id']);
            $usuario_id=mainModel::clean_chain($usuario_id);
            $rspta=usuarioModels::activate_user_model($usuario_id);
            if ($rspta->rowCount()>=1) {                 
                $alert=[
                        "Alert"=>"simple",
                        "title"=>"Activado",
                        "text"=>"Cuenta activada exitosamente",
                        "icon"=>"success"
                ];
            } else {
   
                $alert=[
                    "Alert"=>"simple",
                    "title"=>"Ocurrió un error inesperado",
                    "text"=>"¡Error! No hemos podido registrar producto, en este momento",
                    "icon"=>"error"
                ];
            } 
            return mainModel::sweet_alert($alert);        
        } 
        public function deactivate_user_controller(){

            $usuario_id=mainModel::decryption($_POST['usuario_id']);
            $usuario_id=mainModel::clean_chain($usuario_id);
            $rspta=usuarioModels::deactivate_user_model($usuario_id);
            if ($rspta->rowCount()>=1) {                 
                $alert=[
                        "Alert"=>"simple",
                        "title"=>"Desactivado",
                        "text"=>"Cuenta desactivada exitoxamente",
                        "icon"=>"success"
                ];
            } else {
   
                $alert=[
                    "Alert"=>"simple",
                    "title"=>"Ocurrió un error inesperado",
                    "text"=>"¡Error! No hemos podido registrar producto, en este momento",
                    "icon"=>"error"
                ];
            } 
            return mainModel::sweet_alert($alert);       
        } 
        public function delete_user_controller(){
            $usuario_id=mainModel::decryption($_POST['usuario_id']);
            $usuario_id=mainModel::clean_chain($usuario_id);
 
            $query=mainModel::run_simple_query("SELECT compra_id_usuario  FROM compra WHERE compra_id_usuario ='$usuario_id'");
            if ($query->rowCount()==1){             
                $alert=[
                    "Alert"=>"simple",
                    "title"=>"Ocurrió un error inesperado",
                    "text"=>"!Error¡ Usuario asociado a una compra",
                    "icon"=>"error"
                ];
            } else {
                $query2=mainModel::run_simple_query("SELECT venta_id_usuario  FROM venta WHERE venta_id_usuario ='$usuario_id'");
                if($query2->rowCount()==1){             
                    $alert=[
                        "Alert"=>"simple",
                        "title"=>"Ocurrió un error inesperado",
                        "text"=>"!Error¡ Usuario asociado a una venta",
                        "icon"=>"error"
                    ];
                }else{
                    $query3=mainModel::run_simple_query("SELECT usuario_id FROM usuario WHERE usuario_id='$usuario_id'");
                    $asnwer=$query3->fetch();
                    if ($asnwer['usuario_id']!=1) {
                
                        $user=usuarioModels::delete_user_model($usuario_id);
                        mainModel::delete_binnacle($usuario_id);
                        if ($user->rowCount()>=1) {                

                                $alert=[
                                    "Alert"=>"simple",
                                    "title"=>"Operacion exitosa",
                                    "text"=>"Usuario eliminado exitosamente",
                                    "icon"=>"success"
                                ];
                        
                        }else{
                            $alert=[
                                "Alert"=>"simple",
                                "title"=>"Ocurrió un error inesperado",
                                "text"=>"¡Error! No hemos podido eliminar usuario, en este momento",
                                "icon"=>"error"
                            ];
                        }
                    } else {
                        $alert=[
                            "Alert"=>"simple",
                            "title"=>"Ocurrió un error inesperado",
                            "text"=>"¡Error! No tienes permiso para eliminar al administrador",
                            "icon"=>"error"
                        ];
                    }
                }
            }
            return mainModel::sweet_alert($alert);
        }
        public function show_user_controller(){
            $usuario_id=mainModel::decryption($_POST['usuario_id']);
            $usuario_id=mainModel::clean_chain($usuario_id);
            $query=usuarioModels::show_user_model($usuario_id);
            $rspta= $query->fetch();
            echo json_encode($rspta);
        }   
    } 