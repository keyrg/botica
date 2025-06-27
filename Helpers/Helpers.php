<?php
    
    //Retorna url del proyecto
    function base_url()
    {
        return BASE_URL;
    }

    //Retorna url del assets
    function media()
    {
        return BASE_URL."/Assets";
    }
    
    //Formato de moneda
    function formatMoney($quanty)
{
    $quanty = (float)($quanty ?? 0); // Se asegura de que no sea NULL
    return number_format($quanty, 2, SPD, SPM);
}


    //Desencriptar
    function decryption($string)
    {

        $key=hash('sha256', SECRET_KEYC);
        $iv=substr(hash('sha256', SECRET_IVC), 0, 16);
        $output=openssl_decrypt(base64_decode($string), METHODC, $key, 0, $iv);
        return $output;
        
    }

    //Encriptar
    function encryption($string)
    {

        $output=FALSE;
        $key=hash('sha256', SECRET_KEYC);
        $iv=substr(hash('sha256', SECRET_IVC), 0, 16);
        $output=openssl_encrypt($string, METHODC, $key, 0, $iv);
        $output=base64_encode($output);
        return $output;
        
    }

    //Calcular edad
    function calcularedad($fechanacimiento)
    {
        list($ano,$mes,$dia) = explode("-",$fechanacimiento);
        $ano_diferencia  = date("Y") - $ano;
        $mes_diferencia = date("m") - $mes;
        $dia_diferencia   = date("d") - $dia;
        if ($dia_diferencia < 0 || $mes_diferencia < 0)
          $ano_diferencia--;
        return $ano_diferencia;
    }

    function conocerDiaSemanaFecha($fecha) {

        $dias = array('Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado');
    
        $dia = $dias[date('w', strtotime($fecha))];
    
        return $dia;
    
    }
    //Calcular fecha en letras
    function obtenerFechaEnLetra($fecha)
    {

        $dia= conocerDiaSemanaFecha($fecha);
    
        $num = date("j", strtotime($fecha));
    
        $anno = date("Y", strtotime($fecha));
    
        $mes = array('enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre');
    
        $mes = $mes[(date('m', strtotime($fecha))*1)-1];
    
        return $dia.', '.$num.' de '.$mes.' del '.$anno;
    
    }