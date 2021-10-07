<?php
session_start();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app = new \Slim\App;
$conexion = '../datos/config/conexion.php';
//GET Consultar municipio por ID de dpto
$app->get('/res/municipios/{id}', function (Request $request, Response $response) {
    $id_dpto = $request->getAttribute('id');
    include $GLOBALS['conexion'];
    try {
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "SELECT * FROM seg_municipios WHERE id_departamento = '$id_dpto' ORDER BY nom_municipio";
        $rs = $cmd->query($sql);
        $municipios = $rs->fetchAll();
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getCode());
    }
    if (!empty($municipios)) {
        echo json_encode($municipios);
    } else {
        echo json_encode('0');
    }
});
//GET Consultar dpto
$app->get('/res/dptos', function (Request $request, Response $response) {
    include $GLOBALS['conexion'];
    try {
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "SELECT * FROM seg_departamento ORDER BY nombre_dpto";
        $rs = $cmd->query($sql);
        $dpto = $rs->fetchAll();
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getCode());
    }
    if (!empty($dpto)) {
        echo json_encode($dpto);
    } else {
        echo json_encode('0');
    }
});
//GET Consultar tipo de documento
$app->get('/res/tipo/identificacion', function (Request $request, Response $response) {
    include $GLOBALS['conexion'];
    try {
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "SELECT * FROM seg_tipos_documento";
        $rs = $cmd->query($sql);
        $tipodoc = $rs->fetchAll();
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getCode());
    }
    if (!empty($tipodoc)) {
        echo json_encode($tipodoc);
    } else {
        echo json_encode('0');
    }
});
//Consultar actividades economicas
$app->get('/res/lista/actividades', function (Request $request, Response $response) {
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "SELECT * FROM seg_actividades_economicas";
        $rs = $cmd->query($sql);
        $actividades = $rs->fetchAll();
        if (!empty($actividades)) {
            echo json_encode($actividades);
        } else {
            echo json_encode('0');
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getCode());
    }
});
//Consultar responsabilidades economicas
$app->get('/res/lista/responsabilidades', function (Request $request, Response $response) {
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "SELECT * FROM seg_responsabilidades_tributarias";
        $rs = $cmd->query($sql);
        $responsabilidad = $rs->fetchAll();
        if (!empty($responsabilidad)) {
            echo json_encode($responsabilidad);
        } else {
            echo json_encode('0');
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getCode());
    }
});
$app->get('/res/login/{id}', function (Request $request, Response $response) {
    $ids = $request->getAttribute('id');
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $sql = "SELECT 
                    *
                FROM seg_terceros
                WHERE cc_nit  = '$ids'";
        $rs = $cmd->query($sql);
        $tercero = $rs->fetch();
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getCode());
    }
    if (!empty($tercero)) {
        echo json_encode($tercero);
    } else {
        echo json_encode('0');
    }
});
$app->get('/res/lista/{id}', function (Request $request, Response $response) {
    $ids = $request->getAttribute('id');
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $sql = "SELECT 
                    *
                FROM
                    seg_terceros
                INNER JOIN seg_pais 
                    ON (seg_terceros.pais = seg_pais.id_pais)
                INNER JOIN seg_departamento 
                    ON (seg_departamento.id_pais = seg_pais.id_pais) AND (seg_terceros.departamento = seg_departamento.id_dpto)
                INNER JOIN seg_municipios 
                    ON (seg_municipios.id_departamento = seg_departamento.id_dpto) AND (seg_terceros.municipio = seg_municipios.id_municipio)
                WHERE cc_nit  IN ($ids)";
        $rs = $cmd->query($sql);
        $terceros = $rs->fetchAll();
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getCode());
    }
    if (!empty($terceros)) {
        echo json_encode($terceros);
    } else {
        echo json_encode('0');
    }
});
//GET Datos UP por ID
$app->get('/res/lista/datos_up/{id}', function (Request $request, Response $response) {
    $id = $request->getAttribute('id');
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $sql = "SELECT
                    id_tercero, tipo_doc, cc_nit, apellido1, apellido2, nombre1, nombre2, razon_social, pais, departamento, municipio, direccion, telefono, correo, genero, fec_nacimiento
                FROM
                    seg_terceros
                WHERE cc_nit = '$id'";
        $rs = $cmd->query($sql);
        $tercero = $rs->fetch();
        if (!empty($tercero)) {
            echo json_encode($tercero);
        } else {
            echo json_encode('0');
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getCode());
    }
});
//GET Datos por id de tercero
$app->get('/res/datos/id/{id}', function (Request $request, Response $response) {
    $id = $request->getAttribute('id');
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $sql = "SELECT
                    *
                FROM
                    seg_terceros
                WHERE id_tercero = '$id'";
        $rs = $cmd->query($sql);
        $tercero = $rs->fetch();
        if (!empty($tercero)) {
            echo json_encode($tercero);
        } else {
            echo json_encode('0');
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getCode());
    }
});
//POST Nuevo tercero
$app->post('/res/nuevo', function (Request $request, Response $response) {
    $genero = $request->getParam('slcGenero');
    $fecNacimiento = date('Y-m-d', strtotime($request->getParam('datFecNacimiento')));
    $tipodoc = $request->getParam('slcTipoDocEmp');
    $cc_nit = $request->getParam('txtCCempleado');
    $nomb1 = $request->getParam('txtNomb1Emp');
    $nomb2 = $request->getParam('txtNomb2Emp');
    $ape1 = $request->getParam('txtApe1Emp');
    $ape2 = $request->getParam('txtApe2Emp');
    $razonsoc = $request->getParam('txtRazonSocial');
    $pais = $request->getParam('slcPaisEmp');
    $dpto = $request->getParam('slcDptoEmp');
    $municip = $request->getParam('slcMunicipioEmp');
    $dir = $request->getParam('txtDireccion');
    $mail = $request->getParam('mailEmp');
    $tel = $request->getParam('txtTelEmp');
    $contrasena = $request->getParam('passT');
    $iduser = $request->getParam('id_user');
    $tipouser = 'user';
    $docreg = $request->getParam('nit_emp');
    $pass = $request->getParam('pass');
    $date = new DateTime('now', new DateTimeZone('America/Bogota'));
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "INSERT INTO seg_terceros(genero, fec_nacimiento, tipo_doc, cc_nit, nombre1, nombre2, apellido1, apellido2, razon_social, pais, departamento, municipio, direccion, correo, telefono, id_user_reg, password, tipo_user_reg, fec_reg, doc_reg) "
            . "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $sql = $cmd->prepare($sql);
        $sql->bindParam(1, $genero, PDO::PARAM_STR);
        $sql->bindParam(2, $fecNacimiento, PDO::PARAM_STR);
        $sql->bindParam(3, $tipodoc, PDO::PARAM_INT);
        $sql->bindParam(4, $cc_nit, PDO::PARAM_STR);
        $sql->bindParam(5, $nomb1, PDO::PARAM_STR);
        $sql->bindParam(6, $nomb2, PDO::PARAM_STR);
        $sql->bindParam(7, $ape1, PDO::PARAM_STR);
        $sql->bindParam(8, $ape2, PDO::PARAM_STR);
        $sql->bindParam(9, $razonsoc, PDO::PARAM_STR);
        $sql->bindParam(10, $pais, PDO::PARAM_INT);
        $sql->bindParam(11, $dpto, PDO::PARAM_INT);
        $sql->bindParam(12, $municip, PDO::PARAM_INT);
        $sql->bindParam(13, $dir, PDO::PARAM_STR);
        $sql->bindParam(14, $mail, PDO::PARAM_STR);
        $sql->bindParam(15, $tel, PDO::PARAM_STR);
        $sql->bindParam(16, $iduser, PDO::PARAM_INT);
        $sql->bindParam(17, $pass, PDO::PARAM_STR);
        $sql->bindParam(18, $tipouser, PDO::PARAM_STR);
        $sql->bindValue(19, $date->format('Y-m-d H:i:s'));
        $sql->bindParam(20, $docreg, PDO::PARAM_STR);

        $sql->execute();
        if ($cmd->lastInsertId() > 0) {
            echo json_encode('1');
        } else {
            echo json_encode(print_r($sql->errorInfo()[2]));
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getCode());
    }
});
//PUT modificar contraseña tercero
$app->put('/res/modificar/pass', function (Request $request, Response $response) {
    $newpass = $request->getParam('newpass');
    $idter = $request->getParam('idter');
    $iduser = $request->getParam('iduser');
    $tipuser = $request->getParam('tipuser');
    $date = new DateTime('now', new DateTimeZone('America/Bogota'));
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "UPDATE seg_terceros SET password = ? WHERE id_tercero = ?";
        $sql = $cmd->prepare($sql);
        $sql->bindParam(1, $newpass, PDO::PARAM_STR);
        $sql->bindParam(2, $idter, PDO::PARAM_INT);
        $sql->execute();
        $cambio = $sql->rowCount();
        if (!($sql->execute())) {
            echo json_encode(print_r($sql->errorInfo()[2]));
        } else {
            if ($cambio > 0) {
                $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
                $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
                $sql = "UPDATE seg_terceros SET  id_user_act = ?, tipo_user_act = ? ,fec_act = ? WHERE id_tercero = ?";
                $sql = $cmd->prepare($sql);
                $sql->bindParam(1, $iduser, PDO::PARAM_INT);
                $sql->bindParam(2, $tipuser, PDO::PARAM_STR);
                $sql->bindValue(3, $date->format('Y-m-d H:i:s'));
                $sql->bindParam(4, $idter, PDO::PARAM_INT);
                $sql->execute();
                if ($sql->rowCount() > 0) {
                    echo json_encode('1');
                } else {
                    echo json_encode(print_r($sql->errorInfo()[2]));
                }
            } else {
                echo json_encode('No se ingresó ningún dato nuevo');
            }
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getCode());
    }
});
//PUT Modificar tercero
$app->put('/res/modificar/tercero/{id}', function (Request $request, Response $response) {
    $idter =  $request->getAttribute('id');

    $genero = $request->getParam('slcGenero');
    $fecNacimiento = date('Y-m-d', strtotime($request->getParam('datFecNacimiento')));
    $tipodoc = $request->getParam('slcTipoDocEmp');
    $cc_nit = $request->getParam('txtCCempleado');
    $nomb1 = $request->getParam('txtNomb1Emp');
    $nomb2 = $request->getParam('txtNomb2Emp');
    $ape1 = $request->getParam('txtApe1Emp');
    $ape2 = $request->getParam('txtApe2Emp');
    $razonsoc = $request->getParam('txtRazonSocial');
    $pais = $request->getParam('slcPaisEmp');
    $dpto = $request->getParam('slcDptoEmp');
    $municip = $request->getParam('slcMunicipioEmp');
    $dir = $request->getParam('txtDireccion');
    $mail = $request->getParam('mailEmp');
    $tel = $request->getParam('txtTelEmp');
    $iduser =  $request->getParam('id_user');
    $tipuser = $request->getParam('tipuser');;
    $nit_act =  $request->getParam('nit_emp');;
    $date = new DateTime('now', new DateTimeZone('America/Bogota'));
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "UPDATE seg_terceros SET genero = ?, fec_nacimiento = ?, tipo_doc = ?, cc_nit = ?, nombre1 = ?, nombre2 = ?, apellido1 = ?, apellido2 = ?, razon_social = ?, pais = ?, departamento = ?, municipio = ?, direccion = ?, correo = ?, telefono = ? WHERE id_tercero = ?";
        $sql = $cmd->prepare($sql);
        $sql->bindParam(1, $genero, PDO::PARAM_STR);
        $sql->bindParam(2, $fecNacimiento, PDO::PARAM_STR);
        $sql->bindParam(3, $tipodoc, PDO::PARAM_INT);
        $sql->bindParam(4, $cc_nit, PDO::PARAM_STR);
        $sql->bindParam(5, $nomb1, PDO::PARAM_STR);
        $sql->bindParam(6, $nomb2, PDO::PARAM_STR);
        $sql->bindParam(7, $ape1, PDO::PARAM_STR);
        $sql->bindParam(8, $ape2, PDO::PARAM_STR);
        $sql->bindParam(9, $razonsoc, PDO::PARAM_STR);
        $sql->bindParam(10, $pais, PDO::PARAM_INT);
        $sql->bindParam(11, $dpto, PDO::PARAM_INT);
        $sql->bindParam(12, $municip, PDO::PARAM_INT);
        $sql->bindParam(13, $dir, PDO::PARAM_STR);
        $sql->bindParam(14, $mail, PDO::PARAM_STR);
        $sql->bindParam(15, $tel, PDO::PARAM_STR);
        $sql->bindParam(16, $idter, PDO::PARAM_INT);
        $sql->execute();
        $cambio = $sql->rowCount();
        if (!($sql->execute())) {
            echo json_encode(print_r($sql->errorInfo()[2]));
        } else {
            if ($cambio > 0) {
                $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
                $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
                $sql = "UPDATE seg_terceros SET  id_user_act = ?, tipo_user_act = ? , doc_act = ?, fec_act = ? WHERE id_tercero = ?";
                $sql = $cmd->prepare($sql);
                $sql->bindParam(1, $iduser, PDO::PARAM_INT);
                $sql->bindParam(2, $tipuser, PDO::PARAM_STR);
                $sql->bindParam(3, $nit_act, PDO::PARAM_STR);
                $sql->bindValue(4, $date->format('Y-m-d H:i:s'));
                $sql->bindParam(5, $idter, PDO::PARAM_INT);
                $sql->execute();
                if ($sql->rowCount() > 0) {
                    echo json_encode('1');
                } else {
                    echo json_encode(print_r($sql->errorInfo()[2]));
                }
            } else {
                echo json_encode('No se ingresó datos nuevos');
            }
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getCode());
    }
});

