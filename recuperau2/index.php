
<?php
header("Content-Type","application/json");
$method = $_SERVER["REQUEST_METHOD"];
echo $method; 

switch($method){
    case 'GET':
        echo "CONSULTANDO REGISTROS \n";

        try{ 
            $conexion = new PDO("mysql:host=localhost;dbname=evaluation","root","osmich05");
        }catch(PDOException $e){
            echo $e->getMessage();
        }

        switch ($_GET['accion']){ 
            case "docentes":

                echo "DOCENTES\n---------------\n";
                    $pstm = $conexion->prepare('SELECT * FROM teachers;');
                    $pstm->execute(); 
                    $rs = $pstm->fetchAll(PDO::FETCH_ASSOC);
                    if($rs!=null)
                        echo json_encode($rs, JSON_PRETTY_PRINT); 
                    else
                        echo 'No hay registro de docentes';

                break;
            case "estudiantes" :
                echo "ESTUDIANTES\n---------------\n";
                // Query
                $pstm = $conexion->prepare('SELECT students.*, teachers.name as profesor FROM evaluation.students inner join teachers on teachers_id_teachers= id_teachers;');
                $pstm->execute(); 
                $rs = $pstm->fetchAll(PDO::FETCH_ASSOC); 
                if($rs!=null)
                    echo json_encode($rs, JSON_PRETTY_PRINT); 
                else
                    echo 'No hay registro de alumnos';

                break;

            case "calificacion" :
                echo "CALIFICACIÓN\n---------------\n";
                // Query
                $pstm = $conexion->prepare('SELECT name,subject,grade FROM evaluation.students');
                $pstm->execute(); 
                $rs = $pstm->fetchAll(PDO::FETCH_ASSOC); 
                if($rs!=null)
                    echo json_encode($rs, JSON_PRETTY_PRINT); 
                else
                    echo 'No hay registro de alumnos';

                break;


            case "promedio" :
                echo "PROMEDIO\n---------------\n";
                // Query
                $pstm = $conexion->prepare('SELECT sum(grade)/count(id_students) as PromedioEstudiantes FROM evaluation.students;');
                $pstm->execute(); 
                $rs = $pstm->fetchAll(PDO::FETCH_ASSOC); 
                if($rs!=null)
                    echo json_encode($rs, JSON_PRETTY_PRINT); 
                else
                    echo 'No hay registro ';

                break;
         
            default:
                echo "No se ha encontrado este dato";
                break;
        }
        break;
    



    case 'POST':
        if($_GET['accion']=='estudiante'){
            $jsonData = json_decode(file_get_contents("php://input")); 
            try{
                $conn = new PDO("mysql:host=localhost;dbname=evaluation","root","osmich05");
            }catch(PDOException $e){
                echo $e->getMessage();
            }

            if (validateStudent($conn,$jsonData)==null){                
                $query = $conn->prepare('INSERT INTO `evaluation`.`students`(`name`,`surname`,`date_bth`,`curp`,`license`,`subject`,`grade`,`teachers_id_teachers`)
                VALUES(:name,:surname,:date_bth,:curp,:license,:subject,:grade,:id_teachers);');
                
                $query->bindParam(":name",$jsonData->name);
                $query->bindParam(":surname",$jsonData->surname);
                $query->bindParam(":date_bth",$jsonData->date_bth);
                $query->bindParam(":curp",$jsonData->curp);
                $query->bindParam(":license",$jsonData->license);
                $query->bindParam(":subject",$jsonData->subject);
                $query->bindParam(":grade",$jsonData->grade);
                $query->bindParam(":id_teachers",$jsonData->teachers_id_teachers);
                $result = $query->execute();
                if($result){
                    $_POST["error"] = false;
                    $_POST["message"] = "Estudiante registrado correctamente.";
                    $_POST["status"] = 200;
                }else{
                    $_POST["error"] = true;
                    $_POST["message"] = "Error al registrar";
                    $_POST["status"] = 400;
                }

                echo json_encode($_POST);
            }else{
                echo "Ya existente --------------\n ";
            }

        }elseif($_GET['accion']=='docente'){

            $jsonData = json_decode(file_get_contents("php://input")); 
            try{
                $conn = new PDO("mysql:host=localhost;dbname=evaluation","root","osmich05");
            }catch(PDOException $e){
                echo $e->getMessage();
            }

            if (validateTeachers($conn,$jsonData)==null){  
                
                $query = $conn->prepare('INSERT INTO `evaluation`.`teachers`(`name`,`surname`,`date_bth`,`curp`,`number_employ`)VALUES(:name,:surname,:date_bth,:curp,:number_employ);');                 
                $query->bindParam(":name",$jsonData->name);
                $query->bindParam(":surname",$jsonData->surname);
                $query->bindParam(":date_bth",$jsonData->date_bth);
                $query->bindParam(":curp",$jsonData->curp);
                $query->bindParam(":number_employ",$jsonData->number_employ);
                $result = $query->execute();
                if($result){
                    $_POST["error"] = false;
                    $_POST["message"] = "Docente registrado con éxito";
                    $_POST["status"] = 200;
                }else{
                    $_POST["error"] = true;
                    $_POST["message"] = "Error al registrar";
                    $_POST["status"] = 400;
                }

                echo json_encode($_POST);
            }else{
                echo "Ya existente -----------------\n ";
            }
        }else{
            $_POST["error"] = true;
            $_POST["message"] = "Acción inválida";
            $_POST["status"] = 400;
            echo json_encode($_POST);
        }
        break;

    case 'PUT':
        echo "ACTUALIZAR REGISTROS \n";

        if($_GET['accion']=='estudiante'){
            $jsonData = json_decode(file_get_contents("php://input")); 
            try{
                $conn = new PDO("mysql:host=localhost;dbname=evaluation","root","osmich05");
            }catch(PDOException $e){
                echo $e->getMessage();
            }

            if (validateStudent($conn,$jsonData)!=null){                
              
                $query = $conn->prepare('UPDATE `evaluation`.`students` SET `name` = :name, `surname` = :surname, `date_bth` = :date_bth, `curp` = :curp, `teachers_id_teachers` = :id_teachers, `subject` = :subject, `grade` = :grade WHERE (`license` = :license);                ');  
                
                $query->bindParam(":name",$jsonData->name);
                $query->bindParam(":surname",$jsonData->surname);
                $query->bindParam(":date_bth",$jsonData->date_bth);
                $query->bindParam(":curp",$jsonData->curp);
                $query->bindParam(":license",$jsonData->license);
                $query->bindParam(":subject",$jsonData->subject);
                $query->bindParam(":grade",$jsonData->grade);
                $query->bindParam(":id_teachers",$jsonData->teachers_id_teachers);
                $result = $query->execute();
                if($result){
                    $_POST["error"] = false;
                    $_POST["message"] = "Estudiante actualizado con éxito";
                    $_POST["status"] = 200;
                }else{
                    $_POST["error"] = true;
                    $_POST["message"] = "Error al registrar";
                    $_POST["status"] = 400;
                }

                echo json_encode($_POST);
            }else{
                echo "Ya existente -----------------\n ";
            }

        }elseif($_GET['accion']=='docente'){

            $jsonData = json_decode(file_get_contents("php://input")); 
            try{
                $conn = new PDO("mysql:host=localhost;dbname=evaluation","root","osmich05");
            }catch(PDOException $e){
                echo $e->getMessage();
            }

            if (validateTeachers($conn,$jsonData)!=null){  

                $query = $conn->prepare('UPDATE `evaluation`.`teachers` SET `name` = :name , `surname` = :surname, `date_bth` = :date_bth, `curp` = :curp WHERE (`number_employ` = :number_employ);'); 
     
                               
                $query->bindParam(":name",$jsonData->name);
                $query->bindParam(":surname",$jsonData->surname);
                $query->bindParam(":date_bth",$jsonData->date_bth);
                $query->bindParam(":curp",$jsonData->curp);
                $query->bindParam(":number_employ",$jsonData->number_employ);
                $result = $query->execute();
                if($result){
                    $_POST["error"] = false;
                    $_POST["message"] = "Docente registrado con éxito";
                    $_POST["status"] = 200;
                }else{
                    $_POST["error"] = true;
                    $_POST["message"] = "Error al registrar";
                    $_POST["status"] = 400;
                }

                echo json_encode($_POST);
            }else{
                echo "Ya existente -----------------\n ";
            }
        }else{
            $_POST["error"] = true;
            $_POST["message"] = "Acción inválida";
            $_POST["status"] = 400;
            echo json_encode($_POST);
        }

        break;



    default:
        echo "ACCIÓN INVÁLIDA";
        break;
}



function validateStudent($conexion,$json  ){
    // Query
    $pstm = $conexion->prepare('SELECT * FROM evaluation.students where license=:matricula OR curp=:curp');
    $pstm->bindParam(":matricula",$json->license );
    $pstm->bindParam(":curp",$json->curp );
    $pstm->execute(); //execute to do the function
    $rs = $pstm->fetchAll(PDO::FETCH_ASSOC); // check the dates
    return $rs;
}



function validateTeachers($conexion,$json  ){
    // Query
    $pstm = $conexion->prepare('SELECT * FROM evaluation.teachers where curp=:curp OR number_employ=:n_employ;');
    $pstm->bindParam(":curp",$json->curpñ );
    $pstm->bindParam(":n_employ",$json->number_employ );
    $pstm->execute(); //execute to do the function
    $rs = $pstm->fetchAll(PDO::FETCH_ASSOC); // check the dates
    return $rs;
}