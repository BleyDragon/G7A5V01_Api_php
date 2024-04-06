<?php

$host = "localhost";
$usuario = "root";
$password = "";
$basededatos = "api";

// Conexión a la base de datos
$conn = mysqli_connect($host, $usuario, $password, $basededatos);

// Chequear la conexión
if (!$conn) {
    die("Conexión fallida: " . mysqli_connect_error());
}

// Establecer el encabezado de respuesta como JSON
header("Content-Type: application/json");

// Obtener el método de la solicitud
$metodo = $_SERVER["REQUEST_METHOD"];

// Obtener el ID de la URL si está presente
$path = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/';
$buscarID = explode('/', $path);
$id = ($path != '/') ? end($buscarID) : null;

// Manejar la solicitud según el método
switch ($metodo) {
    case 'GET':
        // Consultar usuarios
        consultar($conn, $id);
        break;
    case 'POST':
        // Insertar un nuevo usuario
        insertar($conn);
        break;
    case 'PUT':
        // Actualizar un usuario existente
        actualizar($conn, $id);
        break;
    case 'DELETE':
        // Borrar un usuario existente
        borrar($conn, $id);
        break;
    default:
        // Método no permitido
        echo json_encode(array("error" => "Método no permitido"));
        break;
}

// Función para consultar usuarios
function consultar($conn, $id)
{
    $sql = ($id !== null) ? "SELECT * FROM usuarios WHERE id = $id" : "SELECT * FROM usuarios";
    $resultado = $conn->query($sql);

    if ($resultado) {
        $datos = array();
        while ($fila = $resultado->fetch_assoc()) {
            $datos[] = $fila;
        }
        echo json_encode($datos);
    } else {
        echo json_encode(array("error" => "Error al consultar usuarios: " . mysqli_error($conn)));
    }
}

// Función para insertar un usuario
function insertar($conn)
{
    $dato = json_decode(file_get_contents("php://input"), true);
    $id = isset($dato["id"]) ? (int)$dato["id"] : null;
    $nombre = isset($dato["nombre"]) ? mysqli_real_escape_string($conn, $dato["nombre"]) : '';

    if ($id !== null && $nombre !== '') {
        $sql = "INSERT INTO usuarios(id, nombre) VALUES ($id, '$nombre')";
        $resultado = $conn->query($sql);

        if ($resultado) {
            echo json_encode(array("id" => $id));
        } else {
            echo json_encode(array("error" => "Error al insertar el usuario: " . mysqli_error($conn)));
        }
    } else {
        echo json_encode(array("error" => "ID o nombre de usuario no proporcionado"));
    }
}

// Función para actualizar un usuario
function actualizar($conn, $id)
{
    // Obtener los datos del cuerpo de la solicitud en formato JSON
    $data = json_decode(file_get_contents("php://input"), true);

    // Verificar si se proporcionó el ID y los nuevos datos
    if (isset($data['id']) && isset($data['nuevosDatos'])) {
        $id = $data['id'];
        $nuevosDatos = $data['nuevosDatos'];

        // Construir la consulta SQL para actualizar los datos del usuario
        $sets = [];
        foreach ($nuevosDatos as $campo => $valor) {
            $sets[] = "$campo = '$valor'";
        }
        $setString = implode(', ', $sets);
        $sql = "UPDATE usuarios SET $setString WHERE id = $id";

        // Ejecutar la consulta SQL
        $resultado = $conn->query($sql);

        // Verificar si se actualizó el usuario correctamente
        if ($resultado) {
            echo json_encode(array("mensaje" => "Usuario actualizado correctamente"));
        } else {
            echo json_encode(array("error" => "Error al actualizar el usuario: " . mysqli_error($conn)));
        }
    } else {
        echo json_encode(array("error" => "ID o nuevos datos no proporcionados"));
    }
}


// Función para borrar un usuario
/*function borrar($conn, $id)
{
    // Verificar si se proporcionó el ID
    if ($id !== null) {
        // Usar una consulta preparada para evitar inyección SQL
        $sql = "DELETE FROM usuarios WHERE id = ?";
        
        // Preparar la consulta
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            // Vincular el parámetro
            $stmt->bind_param("i", $id);
            
            // Ejecutar la consulta
            if ($stmt->execute()) {
                echo json_encode(array("mensaje" => "Usuario eliminado correctamente"));
            } else {
                echo json_encode(array("error" => "Error al eliminar el usuario: " . $stmt->error));
            }
            
            // Cerrar la declaración preparada
            $stmt->close();
        } else {
            // Si no se pudo preparar la consulta
            echo json_encode(array("error" => "Error al preparar la consulta: " . $conn->error));
        }
    } else {
        // Si no se proporcionó el ID de usuario
        echo json_encode(array("error" => "ID de usuario no proporcionado"));
    }
}*/
function borrar($conn,$id){
    echo "el id a borrar es: ". $id;
    $sql="DELETE FROM usuarios where id= $id ";
    $resultado = $conn->query($sql);
    if($resultado){
        echo json_encode(array("Mensaje"=>"Usuario borrado correctamente"));
    }else{
        echo json_encode(array("error"=>"Error al borrar usuario"));
    }
}

?>