//PUT Nuevo Resposabilidad Tercero
$app->PUT('/res/nuevo/responsabilidad', function (Request $request, Response $response) {
    $idt = $request->getParam("id_terero");
    $id_resp_econ = $request->getParam("id_responsabilidad");
    $iduser = $request->getParam("id_user");
    $tipouser = $request->getParam("tipo_user");
    $doc_reg = $request->getParam("nit_reg");
    $date = new DateTime('now', new DateTimeZone('America/Bogota'));
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "SELECT  * FROM seg_responsabilidades_terceros
                WHERE id_tercero = '$idt' AND id_responsabilidad = '$id_resp_econ'";
        $rs = $cmd->query($sql);
        $resposabilidad = $rs->fetchAll();
        $cmd = null;
        if (!empty($resposabilidad)) {
            echo json_encode('Resposabilidad Económica ya se encuentra registrada');
        } else {
            try {
                $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
                $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
                $sql = "INSERT INTO seg_responsabilidades_terceros(id_tercero, id_responsabilidad, id_user_reg, tipo_user_reg, doc_reg, fec_reg) VALUES (?, ?, ?, ?, ?, ?)";
                $sql = $cmd->prepare($sql);
                $sql->bindParam(1, $idt, PDO::PARAM_INT);
                $sql->bindParam(2, $id_resp_econ, PDO::PARAM_INT);
                $sql->bindParam(3, $iduser, PDO::PARAM_INT);
                $sql->bindParam(4, $tipouser, PDO::PARAM_STR);
                $sql->bindParam(5, $doc_reg, PDO::PARAM_STR);
                $sql->bindValue(6, $date->format('Y-m-d H:i:s'));
                $sql->execute();
                if ($cmd->lastInsertId() > 0) {
                    echo json_encode('1');
                } else {
                    echo json_encode(print_r($sql->errorInfo()[2]));
                }
                $cmd = null;
            } catch (PDOException $e) {
                echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getCode());
            }
        }
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getCode());
    }
});
//PUT actualizar estado responsabilidad
$app->PUT('/res/modificar/estado/responsabilidad', function (Request $request, Response $response) {
    $estado = $request->getParam("estado");
    $idter = $request->getParam("idter");
    $iduser = $request->getParam("iduser");
    $tipuser = $request->getParam("tipuser");
    $date = new DateTime('now', new DateTimeZone('America/Bogota'));
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "UPDATE seg_responsabilidades_terceros SET estado = ?, fec_act = ?, id_user_act = ?, tipo_user_act = ? WHERE id_resptercero = ?";
        $sql = $cmd->prepare($sql);
        $sql->bindParam(1, $estado);
        $sql->bindValue(2, $date->format('Y-m-d H:i:s'));
        $sql->bindParam(3, $iduser);
        $sql->bindParam(4, $tipuser);
        $sql->bindParam(5, $idter);
        $sql->execute();
        if ($sql->rowCount() > 0) {
            echo json_encode($estado);
        } else {
            echo json_encode($sql->errorInfo()[2]);
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getCode());
    }
});
//PUT actualizar estado actividad
$app->PUT('/res/modificar/estado/actividad', function (Request $request, Response $response) {
    $estado = $request->getParam("estado");
    $idter = $request->getParam("idter");
    $iduser = $request->getParam("iduser");
    $tipuser = $request->getParam("tipuser");
    $date = new DateTime('now', new DateTimeZone('America/Bogota'));
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "UPDATE seg_actividad_terceros SET estado = ?, fec_act = ?, id_user_act = ?, tipo_user_act = ? WHERE id_actvtercero = ?";
        $sql = $cmd->prepare($sql);
        $sql->bindParam(1, $estado);
        $sql->bindValue(2, $date->format('Y-m-d H:i:s'));
        $sql->bindParam(3, $iduser);
        $sql->bindParam(4, $tipuser);
        $sql->bindParam(5, $idter);
        $sql->execute();
        if ($sql->rowCount() > 0) {
            echo json_encode($estado);
        } else {
            json_encode($sql->errorInfo()[2]);
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getCode());
    }
});
//PUT Nuevo Actividad Tercero
$app->put('/res/nuevo/actividad', function (Request $request, Response $response) {
    $idt = $request->getParam("id_tercero");
    $id_actv_econ = $request->getParam("id_actividad");
    $finic = $request->getParam("finic");
    $iduser = $request->getParam("id_user");
    $tipouser = $request->getParam("tipo_user");
    $doc_reg = $request->getParam("nit_reg");
    $date = new DateTime('now', new DateTimeZone('America/Bogota'));
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "SELECT  * FROM seg_actividad_terceros
            WHERE id_tercero = '$idt' AND id_actividad = '$id_actv_econ'";
        $rs = $cmd->query($sql);
        $actividad = $rs->fetchAll();
        $cmd = null;
        if (!empty($actividad)) {
            echo json_encode('Actividad Económica ya se encuentra registrada');
        } else {
            try {
                $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
                $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
                $sql = "INSERT INTO seg_actividad_terceros(id_tercero, id_actividad, fec_inicio, id_user_reg, tipo_user_reg, doc_reg, fec_reg) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $sql = $cmd->prepare($sql);
                $sql->bindParam(1, $idt, PDO::PARAM_INT);
                $sql->bindParam(2, $id_actv_econ, PDO::PARAM_INT);
                $sql->bindParam(3, $finic, PDO::PARAM_STR);
                $sql->bindParam(4, $iduser, PDO::PARAM_INT);
                $sql->bindParam(5, $tipouser, PDO::PARAM_STR);
                $sql->bindParam(6, $doc_reg, PDO::PARAM_STR);
                $sql->bindValue(7, $date->format('Y-m-d H:i:s'));
                $sql->execute();
                if ($cmd->lastInsertId() > 0) {
                    echo json_encode('1');
                } else {
                    print_r($sql->errorInfo()[2]);
                }
                $cmd = null;
            } catch (PDOException $e) {
                echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getCode());
            }
        }
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getCode());
    }
});
//Detalles de tercero resposabilidad economica
$app->get('/res/lista/resp_econ/{id}', function (Request $request, Response $response) {
    $id = $request->getAttribute('id');
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $sql = "SELECT 
                    id_resptercero, seg_responsabilidades_terceros.id_responsabilidad, codigo,  descripcion, estado
                FROM
                    seg_responsabilidades_terceros
                INNER JOIN seg_responsabilidades_tributarias 
                    ON (seg_responsabilidades_terceros.id_responsabilidad = seg_responsabilidades_tributarias.id_responsabilidad)
                WHERE id_tercero = '$id'";
        $rs = $cmd->query($sql);
        $responsabilidades = $rs->fetchAll();
        if (!empty($responsabilidades)) {
            echo json_encode($responsabilidades);
        } else {
            echo json_encode('0');
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getCode());
    }
});
//Detalles de tercero actividad economica
$app->get('/res/lista/actv_econ/{id}', function (Request $request, Response $response) {
    $id = $request->getAttribute('id');
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $sql = "SELECT 
                id_actvtercero, id_tercero, codigo_ciiu, descripcion, fec_inicio, estado
            FROM
                seg_actividad_terceros
            INNER JOIN seg_actividades_economicas 
                ON (seg_actividad_terceros.id_actividad = seg_actividades_economicas.id_actividad)
            WHERE id_tercero = '$id'";
        $rs = $cmd->query($sql);
        $actvidades = $rs->fetchAll();
        if (!empty($actvidades)) {
            echo json_encode($actvidades);
        } else {
            echo json_encode('0');
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getCode());
    }
});
//PUT Nuevo Documento Tercero
$app->put('/res/nuevo/documento', function (Request $request, Response $response) {
    $idt = $request->getParam("idt");
    $tipodoc = $request->getParam("tipodoc");
    $fini = $request->getParam("fini");
    $fvig = $request->getParam("fvig");
    $iduser = $request->getParam("iduser");
    $tipuser = $request->getParam("tipuser");
    $nom_archivo = $request->getParam("nom_archivo");
    $temporal = $request->getParam("temporal");
    $temporal = base64_decode($temporal);
    $date = new DateTime('now', new DateTimeZone('America/Bogota'));
    try {
        include $GLOBALS['conexion'];
        $ruta = '../../uploads/terceros/docs/' . $idt . '/';
        if (!file_exists($ruta)) {
            $ruta = mkdir('../../uploads/terceros/docs/' . $idt . '/', 0777, true);
            $ruta = '../../uploads/terceros/docs/' . $idt . '/';
        }
        $res = file_put_contents("$ruta/$nom_archivo", $temporal);
        if (false !== $res) {
            $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
            $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
            $sql = "INSERT INTO seg_docs_tercero(id_tercero, id_tipo_doc, fec_inicio, fec_vig, ruta_doc, nombre_doc, id_user_reg, tipo_user_reg, fec_reg)
                VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $sql = $cmd->prepare($sql);
            $sql->bindParam(1, $idt, PDO::PARAM_INT);
            $sql->bindParam(2, $tipodoc, PDO::PARAM_INT);
            $sql->bindParam(3, $fini, PDO::PARAM_STR);
            $sql->bindParam(4, $fvig, PDO::PARAM_STR);
            $sql->bindParam(5, $ruta, PDO::PARAM_STR);
            $sql->bindParam(6, $nom_archivo, PDO::PARAM_STR);
            $sql->bindParam(7, $iduser, PDO::PARAM_INT);
            $sql->bindParam(8, $tipuser, PDO::PARAM_STR);
            $sql->bindValue(9, $date->format('Y-m-d H:i:s'));
            $sql->execute();
            if ($cmd->lastInsertId() > 0) {
                echo json_encode('1');
            } else {
                echo json_encode(print_r($sql->errorInfo()[2]));
            }
        } else {
            echo json_encode('No se pudo adjuntar el archivo');
        }
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getCode());
    }
});
//Descargar documentos
$app->get('/res/descargar/docs/{id}', function (Request $request, Response $response) {
    $id = $request->getAttribute('id');
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $sql = "SELECT 
                    *
                FROM
                    seg_tipo_docs_tercero";
        $rs = $cmd->query($sql);
        $tipo_docs = $rs->fetchAll();
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $sql = "SELECT 
                    id_docster, ruta_doc, nombre_doc
                FROM
                    seg_docs_tercero
                WHERE id_docster = '$id'";
        $rs = $cmd->query($sql);
        $docs = $rs->fetch();
        $ruta = $docs['ruta_doc'] . $docs['nombre_doc'];
        $tipo = explode("_", $docs['nombre_doc']);
        $archivo = file_get_contents($ruta);
        $tip = $tipo[0];
        $key = array_search($tip, array_column($tipo_docs, 'id_doc'));
        if (false !== $key) {
            $res['tipo'] =  strtolower($tipo_docs[$key]['descripcion']);
        } else {
            $res['tipo'] = 'descarga';
        }
        $res['file'] = base64_encode($archivo);
        if (!empty($docs)) {
            echo json_encode($res);
        } else {
            echo json_encode('0');
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getCode());
    }
});
//IDs documentos de tercero
$app->get('/res/lista/docs/{id}', function (Request $request, Response $response) {
    $id = $request->getAttribute('id');
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $sql = "SELECT
                    seg_docs_tercero.id_tercero, seg_docs_tercero.id_tipo_doc, seg_docs_tercero.fec_vig, seg_terceros.cc_nit
                FROM
                    seg_docs_tercero
                INNER JOIN seg_terceros 
                        ON (seg_docs_tercero.id_tercero = seg_terceros.id_tercero)
                WHERE cc_nit = '$id'";
        $rs = $cmd->query($sql);
        $docs = $rs->fetchAll();
        if (!empty($docs)) {
            echo json_encode($docs);
        } else {
            echo json_encode('0');
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getCode());
    }
});

