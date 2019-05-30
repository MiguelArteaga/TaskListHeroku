<?php

$db = parse_url(getenv("DATABASE_URL"));

$pdo = new PDO("pgsql:" . sprintf(
    "host=%s;port=%s;user=%s;password=%s;dbname=%s",
    $db["host"],
    $db["port"],
    $db["user"],
    $db["pass"],
    ltrim($db["path"], "/")
));


$stmt = $pdo->query("SELECT * FROM tareas");

echo "<h1>Task List</h1>";
echo"<form method='POST' action='/'>";
echo"<label>Nueva Tarea</label>";
echo "<input type='text' name='tareaNueva'>";
echo"<input type='submit' value='Insertar' name='botonInsertar'>";	
echo "</form>";


if (isset($_POST['botonInsertar'])) {
    $nuevaTarea1=$_POST['tareaNueva'];
    $sentencia = $pdo->prepare("INSERT INTO tareas (tareas) VALUES (:tarea)");
    $sentencia->bindParam(':tarea', $tarea);

    $tarea = $nuevaTarea1;
    $sentencia->execute();
    header("Location: /");
}

echo"<ul>";
echo"<form method='POST' action='/'>";
while ($row = $stmt->fetch()) {
	echo "<li>";
    echo $row['tareas'];
    echo"<input type='button' value='Eliminar' name='botonEliminar' onclick=location.href='/?id=".$row['id']."'         >";
    echo "</li>";
}
echo "</ul>";
echo "</form>";


$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$id=explode('=', $url);


if (isset($id)) {
    $id1=$id[1];

    $sentencia = $pdo->prepare("DELETE FROM tareas WHERE id=:id");
    $sentencia->bindParam(':id',$id1);
    $sentencia->execute();
    if($sentencia->rowCount()==0){
       echo " ";
    }else{
        header("Location: /"); 
        echo "ENTRAS!!!!!!!!";
    }
    


    
}


?>
