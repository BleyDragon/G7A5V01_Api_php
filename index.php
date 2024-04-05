 <?php

$host="localhost";
$usuario="root";
$password="";
$basededatos="api";

//Prueba de conexion

$conn = mysqli_connect($host, $usuario, $password, $basededatos);

//Chequear la conexion
if(!$conn){
    die("conexion fallida:" . mysqli_connect_error());
}
/*echo "Conectado correctamente a MySQL";
mysqli_close($conn);*/

header("Content-Type: application/json");
$metodo = $_SERVER[ "REQUEST_METHOD" ];
//print_r($metodo); 

//buscar la URL de nuestra API con el id respectivo
$path = isset($_SERVER['PATH_INFO']) ? $_SERVER['path_info'] : '/';
$buscarID = explode('/', $path);
$id = ($path!='/') ? end($buscarID):null;

switch($metodo){
    //select
    case 'GET':
       /* echo "Consulta de Registros - GET";*/
        consultar($conn, $id);
        break;
    //INSERTAR
    case "POST":
       /* echo"Insertar Registros - POST";*/
        insertar($conn);
        break;
    //UPDATE
    case 'PUT':
        /*echo "Edición de Registros - PUT";*/
        actualizar($conn, $id);
        break;
    //DELETE 
    case 'DELETE':
       /* echo "Eliminación de Registro - DELETE";*/
        borrar($conn,$id); 
        break;
    default:
        echo "Método no permitido";
        break;
}
function consultar($conn, $id){
    //$sql = "SELECT * from usuarios"; /*si busco por usuario uo esta */
    $sql = ($id===null) ? "SELECT * from usuarios": "SELECT * from usuarios where id =$id";
    $resultado = $conn->query($sql);

    if($resultado){
        $datos = array();
        while($fila = $resultado->fetch_assoc()){
            $datos[] = $fila;
        }
        echo json_encode($datos);
    }

}

function insertar($conn) {
    // Obtener datos del cuerpo de la solicitud en formato JSON
    $dato = json_decode(file_get_contents("php://input"), true);

    // Verificar si se proporciona el nombre del usuario
    if (isset($dato["nombre"])) {
        // Escapar el nombre del usuario para prevenir la inyección SQL
        $nombre = mysqli_real_escape_string($conn, $dato["nombre"]);

        // Construir la consulta SQL para insertar el nuevo usuario
        $sql = "INSERT INTO usuarios(nombre) VALUES ('$nombre')";

        // Ejecutar la consulta SQL
        $resultado = $conn->query($sql);

        // Comprobar si la inserción fue exitosa
        if ($resultado) {
            // Obtener el ID del usuario recién insertado
            $id_insertado = $conn->insert_id;
            // Construir una respuesta JSON con el ID del nuevo usuario
            echo json_encode(array("id" => $id_insertado));
        } else {
            // En caso de error en la consulta SQL, devolver un mensaje de error
            echo json_encode(array("error" => "Error al insertar el usuario: " . mysqli_error($conn)));
        }
    } else {
        // Si no se proporciona el nombre del usuario, devolver un mensaje de error
        echo json_encode(array("error" => "Nombre de usuario no proporcionado"));
    }
}

/*function insertar($conn){

    $dato = json_decode(file_get_contents("php://input"),true);
    $nombre = isset($dato["nombre"]) ? mysqli_real_escape_string($conn, $dato["nombre"]) : '';
    // borrar
    /*if(array_key_exists("nombre", $dato)){
        $nombre=$dato["nombre"];
    }else{
        $nombre= " ";
    }*/
    //borrar

   /* $sql = "INSERT INTO usuarios(nombre) VALUES ('$nombre')";
    $resultado = $conn-> query ($sql);

    if($resultado){
        $dato["id"] = $conn->insert_id;
        echo json_encode($dato);
    }else{
        echo json_encode(array("error"=>"Error al crear usuario"));
    }
}*/

/*
function borrar($conn,$id){

    echo "el id a borrar es: ". $id;

    $sql="DELETE FROM usuarios where id= $id ";
    $resultado = $conn->query($sql);

    if($resultado){
        echo json_encode(array("Mensaje"=>"Usuario borrado corectamente"));
    }else{
        echo json_encode(array("error"=>"Error al borrar usuario"));
    }
}
*/
function borrar($conn, $id) {
    // Construir la consulta SQL para eliminar el usuario con el ID proporcionado
    $sql = "DELETE FROM usuarios WHERE id = $id";

    // Ejecutar la consulta SQL
    $resultado = $conn->query($sql);

    // Comprobar si la eliminación fue exitosa
    if ($resultado) {
        // Construir una respuesta JSON con un mensaje de éxito
        echo json_encode(array("mensaje" => "Usuario eliminado correctamente"));
    } else {
        // En caso de error en la consulta SQL, devolver un mensaje de error
        echo json_encode(array("error" => "Error al eliminar el usuario: " . mysqli_error($conn)));
    }
}


/*
function actualizar($conn, $id){

    $dato = json_decode(file_get_contents("php://input"),true);
    $nombre = isset($dato["nombre"]) ? mysqli_real_escape_string($conn, $dato["nombre"]) : '';

        echo "El ida editar es: ".$id. "con el dato ".$nombre;

        $sql="UPDATE usuarios SET nombre = '$nombre' WHERE id = $id";
        $resultado = $conn->query($sql);

    if($resultado){
        echo json_encode(array("Mensaje"=>"Datos Usuario Actualizados"));
    }else{
        echo json_encode(array("error"=>"No se pudo actualizar los datos"));
       
    }
}
*/
function actualizar($conn, $id) {
    // Obtener datos del cuerpo de la solicitud en formato JSON
    $dato = json_decode(file_get_contents("php://input"), true);

    // Verificar si se proporciona el nombre del usuario
    if (isset($dato["nombre"])) {
        // Escapar el nombre del usuario para prevenir la inyección SQL
        $nombre = mysqli_real_escape_string($conn, $dato["nombre"]);

        // Construir la consulta SQL para actualizar el nombre del usuario
        $sql = "UPDATE usuarios SET nombre = '$nombre' WHERE id = $id";

        // Ejecutar la consulta SQL
        $resultado = $conn->query($sql);

        // Comprobar si la actualización fue exitosa
        if ($resultado) {
            // Construir una respuesta JSON con un mensaje de éxito
            echo json_encode(array("mensaje" => "Usuario actualizado correctamente"));
        } else {
            // En caso de error en la consulta SQL, devolver un mensaje de error
            echo json_encode(array("error" => "Error al actualizar el usuario: " . mysqli_error($conn)));
        }
    } else {
        // Si no se proporciona el nombre del usuario, devolver un mensaje de error
        echo json_encode(array("error" => "Nombre de usuario no proporcionado"));
    }
}