//Listar documenos por id de tercero
$app->get('/res/listar/docs/{id}', function (Request $request, Response $response) {
    $id = $request->getAttribute('id');
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $sql = "SELECT
                    id_docster, id_tercero, id_tipo_doc, descripcion, fec_inicio, fec_vig, ruta_doc, nombre_doc
                FROM
                    seg_docs_tercero
                INNER JOIN seg_tipo_docs_tercero 
                    ON (seg_docs_tercero.id_tipo_doc = seg_tipo_docs_tercero.id_doc)
                WHERE id_Tercero = '$id'";
        $rs = $cmd->query($sql);
        $docs = $rs->fetchAll();
        if (!empty($docs)) {
            echo json_encode($docs);
        } else {
            echo json_encode('0');
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getCode());
    }
});
//Lista tipo documenos por id de tercero
$app->get('/res/listar/tipo/docs', function (Request $request, Response $response) {
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $sql = "SELECT * FROM seg_tipo_docs_tercero";
        $rs = $cmd->query($sql);
        $tipo = $rs->fetchAll();
        if (!empty($tipo)) {
            echo json_encode($tipo);
        } else {
            echo json_encode('0');
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getCode());
    }
});
//Lista datos documenos por id 
$app->get('/res/lista/documento/{id}', function (Request $request, Response $response) {
    $idDoc = $request->getAttribute('id');
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $sql = "SELECT *
                FROM
                    seg_docs_tercero
                INNER JOIN seg_tipo_docs_tercero 
                    ON (seg_docs_tercero.id_tipo_doc = seg_tipo_docs_tercero.id_doc)
                WHERE id_docster = '$idDoc'";
        $rs = $cmd->query($sql);
        $docs = $rs->fetch();
        if (!empty($docs)) {
            echo json_encode($docs);
        } else {
            echo json_encode('Sin datos');
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getCode());
    }
});
//PUT Modificar documentos
$app->put('/res/modificar/documento', function (Request $request, Response $response) {
    $id_ter = $request->getParam('id_ter');
    $iddoc = $request->getParam('iddoc');
    $idtipdoc = $request->getParam('idtipdoc');
    $nom_archivo = $request->getParam('nom_archivo');
    $nombre = $request->getParam('nombre');
    $archivo = $request->getParam('archivo');
    $fecini = $request->getParam('fecini');
    $fecvig = $request->getParam('fecvig');
    $iduser = $request->getParam('iduser');
    $tipuser = $request->getParam('tipuser');
    $temporal = $request->getParam('temporal');
    $temporal = base64_decode($temporal);
    $ruta = '../../uploads/terceros/docs/' . $id_ter . '/';
    include $GLOBALS['conexion'];
    $date = new DateTime('now', new DateTimeZone('America/Bogota'));
    if ($temporal !== '0') {
        unlink($ruta . $archivo);
        $nombre = $idtipdoc . '_' . date('YmdGis') . '_' . $nom_archivo;
        $nombre = strlen($nombre) >= 101 ? substr($nombre, 0, 100) : $nombre;
        $res = file_put_contents("$ruta/$nombre", $temporal);
        if (false !== $res) {
            try {
                $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
                $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
                $sql = "UPDATE seg_docs_tercero SET id_tipo_doc = ?, fec_inicio  = ?, fec_vig = ?, nombre_doc = ? WHERE id_docster = ?";
                $sql = $cmd->prepare($sql);
                $sql->bindParam(1, $idtipdoc, PDO::PARAM_INT);
                $sql->bindParam(2, $fecini, PDO::PARAM_STR);
                $sql->bindParam(3, $fecvig, PDO::PARAM_STR);
                $sql->bindParam(4, $nombre, PDO::PARAM_STR);
                $sql->bindParam(5, $iddoc, PDO::PARAM_INT);
                $sql->execute();
                $cambio = $sql->rowCount();
                if (!($sql->execute())) {
                    echo json_encode($sql->errorInfo()[2]);
                } else {
                    if ($cambio > 0) {
                        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
                        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
                        $sql = "UPDATE seg_docs_tercero SET  id_user_act = ?, tipo_user_act = ? ,fec_act = ? WHERE id_docster = ?";
                        $sql = $cmd->prepare($sql);
                        $sql->bindParam(1, $iduser, PDO::PARAM_INT);
                        $sql->bindParam(2, $tipuser, PDO::PARAM_STR);
                        $sql->bindValue(3, $date->format('Y-m-d H:i:s'));
                        $sql->bindParam(4, $iddoc, PDO::PARAM_INT);
                        $sql->execute();
                        if ($sql->rowCount() > 0) {
                            echo json_encode('1');
                        } else {
                            echo json_encode($sql->errorInfo()[2]);
                        }
                    } else {
                        echo json_encode('No se ingresó ningún dato nuevo');
                    }
                }
                $cmd = null;
            } catch (PDOException $e) {
                echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getCode());
            }
        } else {
            echo json_encode('No se pudo adjuntar el archivo');
        }
    } else {
        try {
            if ($nombre !== $archivo) {
                rename($ruta . $archivo, $ruta . $nombre);
            }
            $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
            $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
            $sql = "UPDATE seg_docs_tercero SET id_tipo_doc = ?, nombre_doc = ?, fec_inicio  = ?, fec_vig = ? WHERE id_docster = ?";
            $sql = $cmd->prepare($sql);
            $sql->bindParam(1, $idtipdoc, PDO::PARAM_INT);
            $sql->bindParam(2, $nombre, PDO::PARAM_STR);
            $sql->bindParam(3, $fecini, PDO::PARAM_STR);
            $sql->bindParam(4, $fecvig, PDO::PARAM_STR);
            $sql->bindParam(5, $iddoc, PDO::PARAM_INT);
            $sql->execute();
            $cambio = $sql->rowCount();
            if (!($sql->execute())) {
                echo json_encode($sql->errorInfo()[2]);
            } else {
                if ($cambio > 0) {
                    $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
                    $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
                    $sql = "UPDATE seg_docs_tercero SET  id_user_act = ?, tipo_user_act = ? ,fec_act = ? WHERE id_docster = ?";
                    $sql = $cmd->prepare($sql);
                    $sql->bindParam(1, $iduser, PDO::PARAM_INT);
                    $sql->bindParam(2, $tipuser, PDO::PARAM_STR);
                    $sql->bindValue(3, $date->format('Y-m-d H:i:s'));
                    $sql->bindParam(4, $iddoc, PDO::PARAM_INT);
                    $sql->execute();
                    if ($sql->rowCount() > 0) {
                        echo json_encode('1');
                    } else {
                        echo json_encode($sql->errorInfo()[2]);
                    }
                } else {
                    echo json_encode('No se ingresó ningún dato nuevo');
                }
            }
            $cmd = null;
        } catch (PDOException $e) {
            echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getCode());
        }
    }
});
//DELETE  Borrar documento
$app->delete('/res/eliminar/documento/{id}', function (Request $request, Response $response) {
    $idD = $request->getAttribute('id');
    include $GLOBALS['conexion'];
    try {
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "SELECT * FROM seg_docs_tercero
                WHERE id_docster = '$idD'";
        $rs = $cmd->query($sql);
        $doc = $rs->fetch();
        $ruta = $doc['ruta_doc'] . $doc['nombre_doc'];
        $sql = "DELETE FROM seg_docs_tercero  WHERE id_docster = ?";
        $sql = $cmd->prepare($sql);
        $sql->bindParam(1, $idD, PDO::PARAM_INT);
        $sql->execute();
        if ($sql->rowCount() > 0) {
            unlink($ruta);
            echo json_encode('1');
        } else {
            echo json_encode($sql->errorInfo()[2]);
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getCode());
    }
});
// DELETE Borrar resposabilidad economimca
$app->delete('/res/eliminar/resposabilidad/{id}', function (Request $request, Response $response) {
    $idt = $request->getAttribute('id');
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "DELETE FROM seg_responsabilidades_terceros  WHERE id_resptercero = ?";
        $sql = $cmd->prepare($sql);
        $sql->bindParam(1, $idt, PDO::PARAM_INT);
        $sql->execute();
        if ($sql->rowCount() > 0) {
            echo json_encode('1');
        } else {
            json_encode($sql->errorInfo()[2]);
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getCode());
    }
});
// DELETE Borrar actividad economimca
$app->delete('/res/eliminar/actividad/{id}', function (Request $request, Response $response) {
    $ida = $request->getAttribute('id');
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "DELETE FROM seg_actividad_terceros  WHERE id_actvtercero = ?";
        $sql = $cmd->prepare($sql);
        $sql->bindParam(1, $ida, PDO::PARAM_INT);
        $sql->execute();
        if ($sql->rowCount() > 0) {
            echo json_encode('1');
        } else {
            json_encode($sql->errorInfo()[2]);
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getCode());
    }
});
