<?php
if($ajaxRequest){
    require_once "../Core/mainModel.php";
}else{
    require_once "./Core/mainModel.php";
}

class empresaModels extends mainModel{

    protected function update_empresa_model($data){
        try {
            $sql = mainModel::connect()->prepare("UPDATE empresa SET 
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
            
            $sql->bindParam(":Nombre", $data['Nombre']);
            $sql->bindParam(":Ruc", $data['Ruc']);
            $sql->bindParam(":Celular", $data['Celular']);
            $sql->bindParam(":Direccion", $data['Direccion']);
            $sql->bindParam(":Correo", $data['Correo']);
            $sql->bindParam(":Impuesto", $data['Impuesto']);
            $sql->bindParam(":ImpuestoValor", $data['ImpuestoValor']);
            $sql->bindParam(":Moneda", $data['Moneda']);
            $sql->bindParam(":Simbolo", $data['Simbolo']);
            $sql->bindParam(":Codigo", $data['Codigo']);
            
            $sql->execute();
            return $sql;
            
        } catch(PDOException $e) {
            error_log("Error en update_empresa_model: " . $e->getMessage());
            return false;
        }
    }

    protected function update_logo_model($data){
        try {
            $sql = mainModel::connect()->prepare("UPDATE empresa SET 
                empresa_logo=:Logo WHERE empresa_id=:Codigo");
            $sql->bindParam(":Logo", $data['Logo']);
            $sql->bindParam(":Codigo", $data['Codigo']);
            $sql->execute();
            return $sql;
            
        } catch(PDOException $e) {
            error_log("Error en update_logo_model: " . $e->getMessage());
            return false;
        }
    }

    protected function list_empresa_model(){ 
        try {
            $sql = mainModel::connect()->prepare("SELECT * FROM empresa");
            $sql->execute();
            return $sql;
            
        } catch(PDOException $e) {
            error_log("Error en list_empresa_model: " . $e->getMessage());
            return false;
        }
    }

    protected function show_empresa_model($code){ 
        try {
            if(empty($code)) {
                throw new Exception("Código de empresa no proporcionado");
            }
            
            $sql = mainModel::connect()->prepare("SELECT * FROM empresa WHERE empresa_id=:Code");
            $sql->bindParam(":Code", $code);
            $sql->execute();
            return $sql;
            
        } catch(PDOException $e) {
            error_log("Error en show_empresa_model: " . $e->getMessage());
            return false;
        } catch(Exception $e) {
            error_log("Error en show_empresa_model: " . $e->getMessage());
            return false;
        }
    }

    protected function mostrar_serie_model($code){ 
        try {
            if(empty($code)) {
                throw new Exception("Código de comprobante no proporcionado");
            }
            
            $sql = mainModel::connect()->prepare("SELECT comprobante_letraSerie, comprobante_serie FROM comprobante WHERE comprobante_id=:Code");
            $sql->bindParam(":Code", $code);
            $sql->execute();
            
            if($sql->rowCount() == 0) {
                throw new Exception("No se encontró el comprobante con ID: " . $code);
            }
            
            return $sql;
            
        } catch(PDOException $e) {
            error_log("Error en mostrar_serie_model: " . $e->getMessage());
            return false;
        } catch(Exception $e) {
            error_log("Error en mostrar_serie_model: " . $e->getMessage());
            return false;
        }
    }

    protected function mostrar_numero_model($code){ 
        try {
            if(empty($code)) {
                throw new Exception("Código de comprobante no proporcionado");
            }
            
            $sql = mainModel::connect()->prepare("SELECT (COUNT(venta_id)+1) as numero FROM venta WHERE venta_id_comprobante=:Code");
            $sql->bindParam(":Code", $code);
            $sql->execute();
            return $sql;
            
        } catch(PDOException $e) {
            error_log("Error en mostrar_numero_model: " . $e->getMessage());
            return false;
        } catch(Exception $e) {
            error_log("Error en mostrar_numero_model: " . $e->getMessage());
            return false;
        }
    }

    protected function mostrar_impuesto_model(){ 
        try {
            $sql = mainModel::connect()->prepare("SELECT empresa_impuestoValor FROM empresa");
            $sql->execute();
            return $sql;
            
        } catch(PDOException $e) {
            error_log("Error en mostrar_impuesto_model: " . $e->getMessage());
            return false;
        }
    }

    protected function nombre_impuesto_model(){ 
        try {
            $sql = mainModel::connect()->prepare("SELECT empresa_impuesto FROM empresa");
            $sql->execute();
            return $sql;
            
        } catch(PDOException $e) {
            error_log("Error en nombre_impuesto_model: " . $e->getMessage());
            return false;
        }
    }

    protected function mostrar_simbolo_model(){
        try {
            $sql = mainModel::connect()->prepare("SELECT empresa_simbolo FROM empresa");
            $sql->execute();
            return $sql;
            
        } catch(PDOException $e) {
            error_log("Error en mostrar_simbolo_model: " . $e->getMessage());
            return false;
        }
    }

    /** voucher **/
    public function datos_empresa_model(){ 
        try {
            $sql = mainModel::connect()->prepare("SELECT * FROM empresa");
            $sql->execute();
            return $sql;
            
        } catch(PDOException $e) {
            error_log("Error en datos_empresa_model: " . $e->getMessage());
            return false;
        }
    }
}