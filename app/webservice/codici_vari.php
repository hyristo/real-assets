<?php
$action=$_REQUEST["action"];
switch($action){
    
    case 'search':
        autocomplete();
    break;
    case 'load':
        load();
    break; 
    case "list":        
        listCodiciVari();
    break;
    case "save":
        save();
    break;
    case "delete":
        delete();
    break;
    case "riattiva":
        riattiva();
    break;
}

exit();

function autocomplete(){   
    $term = Utils::getFromReq("term", "");
    $gruppo = Utils::getFromReq("gruppo", "");
    session_write_close(); 
    $ret = CodiciVari::autocomplete($term, $gruppo);
    exit(html_entity_decode(json_encode($ret)));
}

function load(){    
    $id = Utils::getFromReq("ID_CODICE",0);    
    $gruppo = Utils::getFromReq("GRUPPO","");    
    $record = CodiciVari::Load($id, $gruppo);
    
    exit(json_encode($record));
}

function listCodiciVari(){
    global $GRUPPI_CODICIVARI;
    $draw = $_POST['draw'];
    $row = $_POST['start'];
    $rowperpage = $_POST['length']; // Rows display per page
    $columnIndex = $_POST['order'][0]['column']; // Column index
    $columnName = $_POST['columns'][$columnIndex]['data']; // Column name
    $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
    $searchValue = $_POST['search']['value']; // Search value
    
    $cl = $_POST['columns'];
    $data = array();
    $searchArray = array();
    
    for($i = 0; $i<count($cl); $i++) {
        if($cl[$i]['search']['value']!==""){
            $searchArray[$cl[$i]['data']]= $cl[$i]['search']['value'];
            //echo $cl[$i]['data']." => ".$cl[$i]['search']['value']."</br>";
        }
    }
    //echo "<pre>".print_r($cl, true)."</pre>";
    //echo "<pre>".print_r($searchArray, true)."</pre>";
    
    //exit();
    $searchQuery = "";
    if(count($searchArray)>0){
        $searchQuery .= " AND ( ";
        $where="";
        foreach ($searchArray as $key => $value) {
            $searchQuery .="";
            $where .= ($where == "" ? "" : " OR ") .$key ." = :".$key;
        }
        
       $searchQuery .= $where." )";
    }
    
    //echo $searchQuery;
    
    ## Search     
    if($searchValue != ''){
       $searchQuery .= " AND ( lower( descrizione ) LIKE :descrizione) ";
       $searchArray['descrizione'] = "%$searchValue%";
    }
    //echo $searchQuery;exit();
    $res = CodiciVari::LoadDataTable($searchQuery, $searchArray, $columnName,$columnSortOrder, $row, $rowperpage);
    //Utils::print_array($res);
    foreach($res['empRecords'] as $row){
        
        $onclick = 'onclick="realAssetsFramework.takeChargeCodiciVari(\''.$row['ID_CODICE'].'\',\''.$row['GRUPPO'].'\',\'codici_vari\', \'load\' )"';
        
        $fnAddMod='<a rel="'.RELUPDATE.'" data-toggle="modal" data-target="#editCodice" class="btn btn-primary" href="#editCodice" '.$onclick.' ><i class="fa fa-edit"></i> </a>';
        
        if($row['MODIFICABILE'] == 1){
            $fnAddMod='<a href="#" disabled class="btn btn-secondary" ><i class="fa fa-edit"></i></a>';
        }
        
        if($row['CANCELLATO']==0){
            $onclickDisable = 'onclick="realAssetsFramework.takeChargeCodiciVari(\''.$row['ID_CODICE'].'\',\''.$row['GRUPPO'].'\',\'codici_vari\', \'delete\', \'#ListCodiciVari\' )"';
            $colorBtnDisable = 'btn-warning';
            $iconBtnDisable = 'fas fa-toggle-on';
            
        } else {
            $onclickDisable = '';//'onclick="fitosanFramework.takeChargeCodiciVari(\''.$row['id_codice'].'\',\'codici_vari\', \'riattiva\', \'#ListCodiciVari\' )"';
            $colorBtnDisable = 'btn-danger';
            $iconBtnDisable = 'fas fa-toggle-off';
        }
        
        $fnDisable = '<a href="#" '.$onclickDisable.' rel="'.RELUPDATE.'"  class="btn '.$colorBtnDisable.'" ><i class="'.$iconBtnDisable.'"></i></a>';
        
        $data[] = array(
            "ID_CODICE"=> $row['ID_CODICE'],
            "GRUPPO"=> $row['GRUPPO'],
            "DESCRIZIONE"=>$row['DESCRIZIONE'],
            "SIGLA"=>$row['SIGLA'],
            "modifica"=>$fnAddMod,
            "cancellato"=>$fnDisable/*,
            "invisibile"=>$fnDisable*/
        );
    }
    ## Response
    $response = array(
       "draw" => intval($draw),
       "iTotalRecords" => $res['iTotalRecords'],
       "iTotalDisplayRecords" => $res['iTotalDisplayRecords'],
       "aaData" => $data
    );

    exit(json_encode($response));
    
    
}

function save(){
    $response = array();
    $rec = new CodiciVari($_POST);    
    $response = $rec->Save();
    exit(json_encode($response));
}


function delete(){
    
    $rec = new CodiciVari($_POST);        
    $return = $rec->LogicalDelete();
    exit(json_encode($return));
}
