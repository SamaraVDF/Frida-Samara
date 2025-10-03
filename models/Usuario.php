<?php

class Usuario{
    private mysqli $conn;

    public function __construct(mysqli $conexion){
        $this->conn=$conexion;
    }
    
    //REGISTRAR NUEVO USUARIO
    public function nuevoUsuario(string $nombre, string $apellido, int $edad,int $telefono, string $password){

        $hash=password_hash($password,PASSWORD_BCRYPT);
        if($hash===false){
            return "Error: no se pudo generar el hash de la de contrseña";
        }

        $sql="INSERT INTO usuario(nombre,apellido,edad,telefono,Contrasena)
        VALUES(?,?,?,?,?)";
        $stm=$this->conn->prepare($sql);
        if(!$stm) return "Error: " .$this->conn->error;

        $stm->bind_param('ssiis',$nombre,$apellido,$edad,$telefono,$hash);
        if (!$stm->execute()) {
            return "Error al registrar usuario: ".$stm->error;
        }

        return "Usuario registrado";
    }

    //LOGIN
    public function loginUsuario(string $usuario, string $password) {
    $sql = "SELECT idUsuario,nombre, apellido, edad, telefono, Contrasena 
            FROM usuario 
            WHERE nombre = ?
            LIMIT 1";

    $stm = $this->conn->prepare($sql);
    if (!$stm) {
        return ["error" => "Error de preparación: " . $this->conn->error];
    }

    $stm->bind_param('s', $usuario);

    if (!$stm->execute()) {
        return ["error" => "Error en la ejecución: " . $stm->error];
    }

    $res = $stm->get_result();
    $row = $res->fetch_assoc();

    if (!$row) {
        return ["error" => "Usuario no encontrado"];
    }

    $hash = $row['Contrasena'] ?? '';

    if (!password_verify($password, $hash)) {
        return ["error" => "Contraseña inválida"];
    }

    unset($row['Contrasena']);

    return ["ok" => true, "user" => $row];
}

    //MOSTRAR DATOS DEL USUARIO
    public function datosUsuario(int $id){
        $sql="SELECT 
        nombre,
        apellido,
        telefono,
        edad
        FROM usuario
        WHERE idUsuario=?";

        $stm=$this->conn->prepare($sql);
        if(!$stm) return 'Error: '.$this->conn->error;

        $stm->bind_param('i',$id);
        if(!$stm->execute()){
            return "Error al acceder a los datos del usuario" .$stm->error;
        }

        $res = $stm->get_result();
        $row = $res->fetch_assoc();

        if (!$row) {
            return ["error" => "Usuario no encontrado"];
        }

        return ["ok" => true, "usuario" => $row];
    }

}