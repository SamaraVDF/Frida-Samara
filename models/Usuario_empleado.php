<?php

class UsuarioEmpleado{
    private mysqli $conn;

    public function __construct(mysqli $conexion){
        $this->conn=$conexion;
    }

    //CREAR USUARI
    public function nuevoUsuarioEmpleado(string $nombre,string $apellido, string $puesto, string $password ){
        $hash=password_hash($password,PASSWORD_BCRYPT);
        if($hash==false){
            return"Error: no se puedo generar el hash";
        }

        $sql="INSERT INTO empleado(nombre,apellido,puesto,Contrasena)
        VALUES(?,?,?,?)";
        $stm=$this->conn->prepare($sql);
        if(!$stm) return "Error: " .$this->conn->error;

        $stm->bind_param('ssss',$nombre,$apellido,$puesto,$hash);
        if(!$stm->execute()){
            return "Error a registrar un empleado: " .$stm->error;
        }

        return "Empleado Registrado";
    }
    
    //LOGIN
    public function loginUsuarioEmpleado(string $usuario, string $password){
        $sql="SELECT idEmpleado,nombre,apellido,puesto,Contrasena
        FROM empleado
        WHERE nombre=? 
        LIMIT 1";
    
    $stm=$this->conn->prepare($sql);
    if(!$stm) return ["Error: "=>'Error de preparacion' .$this->conn->error];

    $stm->bind_param('s',$usuario);
    if (!$stm->execute()) {
        return ["error" => "Error en la ejecución: " . $stm->error];
    }

    $res=$stm->get_result();
    $row=$res->fetch_assoc();

    if(!$row){
        return ['Error'=>'Usario no ecnontrado o contraseña incorrecta'];
    }

    $hash=$row['Contrasena'] ?? '';

    if (!password_verify($password, $hash)) {
        return ["error" => "Contraseña inválida"];
    }

    unset($row['Contrasena']);

    return ["ok" => true, "user" => $row];

    }
    //MOSTRAR DATOS DEL USUARIO
    public function datosUsuarioEmpleado(int $id) {
    $sql = "SELECT 
                nombre,
                apellido,
                puesto
            FROM empleado
            WHERE idEmpleado=?";

    $stm = $this->conn->prepare($sql);
    if (!$stm) {
        return ["error" => "Error de preparación: " . $this->conn->error];
    }

    $stm->bind_param('i', $id);

    if (!$stm->execute()) {
        return ["error" => "Error al acceder a los datos del empleado: " . $stm->error];
    }

    $res = $stm->get_result();
    $row = $res->fetch_assoc();

    if (!$row) {
        return ["error" => "Empleado no encontrado"];
    }

    return ["ok" => true, "empleado" => $row];
}


}