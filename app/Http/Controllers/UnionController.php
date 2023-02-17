<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UnionController extends Controller
{
    public function __construct(){
        $macces = env("MOCHILA");//conexion a access de sucursal
        if(file_exists($macces)){
        try{  $this->conn  = new \PDO("odbc:DRIVER={Microsoft Access Driver (*.mdb, *.accdb)};charset=UTF-8; DBQ=".$macces."; Uid=; Pwd=;");
            }catch(\PDOException $e){ die($e->getMessage()); }
        }else{ die("$macces no es un origen de datos valido."); }
    

        $naccess = env("NAVIDAD");//conexion a access de sucursal
        if(file_exists($naccess)){
        try{  $this->con  = new \PDO("odbc:DRIVER={Microsoft Access Driver (*.mdb, *.accdb)};charset=UTF-8; DBQ=".$naccess."; Uid=; Pwd=;");
            }catch(\PDOException $e){ die($e->getMessage()); }
        }else{ die("$naccess no es un origen de datos valido."); }

    }

    public function uniondatabase(){
        $terminales = $this->logterminales();
        $retiradas = $this->retiradas();
        $proveedores = $this->proveedores();
        $ingresos = $this->ingresos();
        $categorias = $this->categorizacion();
        $traspasos = $this->traspasos();
        $clientes = $this->clientes();
        $anticipos = $this->anticipos();
        $almacenes = $this->almacenes();
        $articulos = $this->articulos();
        $compuestos = $this->compuestos();
        $familiarizados = $this->familiarizacion();
        $formapago = $this->formapago();
        $facturasrecibidas = $this->facturasrecibidas();
        $devoluciones = $this->devoluciones();
        $entradas = $this->entradas();
        $albaranes = $this->albaranes();
        $cobros = $this->cobros();



        return response()->json([
            "terminales"=>$terminales,
            "retiradas"=>$retiradas,
            "proveedores"=>$proveedores,
            "ingresos"=>$ingresos,
            "categorias"=>$categorias,
            "traspasos"=>$traspasos,
            "clientes"=>$clientes,
            "anticipos"=>$anticipos,
            "almacenes"=>$almacenes,
            "articulos"=>$articulos,
            "compuestos"=>$compuestos,
            "familiarizados"=>$familiarizados,
            "formaPago"=>$formapago,
            "facturasRecibidas"=>$facturasrecibidas,
            "devoluciones"=>$devoluciones,
            "entradas"=>$entradas,
            "albaranes"=>$albaranes,
            "cobros"=>$cobros,


        ]);

    }

    public function cobros(){
        $selectmoc = "SELECT MAX(CODCOB) AS ultimo FROM F_COB";
        $exec = $this->conn->prepare($selectmoc);//CON NAVIDAD CONN MOCHILA
        $exec -> execute();
        $cob=$exec->fetch(\PDO::FETCH_ASSOC);
        $idmax = $cob['ultimo'] + 1;

        $selectnav = "SELECT * FROM F_COB";
        $exec = $this->con->prepare($selectnav);//CON NAVIDAD CONN MOCHILA
        $exec -> execute();
        $cobnav=$exec->fetchall(\PDO::FETCH_ASSOC);
        if($cobnav){
            foreach($cobnav as $cobro){
                $registros[] = $cobro['CODCOB'];
                $cobro['CODCOB']=$idmax;
                $values = array_values($cobro);
                $column = array_keys($cobro);
                $impkeys = implode(",",$column);
                $insertcob = "INSERT INTO F_COB (".$impkeys.") VALUES (?,?,?,?,?,?,?,?)";
                $exec = $this->conn->prepare($insertcob);//CON NAVIDAD CONN MOCHILA
                $exec -> execute($values);
                $idmax++;
            }
            return count($registros);
        }else{return "No hay cobros buey jeje XD";}



    }

    public function albaranes(){
        $selectmoc = "SELECT DISTINCT TIPALB FROM F_ALB ORDER BY TIPALB ASC";
        $exec = $this->con->prepare($selectmoc);//CON NAVIDAD CONN MOCHILA
        $exec -> execute();
        $series=$exec->fetchall(\PDO::FETCH_ASSOC);
        if($series){
            foreach($series as $serie){
                $type[] = $serie['TIPALB'];
                $tipo = "'".$serie['TIPALB']."'";
                $maxidtip = "SELECT MAX(CODALB) as ultimo FROM F_ALB WHERE TIPALB = $tipo";
                $exec = $this->conn->prepare($maxidtip);//CON NAVIDAD CONN MOCHILA
                $exec -> execute();
                $maxid=$exec->fetch(\PDO::FETCH_ASSOC);
                $idmax = $maxid['ultimo'] + 1;

                $frec = "SELECT * FROM F_ALB WHERE TIPALB = $tipo";
                $exec = $this->con->prepare($frec);//CON NAVIDAD CONN MOCHILA
                $exec -> execute();
                $frecibida=$exec->fetchall(\PDO::FETCH_ASSOC);
                foreach($frecibida as $invoice){
                    $invo [] = $invoice['CODALB'];
                    $boin = $invoice['CODALB'];
                    $invoice['CODALB'] = $idmax;
                    $valuesinvo = array_values($invoice);
                    $column = array_keys($invoice);
                    $impkeys = implode(",",$column);

                    $inserinvo = "INSERT INTO F_ALB (".$impkeys.") VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
                    $exec = $this->conn->prepare($inserinvo);//CON NAVIDAD CONN MOCHILA
                    $exec -> execute($valuesinvo);
                    
                    $bodiesinvoice = "SELECT * FROM F_LAL WHERE TIPLAL = $tipo AND CODLAL = $boin";
                    $exec = $this->con->prepare($bodiesinvoice);//CON NAVIDAD CONN MOCHILA
                    $exec -> execute();
                    $bodies=$exec->fetchall(\PDO::FETCH_ASSOC);
                    if($bodies){
                        foreach($bodies as $bodie){
                            $bod [] = $bodie['ARTLAL'];
                            $bodie['CODLAL'] = $idmax;
                            $valuesbod = array_values($bodie);
                            $columnbod = array_keys($bodie);
                            $impcolumbod = implode(",",$columnbod);
                            $insertbod = "INSERT INTO F_LAL (".$impcolumbod.") VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
                            $exec = $this->conn->prepare($insertbod);//CON NAVIDAD CONN MOCHILA
                            $exec -> execute($valuesbod);

                        }
                    }else{$bod[] = [];}
                    $idmax++;
                }

            }
            $res = [
                "series"=>count($type),
                "albaranes"=>count($invo),
                "lineas"=>count($bod),
            ];

            return $res;
        }else{return "No hay remisiones  brou";}

    }
    
    public function entradas(){
        $selectmoc = "SELECT DISTINCT TIPENT FROM F_ENT ORDER BY TIPENT ASC";
        $exec = $this->con->prepare($selectmoc);//CON NAVIDAD CONN MOCHILA
        $exec -> execute();
        $series=$exec->fetchall(\PDO::FETCH_ASSOC);
        if($series){
            foreach($series as $serie){
                $type[] = $serie['TIPENT'];
                $tipo = "'".$serie['TIPENT']."'";
                $maxidtip = "SELECT MAX(CODENT) as ultimo FROM F_ENT WHERE TIPENT = $tipo";
                $exec = $this->conn->prepare($maxidtip);//CON NAVIDAD CONN MOCHILA
                $exec -> execute();
                $maxid=$exec->fetch(\PDO::FETCH_ASSOC);
                $idmax = $maxid['ultimo'] + 1;

                $frec = "SELECT * FROM F_ENT WHERE TIPENT = $tipo";
                $exec = $this->con->prepare($frec);//CON NAVIDAD CONN MOCHILA
                $exec -> execute();
                $frecibida=$exec->fetchall(\PDO::FETCH_ASSOC);
                foreach($frecibida as $invoice){
                    $invo [] = $invoice['CODENT'];
                    $boin = $invoice['CODENT'];
                    $invoice['CODENT'] = $idmax;
                    $valuesinvo = array_values($invoice);
                    $column = array_keys($invoice);
                    $impkeys = implode(",",$column);
                    $inserinvo = "INSERT INTO F_ENT (".$impkeys.") VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
                    $exec = $this->conn->prepare($inserinvo);//CON NAVIDAD CONN MOCHILA
                    $exec -> execute($valuesinvo);
                    
                    $bodiesinvoice = "SELECT * FROM F_LEN WHERE TIPLEN = $tipo AND CODLEN = $boin";
                    $exec = $this->con->prepare($bodiesinvoice);//CON NAVIDAD CONN MOCHILA
                    $exec -> execute();
                    $bodies=$exec->fetchall(\PDO::FETCH_ASSOC);
                    if($bodies){
                        foreach($bodies as $bodie){
                            $bod [] = $bodie['ARTLEN'];
                            $bodie['CODLEN'] = $idmax;
                            $valuesbod = array_values($bodie);
                            $columnbod = array_keys($bodie);
                            $impcolumbod = implode(",",$columnbod);
                            $insertbod = "INSERT INTO F_LEN (".$impcolumbod.") VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
                            $exec = $this->conn->prepare($insertbod);//CON NAVIDAD CONN MOCHILA
                            $exec -> execute($valuesbod);

                        }
                    }else{$bod[] = [];}
                    $idmax++;
                }

            }
            $res = [
                "series"=>count($type),
                "entradas"=>count($invo),
                "lineas"=>count($bod),
            ];

            return $res;
        }else{return "No hay entradas brou";}

    }

    public function devoluciones(){
        $selectmoc = "SELECT DISTINCT TIPFRD FROM F_FRD ORDER BY TIPFRD ASC";
        $exec = $this->con->prepare($selectmoc);//CON NAVIDAD CONN MOCHILA
        $exec -> execute();
        $series=$exec->fetchall(\PDO::FETCH_ASSOC);
        if($series){
            foreach($series as $serie){
                $type[] = $serie['TIPFRD'];
                $tipo = "'".$serie['TIPFRD']."'";
                $maxidtip = "SELECT MAX(CODFRD) as ultimo FROM F_FRD WHERE TIPFRD = $tipo";
                $exec = $this->conn->prepare($maxidtip);//CON NAVIDAD CONN MOCHILA
                $exec -> execute();
                $maxid=$exec->fetch(\PDO::FETCH_ASSOC);
                $idmax = $maxid['ultimo'] + 1;

                $frec = "SELECT * FROM F_FRD WHERE TIPFRD = $tipo";
                $exec = $this->con->prepare($frec);//CON NAVIDAD CONN MOCHILA
                $exec -> execute();
                $frecibida=$exec->fetchall(\PDO::FETCH_ASSOC);
                foreach($frecibida as $invoice){
                    $invo [] = $invoice['CODFRD'];
                    $boin = $invoice['CODFRD'];
                    $invoice['CODFRD'] = $idmax;
                    $valuesinvo = array_values($invoice);
                    $column = array_keys($invoice);
                    $impkeys = implode(",",$column);
                    $inserinvo = "INSERT INTO F_FRD (".$impkeys.") VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
                    $exec = $this->conn->prepare($inserinvo);//CON NAVIDAD CONN MOCHILA
                    $exec -> execute($valuesinvo);
                    
                    $bodiesinvoice = "SELECT * FROM F_LFD WHERE TIPLFD = $tipo AND CODLFD = $boin";
                    $exec = $this->con->prepare($bodiesinvoice);//CON NAVIDAD CONN MOCHILA
                    $exec -> execute();
                    $bodies=$exec->fetchall(\PDO::FETCH_ASSOC);
                    if($bodies){
                        foreach($bodies as $bodie){
                            $bod [] = $bodie['ARTLFD'];
                            $bodie['CODLFD'] = $idmax;
                            $valuesbod = array_values($bodie);
                            $columnbod = array_keys($bodie);
                            $impcolumbod = implode(",",$columnbod);
                            $insertbod = "INSERT INTO F_LFD (".$impcolumbod.") VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
                            $exec = $this->conn->prepare($insertbod);//CON NAVIDAD CONN MOCHILA
                            $exec -> execute($valuesbod);

                        }
                    }else{$bod[] = [];}
                    $idmax++;
                }

            }
            $res = [
                "series"=>count($type),
                "devoluciones"=>count($invo),
                "lineas"=>count($bod),
            ];

            return $res;
        }else{return "No hay devoluciones brou";}

    }

    public function facturasrecibidas(){
        $selectmoc = "SELECT DISTINCT TIPFRE FROM F_FRE ORDER BY TIPFRE ASC";
        $exec = $this->con->prepare($selectmoc);//CON NAVIDAD CONN MOCHILA
        $exec -> execute();
        $series=$exec->fetchall(\PDO::FETCH_ASSOC);
        if($series){
            foreach($series as $serie){
                $type[] = $serie['TIPFRE'];
                $tipo = "'".$serie['TIPFRE']."'";
                $maxidtip = "SELECT MAX(CODFRE) as ultimo FROM F_FRE WHERE TIPFRE = $tipo";
                $exec = $this->conn->prepare($maxidtip);//CON NAVIDAD CONN MOCHILA
                $exec -> execute();
                $maxid=$exec->fetch(\PDO::FETCH_ASSOC);
                $idmax = $maxid['ultimo'] + 1;

                $frec = "SELECT * FROM F_FRE WHERE TIPFRE = $tipo";
                $exec = $this->con->prepare($frec);//CON NAVIDAD CONN MOCHILA
                $exec -> execute();
                $frecibida=$exec->fetchall(\PDO::FETCH_ASSOC);
                foreach($frecibida as $invoice){
                    $invo [] = $invoice['CODFRE'];
                    $boin = $invoice['CODFRE'];
                    $invoice['CODFRE'] = $idmax;
                    $valuesinvo = array_values($invoice);
                    $column = array_keys($invoice);
                    $impkeys = implode(",",$column);
                    $inserinvo = "INSERT INTO F_FRE (".$impkeys.") VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
                    $exec = $this->conn->prepare($inserinvo);//CON NAVIDAD CONN MOCHILA
                    $exec -> execute($valuesinvo);
                    
                    $bodiesinvoice = "SELECT * FROM F_LFR WHERE TIPLFR = $tipo AND CODLFR = $boin";
                    $exec = $this->con->prepare($bodiesinvoice);//CON NAVIDAD CONN MOCHILA
                    $exec -> execute();
                    $bodies=$exec->fetchall(\PDO::FETCH_ASSOC);
                    if($bodies){
                        foreach($bodies as $bodie){
                            $bod [] = $bodie['ARTLFR'];
                            $bodie['CODLFR'] = $idmax;
                            $valuesbod = array_values($bodie);
                            $columnbod = array_keys($bodie);
                            $impcolumbod = implode(",",$columnbod);
                            $insertbod = "INSERT INTO F_LFR (".$impcolumbod.") VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
                            $exec = $this->conn->prepare($insertbod);//CON NAVIDAD CONN MOCHILA
                            $exec -> execute($valuesbod);

                        }
                    }else{$bod[] = [];}
                    $idmax++;
                }

            }
            $res = [
                "series"=>count($type),
                "facturas"=>count($invo),
                "lineas"=>count($bod),
            ];

            return $res;
        }else{return "No hay facturas brou";}
    }

    public function formapago(){
        $selectmoc = "SELECT CODFPA FROM F_FPA ORDER BY CODFPA ASC";
        $exec = $this->conn->prepare($selectmoc);//CON NAVIDAD CONN MOCHILA
        $exec -> execute();
        $prov=$exec->fetchall(\PDO::FETCH_ASSOC);
        foreach($prov as $pvo){
            $idmoc [] = $pvo['CODFPA'];
        }

        $selectnav = "SELECT CODFPA FROM F_FPA ORDER BY CODFPA ASC";
        $exec = $this->con->prepare($selectnav);//CON NAVIDAD CONN MOCHILA
        $exec -> execute();
        $provna=$exec->fetchall(\PDO::FETCH_ASSOC);
        foreach($provna as $pvona){
            $idnav [] = $pvona['CODFPA'];
        }

        $diff = array_diff($idnav,$idmoc);
        if($diff){
            foreach($diff as $diferente){
                $compu[]= "'".$diferente."'";
            }
            $implocompu = implode(",",$compu);
            $compunav = "SELECT *  FROM F_FPA WHERE CODFPA IN (".$implocompu.") ORDER BY CODFPA ASC";
            $exec = $this->con->prepare($compunav);//CON NAVIDAD CONN MOCHILA
            $exec -> execute();
            $compuestos=$exec->fetchall(\PDO::FETCH_ASSOC);
            if($compuestos){
                foreach($compuestos as $compuesto){
                    $registros[]=$compuesto['CODFPA'];
                    $values = array_values($compuesto);
                    $column = array_keys($compuesto);
                    $impkeys = implode(",",$column);
                    $insertcom = "INSERT INTO F_FPA (".$impkeys.") VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
                    $exec = $this->conn->prepare($insertcom);//CON NAVIDAD CONN MOCHILA
                    $exec -> execute($values);
                }
            }else{$registros = [];}

            return count($registros);
            
        }else{return "Las formas de pago estan igualitas carnal jalalae para otro lado buey";}

    }

    public function familiarizacion(){
        $selectmoc = "SELECT EANEAN FROM F_EAN ORDER BY EANEAN ASC";
        $exec = $this->conn->prepare($selectmoc);//CON NAVIDAD CONN MOCHILA
        $exec -> execute();
        $prov=$exec->fetchall(\PDO::FETCH_ASSOC);
        foreach($prov as $pvo){
            $idmoc [] = $pvo['EANEAN'];
        }

        $selectnav = "SELECT EANEAN FROM F_EAN ORDER BY EANEAN ASC";
        $exec = $this->con->prepare($selectnav);//CON NAVIDAD CONN MOCHILA
        $exec -> execute();
        $provna=$exec->fetchall(\PDO::FETCH_ASSOC);
        foreach($provna as $pvona){
            $idnav [] = $pvona['EANEAN'];
        }

        $diff = array_diff($idnav,$idmoc);
        if($diff){
            foreach($diff as $diferente){
                $compu[]= "'".$diferente."'";
            }
            $implocompu = implode(",",$compu);
            $compunav = "SELECT *  FROM F_EAN WHERE EANEAN IN (".$implocompu.") ORDER BY EANEAN ASC";
            $exec = $this->con->prepare($compunav);//CON NAVIDAD CONN MOCHILA
            $exec -> execute();
            $compuestos=$exec->fetchall(\PDO::FETCH_ASSOC);
            if($compuestos){
                foreach($compuestos as $compuesto){
                    $registros[]=$compuesto['EANEAN'];
                    $values = array_values($compuesto);
                    $column = array_keys($compuesto);
                    $impkeys = implode(",",$column);
                    $insertcom = "INSERT INTO F_EAN (".$impkeys.") VALUES (?,?)";
                    $exec = $this->conn->prepare($insertcom);//CON NAVIDAD CONN MOCHILA
                    $exec -> execute($values);
                }
            }else{$registros = [];}

            return count($registros);
            
        }else{return "Articulos Familiarizados Iguales Padrino de vino";}
    }

    public function compuestos(){
        $selectmoc = "SELECT ARTCOM  FROM F_COM ORDER BY ARTCOM ASC";
        $exec = $this->conn->prepare($selectmoc);//CON NAVIDAD CONN MOCHILA
        $exec -> execute();
        $prov=$exec->fetchall(\PDO::FETCH_ASSOC);
        foreach($prov as $pvo){
            $idmoc [] = $pvo['ARTCOM'];
        }

        $selectnav = "SELECT ARTCOM FROM F_COM ORDER BY ARTCOM ASC";
        $exec = $this->con->prepare($selectnav);//CON NAVIDAD CONN MOCHILA
        $exec -> execute();
        $provna=$exec->fetchall(\PDO::FETCH_ASSOC);
        if($provna){
            foreach($provna as $pvona){
                $idnav [] = $pvona['ARTCOM'];
            }

            $diff = array_diff($idnav,$idmoc);
            if($diff){
                foreach($diff as $diferente){
                    $compu[]= "'".$diferente."'";
                }
                $implocompu = implode(",",$compu);
                $compunav = "SELECT *  FROM F_COM WHERE ARTCOM IN (".$implocompu.") ORDER BY ARTCOM ASC";
                $exec = $this->con->prepare($compunav);//CON NAVIDAD CONN MOCHILA
                $exec -> execute();
                $compuestos=$exec->fetchall(\PDO::FETCH_ASSOC);
                if($compuestos){
                    foreach($compuestos as $compuesto){
                        $registros[]=$compuesto['ARTCOM'];
                        $values = array_values($compuesto);
                        $column = array_keys($compuesto);
                        $impkeys = implode(",",$column);
                        $insertcom = "INSERT INTO F_COM (".$impkeys.") VALUES (?,?,?,?,?,?,?,?)";
                        $exec = $this->conn->prepare($insertcom);//CON NAVIDAD CONN MOCHILA
                        $exec -> execute($values);
                    }
                }else{$registros = [];}

                return count($registros);
                
            }else{return "Articulos Compuestos Iguales Padrino de vino";}
        }else{return "No hay nada que pasar bro";}


    }

    public function clientes(){
        $selectmoc = "SELECT CODCLI  FROM F_CLI ORDER BY CODCLI ASC";
        $exec = $this->conn->prepare($selectmoc);//CON NAVIDAD CONN MOCHILA
        $exec -> execute();
        $prov=$exec->fetchall(\PDO::FETCH_ASSOC);
        foreach($prov as $pvo){
            $idmoc [] = $pvo['CODCLI'];
        }

        $selectnav = "SELECT CODCLI  FROM F_CLI ORDER BY CODCLI ASC";
        $exec = $this->con->prepare($selectnav);//CON NAVIDAD CONN MOCHILA
        $exec -> execute();
        $provna=$exec->fetchall(\PDO::FETCH_ASSOC);
        foreach($provna as $pvona){
            $idnav [] = $pvona['CODCLI'];
        }

        $diff = array_diff($idnav,$idmoc);
        
        if($diff){
            foreach($diff as $dife){
                $impid[] = $dife;
            }

            $selectnavid = "SELECT *  FROM F_CLI WHERE CODCLI IN (".implode(",",$impid).") ORDER BY CODCLI ASC";
            $exec = $this->con->prepare($selectnavid);//CON NAVIDAD CONN MOCHILA
            $exec -> execute();

            $provnav=$exec->fetchall(\PDO::FETCH_ASSOC);
            foreach($provnav as $pvonaic){
                $registros [] = $pvonaic['CODCLI'];
                $values = array_values($pvonaic);
                $column = array_keys($pvonaic);
                $impkey = implode(",",$column);
                $insertmoc = "INSERT INTO F_CLI (".$impkey.") VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
                $exec = $this->conn->prepare($insertmoc);//CON NAVIDAD CONN MOCHILA
                $exec -> execute($values);
            }
            return count($registros);

        }else{return "Los clientes son los mismos buey";}
    }

    public function articulos(){
        $selectmoc = "SELECT CODART FROM F_ART ORDER BY CODART ASC";
        $exec = $this->conn->prepare($selectmoc);//CON NAVIDAD CONN MOCHILA
        $exec -> execute();
        $artmoc = $exec->fetchall(\PDO::FETCH_ASSOC);
        $mocutf = array_keys($artmoc[0]);
        foreach($artmoc as $artmoch){
            foreach($mocutf as $col){ $artmoch[$col] = utf8_encode($artmoch[$col]); }
            $idartmoch [] = $artmoch['CODART'];
        }

        $selectnav = "SELECT CODART FROM F_ART ORDER BY CODART ASC";
        $exec = $this->con->prepare($selectnav);//CON NAVIDAD CONN MOCHILA
        $exec -> execute();
        $artnavid = $exec->fetchall(\PDO::FETCH_ASSOC);
        $navutf = array_keys($artnavid[0]);
        foreach($artnavid as $artnav){
            foreach($navutf as $col){ $artnav[$col] = utf8_encode($artnav[$col]); }
            $idartnav [] = $artnav['CODART'];
        }

        $diff = array_diff($idartnav,$idartmoch);
        if($diff){
            foreach($diff as $diferencia){
                $artfal [] ="'".$diferencia."'"; 
            }
            $impart = implode(",",$artfal);

            $articulosnavidad = "SELECT * FROM F_ART WHERE CODART IN (".$impart.") ORDER BY CODART ASC";
            $exec = $this->con->prepare($articulosnavidad);//CON NAVIDAD CONN MOCHILA
            $exec -> execute();
            $artnavidad = $exec->fetchall(\PDO::FETCH_ASSOC);
            if($artnavidad){
                $naviutf = array_keys($artnavidad[0]);
                foreach($artnavidad as $artnavi){
                    foreach($naviutf as $coli){ $artnavi[$coli] = utf8_encode($artnavi[$coli]); }
                    $registros [] = $artnavi['CODART'];
                    $values = array_values($artnavi);
                    $column = array_keys($artnavi);
                    $impkeys = implode(",",$column);
                    $insertmoc = "INSERT INTO F_ART (".$impkeys.") VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
                    $exec = $this->conn->prepare($insertmoc);//CON NAVIDAD CONN MOCHILA
                    $exec -> execute($values);
                }
            }else{$registros = [];}

            return count($registros);
        }else{return "Los articulos son los mismos buey";}
    }

    public function almacenes(){
        $selectmoc = "SELECT CODALM FROM F_ALM ORDER BY CODALM ASC";
        $exec = $this->conn->prepare($selectmoc);//CON NAVIDAD CONN MOCHILA
        $exec -> execute();
        $almacenesmoc = $exec->fetchall(\PDO::FETCH_ASSOC);
        foreach($almacenesmoc as $almmoc){
            $idalmmoc [] = $almmoc['CODALM'];
        }

        $selectnav = "SELECT CODALM FROM F_ALM ORDER BY CODALM ASC";
        $exec = $this->con->prepare($selectnav);//CON NAVIDAD CONN MOCHILA
        $exec -> execute();
        $almacenesnav = $exec->fetchall(\PDO::FETCH_ASSOC);
        foreach($almacenesnav as $almnav){
            $idalmnav [] = $almnav['CODALM'];
        }

        $diff = array_diff($idalmnav,$idalmmoc);
        if($diff){
            foreach($diff as $diferencia){
                $dife[]  = "'".$diferencia."'";
            }
            
            $alnav= "SELECT * FROM F_ALM WHERE CODALM IN (".implode($dife).")";
            $exec = $this->con->prepare($alnav);//CON NAVIDAD CONN MOCHILA
            $exec -> execute();
            $anav = $exec->fetchall(\PDO::FETCH_ASSOC);
            if($anav){
                foreach($anav as $almacen){
                    $registros[]=$almacen['CODALM'];
                    $values = array_values($almacen);
                    $column = array_keys($almacen);
                    $impkey = implode(",",$column);
                    $insertalm = "INSERT INTO F_ALM (".$impkey.") VALUES (?,?,?,?,?,?,?,?,?,?,?)";
                    $exec = $this->conn->prepare($insertalm);//CON NAVIDAD CONN MOCHILA
                    $exec -> execute($values);
                }
            }else{$registros = [];}

            return count($registros);

        }else{return "Los almacenes son los mismos bro";}

    }

    public function anticipos(){
       $selectmoc = "SELECT MAX(CODANT) AS ultimo FROM F_ANT";
        $exec = $this->conn->prepare($selectmoc);//CON NAVIDAD CONN MOCHILA
        $exec -> execute();
        $ret=$exec->fetch(\PDO::FETCH_ASSOC);

            $idmax = $ret['ultimo'] + 1;
            $id = $ret['ultimo'];

        $selectnav = "SELECT * FROM F_ANT WHERE CODANT > $id ORDER BY FECANT ASC";
        $exec = $this->con->prepare($selectnav);//CON NAVIDAD CONN MOCHILA
        $exec -> execute();
        $retnav=$exec->fetchall(\PDO::FETCH_ASSOC);
        if($retnav){
            foreach($retnav as $retirada){
                $registros[] = $retirada['CODANT'];
                $values  = [
                    $idmax,
                    $retirada['FECANT'],
                    $retirada['CLIANT'],
                    $retirada['IMPANT'],
                    $retirada['ESTANT'],
                    $retirada['DOCANT'],
                    $retirada['TDOANT'],
                    $retirada['CDOANT'],
                    $retirada['SDOANT'],
                    $retirada['OBSANT'],
                    $retirada['CRIANT'],
                    $retirada['CAJANT'],
                    $retirada['TPVIDANT']
                ];
                $column = array_keys($retirada);
                $impkey = implode(",",$column);
                $idmax++;
                $insertmoc = "INSERT INTO F_ANT (".$impkey.") VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";
                $exec = $this->conn->prepare($insertmoc);//CON NAVIDAD CONN MOCHILA
                $exec -> execute($values);
            }
            return count($registros);
        }else{return "bro todas las retiradas estan";}
    }

    public function categorizacion(){
        $selecsecmoc = "SELECT CODSEC FROM F_SEC ORDER BY CODSEC ASC";
        $exec = $this->conn->prepare($selecsecmoc);//CON NAVIDAD CONN MOCHILA
        $exec -> execute();
        $seccionmoc=$exec->fetchall(\PDO::FETCH_ASSOC);
        if($seccionmoc){
            foreach($seccionmoc as $secmoc){
                $idmoc[] = $secmoc['CODSEC'];
            }
        }else{$idmoc = [];}

        $selecsecnav = "SELECT CODSEC FROM F_SEC ORDER BY CODSEC ASC";
        $exec = $this->con->prepare($selecsecnav);//CON NAVIDAD CONN MOCHILA
        $exec -> execute();
        $seccionnav=$exec->fetchall(\PDO::FETCH_ASSOC);
        if($seccionnav){
            foreach($seccionnav as $secnav){
                $idnav[] = $secnav['CODSEC'];
            }
        }else{$idnav = [];}

        $diff = array_diff($idnav,$idmoc);
        if($diff){
            foreach($diff as $dif){
                $secfal[] = "'".$dif."'";
            }
            $busc = "SELECT * FROM F_SEC WHERE CODSEC IN (".implode(",",$secfal).")";
            $exec = $this->con->prepare($busc);//CON NAVIDAD CONN MOCHILA
            $exec -> execute();
            $falt=$exec->fetchall(\PDO::FETCH_ASSOC);
            foreach($falt as $seccion){
                $fsec [] = $seccion['CODSEC'];
                $values = array_values($seccion);
                $column = array_keys($seccion);
                $impkey = implode(",",$column);
                $insertmoc = "INSERT INTO F_SEC (".$impkey.") VALUES (?,?,?,?,?,?)";
                $exec = $this->conn->prepare($insertmoc);//CON NAVIDAD CONN MOCHILA
                $exec -> execute($values);
            }
        }else{$fsec = [];}

        $selecsecfam = "SELECT CODFAM FROM F_FAM ORDER BY CODFAM ASC";
        $exec = $this->conn->prepare($selecsecfam);//CON NAVIDAD CONN MOCHILA
        $exec -> execute();
        $seccionfam=$exec->fetchall(\PDO::FETCH_ASSOC);
        if($seccionfam){
            foreach($seccionfam as $secfam){
                $idmocfam[] = $secfam['CODFAM'];
            }
        }else{$idmocfam = [];}

        $selecfamnav = "SELECT CODFAM FROM F_FAM ORDER BY CODFAM ASC";
        $exec = $this->con->prepare($selecfamnav);//CON NAVIDAD CONN MOCHILA
        $exec -> execute();
        $famnav=$exec->fetchall(\PDO::FETCH_ASSOC);
        if($famnav){
            foreach($famnav as $famnav){
                $idnavfam[] = $famnav['CODFAM'];
            }
        }else{$idnavfam = [];}

        $diffam = array_diff($idnavfam,$idmocfam);
        if($diffam){
            foreach($diffam as $difam){
                $famfal[] = "'".$difam."'";
            }
            $busca = "SELECT * FROM F_FAM WHERE CODFAM IN (".implode(",",$famfal).")";
            $exec = $this->con->prepare($busca);//CON NAVIDAD CONN MOCHILA
            $exec -> execute();
            $falta=$exec->fetchall(\PDO::FETCH_ASSOC);
            foreach($falta as $familia){
                $ffam [] = $familia['CODFAM'];
                $valuesfam = array_values($familia);
                $columnfam = array_keys($familia);
                $impkey = implode(",",$columnfam);
                $insertmocfam = "INSERT INTO F_FAM (".$impkey.") VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
                $exec = $this->conn->prepare($insertmocfam);//CON NAVIDAD CONN MOCHILA
                $exec -> execute($valuesfam);
            }
        }else{$ffam = [];}

        
        $res = [
            "secciones"=>count($fsec),
            "familias"=>count($ffam)
        ];

        return $res;






    }

    public function ingresos(){
        $selectmoc = "SELECT MAX(CODING) AS ultimo FROM F_ING";
        $exec = $this->conn->prepare($selectmoc);//CON NAVIDAD CONN MOCHILA
        $exec -> execute();
        $ing=$exec->fetch(\PDO::FETCH_ASSOC);
        // $idmax = $ing['ultimo'] == "" ? 1 : $ing['ultimo'];
        // $id = $ing['ultimo'] == "" ? 0 : $ing['ultimo'];
        $idmax = $ing['ultimo'] + 1 ;
        $id = $ing['ultimo'];
        
        $selectnav = "SELECT * FROM F_ING WHERE CODING > $id ORDER BY FECING ASC";
        $exec = $this->con->prepare($selectnav);//CON NAVIDAD CONN MOCHILA
        $exec -> execute();
        $ingnav=$exec->fetchall(\PDO::FETCH_ASSOC);
        if($ingnav){
            foreach($ingnav as $ingreso){
                $registros[] = $ingreso['CODING'];
                $values  = [
                    $idmax,
                    $ingreso['CAJING'],
                    $ingreso['FECING'],
                    $ingreso['HORING'],
                    $ingreso['CONING'],
                    $ingreso['IMPING'],
                    $ingreso['CLIING'],
                    $ingreso['TPVIDING'],
                    $ingreso['CFAING'],
                    $ingreso['PFAING'],
                ];
                $column = array_keys($ingreso);
                $impkey = implode(",",$column);
                $idmax++;
                $insertmoc = "INSERT INTO F_ING (".$impkey.") VALUES (?,?,?,?,?,?,?,?,?,?)";
                $exec = $this->conn->prepare($insertmoc);//CON NAVIDAD CONN MOCHILA
                $exec -> execute($values);
            }
            return count($registros);
        }else{return "Estan bien los ingresos bro";}
    }

    public function traspasos(){
        $selectmoc = "SELECT MAX(DOCTRA) AS ultimo FROM F_TRA";
        $exec = $this->conn->prepare($selectmoc);//CON NAVIDAD CONN MOCHILA
        $exec -> execute();
        $tra=$exec->fetch(\PDO::FETCH_ASSOC);
        $idmax = $tra['ultimo'] + 1;

        $selectnav = "SELECT * FROM F_TRA ORDER BY DOCTRA ASC";
        $exec = $this->con->prepare($selectnav);//CON NAVIDAD CONN MOCHILA
        $exec -> execute();
        $tranav=$exec->fetchall(\PDO::FETCH_ASSOC);
        if($tranav){
            foreach($tranav as $traspaso){
                $registros[]=$traspaso['DOCTRA'];
                $idtraspaso = $traspaso['DOCTRA'];
                $insvaltra = [
                    $idmax,
                    $traspaso['FECTRA'],
                    $traspaso['AORTRA'],
                    $traspaso['ADETRA'],
                    $traspaso['COMTRA']
                ];
                $column = array_keys($traspaso);
                $impcol = implode(",",$column);
                $insertra = "INSERT INTO F_TRA (".$impcol.") VALUES (?,?,?,?,?)";
                $exec = $this->conn->prepare($insertra);//CON NAVIDAD CONN MOCHILA
                $exec -> execute($insvaltra);

                $cuerpo = "SELECT * FROM F_LTR WHERE DOCLTR = $idtraspaso";
                $exec = $this->con->prepare($cuerpo);//CON NAVIDAD CONN MOCHILA
                $exec -> execute();
                $bodietra=$exec->fetchall(\PDO::FETCH_ASSOC);
                foreach($bodietra as $bodie){
                    $lintra[]=$bodie['DOCLTR'];
                    $insvalltr = [
                        $idmax,
                        $bodie['LINLTR'],
                        $bodie['ARTLTR'],
                        $bodie['CANLTR'],
                        $bodie['LOTLTR'],
                        $bodie['FFALTR'],
                        $bodie['FCOLTR'],
                        $bodie['CE1LTR'],
                        $bodie['CE2LTR'],
                        $bodie['BULLTR'],
                    ];
                    $columnlin = array_keys($bodie);
                    $impcollin = implode(",",$columnlin);
                    $inserltr = "INSERT INTO F_LTR (".$impcollin.") VALUES (?,?,?,?,?,?,?,?,?,?)";
                    $exec = $this->conn->prepare($inserltr);//CON NAVIDAD CONN MOCHILA
                    $exec -> execute($insvalltr);
                }

                $idmax++; 
            }
            $res = [
                "traspasos"=>count($registros),
                "lineastraspasos"=>count($lintra)
            ];
    
            return $res;
    
        }else{return "No hay traspasos brou";}


    }

    public function proveedores(){
        $selectmoc = "SELECT CODPRO  FROM F_PRO ORDER BY CODPRO ASC";
        $exec = $this->conn->prepare($selectmoc);//CON NAVIDAD CONN MOCHILA
        $exec -> execute();
        $prov=$exec->fetchall(\PDO::FETCH_ASSOC);
        foreach($prov as $pvo){
            $idmoc [] = $pvo['CODPRO'];
        }

        $selectnav = "SELECT CODPRO  FROM F_PRO ORDER BY CODPRO ASC";
        $exec = $this->con->prepare($selectnav);//CON NAVIDAD CONN MOCHILA
        $exec -> execute();
        $provna=$exec->fetchall(\PDO::FETCH_ASSOC);
        foreach($provna as $pvona){
            $idnav [] = $pvona['CODPRO'];
        }

        $diff = array_diff($idnav,$idmoc);
        
        if($diff){
            foreach($diff as $dife){
                $impid[] = $dife;
            }
            $selectnavid = "SELECT *  FROM F_PRO WHERE CODPRO IN (".implode(",",$impid).") ORDER BY CODPRO ASC";
            $exec = $this->con->prepare($selectnavid);//CON NAVIDAD CONN MOCHILA
            $exec -> execute();
            $provnav=$exec->fetchall(\PDO::FETCH_ASSOC);
            foreach($provnav as $pvonaic){
                $registros [] = $pvonaic['CODPRO'];
                $values = array_values($pvonaic);
                $column = array_keys($pvonaic);
                $impkey = implode(",",$column);
                $insertmoc = "INSERT INTO F_PRO (".$impkey.") VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
                $exec = $this->conn->prepare($insertmoc);//CON NAVIDAD CONN MOCHILA
                $exec -> execute($values);
            }
            return count($registros);

        }else{return "Los proveedores son los mismos buey";}
    }

    public function retiradas(){
        $selectmoc = "SELECT MAX(CODRET) AS ultimo FROM F_RET";
        $exec = $this->conn->prepare($selectmoc);//CON NAVIDAD CONN MOCHILA
        $exec -> execute();
        $ret=$exec->fetch(\PDO::FETCH_ASSOC);

            $idmax = $ret['ultimo'] + 1;
            $id = $ret['ultimo'];

        $selectnav = "SELECT * FROM F_RET WHERE CODRET > $id ORDER BY FECRET ASC";
        $exec = $this->con->prepare($selectnav);//CON NAVIDAD CONN MOCHILA
        $exec -> execute();
        $retnav=$exec->fetchall(\PDO::FETCH_ASSOC);
        if($retnav){
            foreach($retnav as $retirada){
                $registros[] = $retirada['CODRET'];
                $values  = [
                    $idmax,
                    $retirada['CAJRET'],
                    $retirada['FECRET'],
                    $retirada['HORRET'],
                    $retirada['CONRET'],
                    $retirada['IMPRET'],
                    $retirada['PRORET'],
                    $retirada['TPVIDRET'],
                    $retirada['CFARET'],
                    $retirada['PFARET'],
                ];
                $column = array_keys($retirada);
                $impkey = implode(",",$column);
                $idmax++;
                $insertmoc = "INSERT INTO F_RET (".$impkey.") VALUES (?,?,?,?,?,?,?,?,?,?)";
                $exec = $this->conn->prepare($insertmoc);//CON NAVIDAD CONN MOCHILA
                $exec -> execute($values);
            }
            return count($registros);
        }else{return "bro todas las retiradas estan";}
        

    }

    public function logterminales(){
        $selectmoc = "SELECT IDEATE  FROM T_ATE ORDER BY IDEATE ASC";
        $exec = $this->conn->prepare($selectmoc);//CON NAVIDAD CONN MOCHILA
        $exec -> execute();
        $fecmax=$exec->fetchall(\PDO::FETCH_ASSOC);
        foreach($fecmax as $idsmoc){
            $idsmate [] = $idsmoc['IDEATE'];
        }

        $selectnavs = "SELECT IDEATE  FROM T_ATE ORDER BY IDEATE ASC";
        $exec = $this->con->prepare($selectnavs);//CON NAVIDAD CONN MOCHILA
        $exec -> execute();
        $fecmaxn=$exec->fetchall(\PDO::FETCH_ASSOC);
        foreach($fecmaxn as $idsnav){
            $idsnate [] = $idsnav['IDEATE'];
        }

        $diff =  array_diff($idsnate,$idsmate);
        if($diff){
            foreach($diff as $dife){
                $difere [] = "'".$dife."'";
            }
            $implodif = implode(",",$difere);
            $selectnav = "SELECT * FROM T_ATE WHERE IDEATE IN (".$implodif.")";
            $exec = $this->con->prepare($selectnav);//CON NAVIDAD CONN MOCHILA
            $exec -> execute();
            $atenav=$exec->fetchall(\PDO::FETCH_ASSOC);
            if($atenav){
                foreach($atenav as $ternav){
                    $registros[] = $ternav['TERATE'];
                    $values = array_values($ternav);
                    $column = array_keys($ternav); 
                    $impkey = implode(",",$column);
                    $insertmoc = "INSERT INTO T_ATE (".$impkey.") VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
                    $exec = $this->conn->prepare($insertmoc);//CON NAVIDAD CONN MOCHILA
                    $exec -> execute($values);
                }
            }else{$registros = [];}
            return count($registros);
        }else{return count($diff);}

           
        


    }
}
