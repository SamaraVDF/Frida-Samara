<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ .'/../models/Usuario_empleado.php';

class UsuarioEmpleadoController{
    private UsuarioEmpleado $model;

    public function __construct(mysqli $conn){
        $this->model=new UsuarioEmpleado($conn);
        header('Content-Type:application/json; charset=utf-8');
    }

    /*POST*/
    public function registrarEmpleado(){
        $data=$this->input();

        if(!$this->valid($data,['nombre','apellido','puesto','password'])){
            http_response_code(422);
            echo json_encode(['error'=>'Campos requiridos:nombre,apellido,puesto,password']);
            return;
        }

        $id=isset($data['id']) ? (int)$data['id']:null;

        $msg=$this->model->nuevoUsuarioEmpleado(
            $data['nombre'],
            $data['apellido'],
            $data['puesto'],
            $data['password']
        );
        http_response_code(str_starts_with((string)$msg, 'Error') ? 400 : 201);
        echo json_encode(['message' => $msg]);
    }

    /*POST LOGIN EMPLEADO*/
    public function loginEmpleado(){
        $data=$this->input();

        if(!$this->valid($data,['usuario','password'])){
            http_response_code(422);
            echo json_encode(['error: '=>'campos requeridos:usuario,password']);
        }
        $id=isset($data['id']) ? (int)$data['id']:null;
        $msg=$this->model->loginUsuarioEmpleado(
            $data['usuario'],
            $data['password']
        );
        if (isset($msg['error'])) {
            http_response_code(401); 
        } else {
            http_response_code(200);
        }
        echo json_encode($msg);
    }

    /**GET DATOS DEL EMPLEADO */
    public function datosEmpleado(){
        $id=$_GET['id'] ?? null;

        if(!$id){
            http_response_code(422);
            echo json_encode(['error: '=>'campos requeridos:id']);
            return;
        }
        
        $msg=$this->model->datosUsuarioEmpleado(
            (int)$id
        );

        if (isset($msg['error'])) {
            http_response_code(401); 
        } else {
            http_response_code(200);
        }
        echo json_encode($msg);

    }
    
    private function input(): array {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        $raw = file_get_contents('php://input');

        if (stripos($contentType, 'application/json') !== false) {
            $data = json_decode($raw, true);
            return is_array($data) ? $data : [];
        }

    
        if (!empty($_POST)) {
            return $_POST;
        }

    
        $data = json_decode($raw, true);
        return is_array($data) ? $data : [];
    }

    private function valid(array &$data, array $required): bool {
        foreach ($required as $field) {
            if (!array_key_exists($field, $data)) return false;

            if (is_string($data[$field])) {
                $data[$field] = trim($data[$field]);
            }
            if ($data[$field] === '' || $data[$field] === null) return false;
        }


        return true;
    }

    
}