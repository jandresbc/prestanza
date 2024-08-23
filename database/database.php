<?php

    class database{

        private $HOST = "2.59.150.4";
        private $USER = "u899984956_prueba_julio";
        private $PASS = "Prueba@2024";
        private $DATABASE = "u899984956_prueba_julio";

        private function connect(){
            $mysqli = new mysqli($this->HOST, $this->USER, $this->PASS, $this->DATABASE);
            if ($mysqli->connect_errno) {
                echo "Fallo al conectar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
            }

            return $mysqli;
        }

        public function query($query){
            $db = $this->connect();

            $consult = $db->query($query);
            $data = [];

            while($obj = $consult->fetch_object()){
                array_push($data,$obj);
            }

            $consult->close();

            return $data;
        }

        public function insert($query){
            $db = $this->connect();

            return $db->query($query);
        }

    }

?>