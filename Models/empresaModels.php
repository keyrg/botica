<?php
    if($ajaxRequest){
        require_once "../Core/mainModel.php";
    }else{
        require_once "./Core/mainModel.php";
    }

    class empresaModels extends mainModel{

        protected function update_empresa_model($data){
            $sql=mainModel::connect()->prepare("UPDATE empresa SET 
            empresa_nombre=:Nombre,
            empresa_ruc=:Ruc,
            empresa_celular=:Celular,
            empresa_direccion=:Direccion,
            empresa_correo=:Correo,
            empresa_impuesto=:Impuesto,
            empresa_impuestoValor=:ImpuestoValor,
            empresa_moneda=:Moneda,
            empresa_simbolo=:Simbolo
            WHERE empresa_id =:Codigo");
            $sql->bindParam(":Nombre",$data['Nombre']);
            $sql->bindParam(":Ruc",$data['Ruc']);
            $sql->bindParam(":Celular",$data['Celular']);
            $sql->bindParam(":Direccion",$data['Direccion']);
            $sql->bindParam(":Correo",$data['Correo']);
            $sql->bindParam(":Impuesto",$data['Impuesto']);
            $sql->bindParam(":ImpuestoValor",$data['ImpuestoValor']);
            $sql->bindParam(":Moneda",$data['Moneda']);
            $sql->bindParam(":Simbolo",$data['Simbolo']);
            $sql->bindParam(":Codigo",$data['Codigo']);
            $sql->execute();
            return $sql;
        }
        protected function update_logo_model($data){
            $sql=mainModel::connect()->prepare("UPDATE empresa SET 
            empresa_logo=:Logo WHERE empresa_id=:Codigo");
            $sql->bindParam(":Logo",$data['Logo']);
            $sql->bindParam(":Codigo",$data['Codigo']);
            $sql->execute();
            return $sql;
        }
        protected function list_empresa_model(){ 
            $sql=mainModel::connect()->prepare("SELECT * FROM empresa");
            $sql->execute();
            return $sql;
        }
        protected function show_empresa_model($code){ 
            $sql=mainModel::connect()->prepare("SELECT * FROM empresa  WHERE empresa_id=:Code");
            $sql->bindParam("Code",$code);
            $sql->execute();
            return $sql;
        }
        protected function mostrar_serie_model($code){ 
    // Validación del parámetro de entrada
    if(empty($code) || !is_numeric($code)) {
        throw new Exception("El código del comprobante debe ser un número válido");
    }

    $sql = mainModel::connect()->prepare("SELECT comprobante_letraSerie, comprobante_serie FROM comprobante WHERE comprobante_id = :Code");
    $sql->bindParam(":Code", $code, PDO::PARAM_INT);
    $sql->execute();
    
    return $sql;
}
        protected function mostrar_numero_model($code){ 
            $sql=mainModel::connect()->prepare("SELECT (COUNT(venta_id)+1)as numero FROM venta where venta_id_comprobante=:Code");
            $sql->bindParam("Code",$code);
            $sql->execute();
            return $sql;
        }
        protected function mostrar_impuesto_model(){ 
            $sql=mainModel::connect()->prepare("SELECT empresa_impuestoValor FROM empresa");
            $sql->execute();
            return $sql;
        }
        protected function nombre_impuesto_model(){ 
            $sql=mainModel::connect()->prepare("SELECT empresa_impuesto FROM empresa");
            $sql->execute();
            return $sql;
        }
        protected function mostrar_simbolo_model(){
            $sql=mainModel::connect()->prepare("SELECT empresa_simbolo FROM empresa");
            $sql->execute();
            return $sql;
        }
        /**  voucher **/
        public function datos_empresa_model(){ 
            $sql=mainModel::connect()->prepare("SELECT * FROM empresa");
            $sql->execute();
            return $sql;
        }
    }