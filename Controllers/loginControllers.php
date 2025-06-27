<?php
if($ajaxRequest){
    require_once "../Models/loginModels.php";
}else{
    require_once "./Models/loginModels.php";
}

class loginControllers extends loginModels{

    public function login_controller(){
        // Iniciar sesión si no está activa
        if (session_status() === PHP_SESSION_NONE) {
            session_start(['name' => 'STR']);
        }

        // Limpiar y obtener credenciales
        $username = mainModel::clean_chain($_POST['usuario']);
        $password = mainModel::clean_chain($_POST['clave']);
        $password = mainModel::encryption($password);

        // Debug
        error_log("[LOGIN DEBUG] Intento de login - Usuario: ".$username);
        error_log("[LOGIN DEBUG] Password encriptado: ".$password);

        // Verificar usuario y contraseña en una sola consulta segura
        $dataLogin = [
            "Username" => $username,
            "Pass" => $password
        ];
        
        $dataAccount = loginModels::login_model($dataLogin);

        if($dataAccount->rowCount() == 1){ 
            $row = $dataAccount->fetch();

            // Registrar en bitácora
            $current_date = date("Y-m-d");
            $current_year = date("Y");
            $current_time = date("h:i:s a");

            $query1 = mainModel::run_simple_query("SELECT bitacora_id FROM bitacora");
            $number = ($query1->rowCount()) + 1;
            $code_binnacle = mainModel::random_code("BIT-", 6, $number);

            $dataBinnacle = [
                "Code" => $code_binnacle,
                "Date" => $current_date,
                "StartTime" => $current_time,
                "EndTime" => "sin registro",
                "Type" => $row['usuario_cargo'],
                "Year" => $current_year,
                "AccountCode" => $row['usuario_id']
            ];

            $addBinnacle = mainModel::save_binnacle($dataBinnacle);
            
            if($addBinnacle->rowCount() >= 1) {
                $query3 = mainModel::run_simple_query("SELECT * FROM empresa");
                $company = $query3->fetch();

                // Configurar variables de sesión
                $_SESSION['name_str'] = $row['usuario_nombre'];
                $_SESSION['lastname_str'] = $row['usuario_apellido'];
                $_SESSION['dni_str'] = $row['usuario_dni'];
                $_SESSION['mobile_str'] = $row['usuario_celular'];
                $_SESSION['username_str'] = $row['usuario_login'];
                $_SESSION['type_str'] = $row['usuario_cargo'];
                $_SESSION['gender_str'] = $row['usuario_genero'];
                $_SESSION['profession_str'] = $row['usuario_profesion'];
                $_SESSION['birthdate_str'] = $row['usuario_fechanacimiento'];
                $_SESSION['description_str'] = $row['usuario_descripcion'];
                $_SESSION['image_str'] = $row['usuario_perfil'];
                $_SESSION['token_str'] = md5(uniqid(mt_rand(), true));
                $_SESSION['code_user_str'] = $row['usuario_codigo'];
                $_SESSION['id_user_str'] = $row['usuario_id'];
                $_SESSION['code_binnacle_str'] = $code_binnacle;
                $_SESSION['company_str'] = $company['empresa_nombre'];
                $_SESSION['logotype_str'] = $company['empresa_logo'];
                $_SESSION['simbolo_str'] = $company['empresa_simbolo'];

                // Redirección segura usando HTTPS
                $base_url = 'https://botica-532h.onrender.com/';
                $url = ($row['usuario_cargo'] == "Administrador") 
                      ? $base_url.'dashboard/' 
                      : $base_url.'product_catalog/';
                
                return '<script>window.location="'.$url.'"</script>';
            } else {
                return $this->show_error("Error al registrar en bitácora");
            }
        } else {
            return $this->show_error("Usuario o contraseña incorrectos");
        }
    }

    public function logout_controller(){
        session_start(['name'=>'STR']);
        $token = mainModel::decryption($_GET['Token']);
        $End_Time = date("h:i:s a");
        
        $data = [
            "Username" => $_SESSION['username_str'],
            "Token_User" => $_SESSION['token_str'],
            "Token" => $token,
            "Code_binnacle" => $_SESSION['code_binnacle_str'],
            "End_Time" => $End_Time
        ];

        $result = loginModels::logout_model($data);
        
        if($result == "true") {
            session_unset();
            session_destroy();
            return '<script>window.location.href="https://botica-532h.onrender.com/login/"</script>';
        } else {
            return $this->show_error("Error al cerrar sesión");
        }
    }

    public function force_logoff_controller(){
        session_unset();
        session_destroy();
        return '<script>window.location.href="https://botica-532h.onrender.com/login/"</script>';
    }

    public function redirect_user_controller($type){
        $url = 'https://botica-532h.onrender.com/';
        $url .= ($type == "Administrador") ? 'dashboard/' : 'product_catalog/';
        return '<script>window.location.href="'.$url.'"</script>';
    }

    private function show_error($message){
        $alert = [
            "Alert" => "simple",
            "title" => "Ocurrió un error",
            "text" => $message,
            "icon" => "error"
        ];
        return mainModel::sweet_alert($alert);
    }
}