<?php

    class Utils{

        private $db = null;

        function __construct($db){
            $this->db = $db;
        }

        public function puedeEjecutarAccion() {
            // Obtener la fecha y hora actual
            $fecha_hora_actual = new DateTime();
        
            // Obtener el día de la semana (1 = lunes, 7 = domingo)
            $dia_semana = (int)$fecha_hora_actual->format('N');
        
            // Obtener la hora actual (formato 24 horas)
            $hora_actual = (int)$fecha_hora_actual->format('H');
        
            // Verificar si el día de la semana está dentro del rango (lunes a sábado)
            if ($dia_semana >= 1 && $dia_semana <= 6) {
                // Verificar si la hora está dentro del rango (8:00 AM a 9:00 PM)
                if ($hora_actual >= 8 && $hora_actual <= 21) {
                    return true; // Se puede ejecutar la acción
                }
            }
        
            return false; // No se puede ejecutar la acción
        }

        public function send($idCom,$idsimulacion,$Identificador,$canal,$TipoComunicacion,$idusuario,$Destinatarios){
            if ($this->puedeEjecutarAccion()) {
                $sqlInsert = "INSERT INTO sys_comunicaciones_registros (
                    enlace,
                    idsimulacion,
                    identificador,
                    tipo,
                    idusuario,
                    observaciones,
                    condicion,
                    fecha,
                    fecha_envio
                ) VALUES ( '/gestion-comunicaciones/controller/controller-sys-comunicacionesenvios.php?id=$idsimulacion&Identificador=$Identificador&Tip=3&Canal=$canal&Tipo=3&Destinatario=$Destinatarios&TipoComunicacion=$TipoComunicacion&IdCom=&IdMovi=&IdOp=&IdUser=$idusuario',
                    $idsimulacion,
                    '$Identificador',
                    3,
                    $idusuario,
                    '',
                    '0',
                    NOW(),
                    NOW()
                );";

                $val = $this->db->insert($sqlInsert);

                if($val == 1){
                    return true;
                }else{
                    return false;
                }
            } else {
                print_r ("No se puede ejecutar la acción en este momento.");
            }
        }
    }

?>