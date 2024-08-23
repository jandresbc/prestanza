<?php
    require_once "database/database.php";
    require_once "utils.php";

    $db = new Database();
    $utils = new Utils($db);

    #Simulaciones a las cuales debo enviarles las comunicaciones
    $consult = $db->query("select idSimulacionHipotecas,email, max(Fecha_simulacion) as ultima_simulacion, idUsuario
from simulacion_hipotecas inner join usuario ON usuario.IdUsusario = simulacion_hipotecas.IdUsuario
where simulacion_hipotecas.estado = 0 and simulacion_hipotecas.condicion = 0 and simulacion_hipotecas.garantias <> 1 and DATEDIFF('2023-01-23',Fecha_simulacion) < 90
GROUP BY email;");

    foreach($consult as $valor){
        $comunicaciones = $db->query("SELECT Idcom, canal, Identificador, Ref1, cuerpo, Canal, TipoComunicacion FROM sys_comunicaciones WHERE Tipo=3 AND TipoComunicacion='Comercial' order by canal;");

        foreach($comunicaciones as $value){
            print_r("Procesando: ".$valor->idSimulacionHipotecas."\n");
            // Consulta para obtener el seguimiento de comunicaciones
            $seguimiento = $db->query("select * from (
                select identificador, count(identificador) as nro_envios_comunicacion, idsimulacion, fecha_envio from sys_comunicaciones_registros
                GROUP BY identificador,idsimulacion) tabla1 where nro_envios_comunicacion >= 2 and identificador = '".$value->Identificador."' and idsimulacion = ".$valor->idSimulacionHipotecas.";");
            
            if(count($seguimiento) == 0){
                // Consulta para obtener el seguimiento de comunicaciones
                $ultima_comunicacion = $db->query("
                    select identificador, idsimulacion, fecha_envio from sys_comunicaciones_registros
where idsimulacion = ".$valor->idSimulacionHipotecas." order by fecha_envio DESC limit 1");

                if (isset($ultima_comunicacion[0]->fecha_envio)) {
                    print_r("Nro dias último envio: ". (new DateTime())->diff(new DateTime($ultima_comunicacion[0]->fecha_envio))->days."\n");
                    // Solo se envia si ha pasado un dia
                    if((new DateTime())->diff(new DateTime($ultima_comunicacion[0]->fecha_envio))->days >= 1){
                        $msj = $utils->send($value->Idcom,$valor->idSimulacionHipotecas,$value->Identificador,$value->Canal,$value->TipoComunicacion,$valor->idUsuario,"Email");

                        if($msj){
                            print_r("mensaje enviado\n");
                        }
                        break;
                    }
                }else{
                    $msj = $utils->send($value->Idcom,$valor->idSimulacionHipotecas,$value->Identificador,$value->Canal,$value->TipoComunicacion,$valor->idUsuario,"Email");
                    
                    if($msj){
                        print_r("mensaje enviado\n");
                    }
                }
            }

        }
    }
?>