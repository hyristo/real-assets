<?php
$action=$_REQUEST["action"];
switch($action){
    case 'search':
        $term = Utils::getFromReq("searchTerm", "");
        search($term);
    break;
    
    case 'load':
        load();
    break; 
    case "list":        
        listCard();
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

function search($term){ 
    session_write_close(); 
    $return = AnagraficaMemoryCard::autocomplete($term);
    exit(html_entity_decode(json_encode($return)));
}

function load(){    
    $id = Utils::getFromReq("id",0);          
    $record = new AnagraficaMemoryCard($id);
    
    exit(json_encode($record));
}

function listCard(){    
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
       $searchQuery .= " AND ( lower( CODICE ) LIKE :CODICE) ";
       $searchArray['CODICE'] = "%$searchValue%";
    }
    //echo $searchQuery;exit();
    $res = AnagraficaMemoryCard::LoadDataTable($searchQuery, $searchArray, $columnName,$columnSortOrder, $row, $rowperpage);
    //Utils::print_array($res);
    foreach($res['empRecords'] as $row){
        
        $onclick = 'onclick="realAssetsFramework.takeCharge(\''.$row['ID'].'\',\'memory_card\', \'load\', \'\', \'form-edit-codice\'  )"';
        
        $fnAddMod='<a rel="'.RELUPDATE.'" data-toggle="modal" data-target="#editCodice" class="btn btn-primary" href="#editCodice" '.$onclick.' ><i class="fa fa-edit"></i> </a>';
        
        
        if($row['CANCELLATO']==0){
            $onclickDisable = 'onclick="realAssetsFramework.takeChargeConfirm(\''.$row['ID'].'\',\'memory_card\', \'delete\', \'#ListCard\' )"';
            $colorBtnDisable = 'btn-success';
            $iconBtnDisable = 'fas fa-toggle-on';
            
        } else {
            $onclickDisable = 'onclick="realAssetsFramework.takeChargeConfirm(\''.$row['ID'].'\',\'memory_card\', \'riattiva\', \'#ListCard\' )"';
            $colorBtnDisable = 'btn-danger';
            $iconBtnDisable = 'fas fa-toggle-off';
        }
        
        $fnDisable = '<a href="#" '.$onclickDisable.' rel="'.RELUPDATE.'"  class="btn '.$colorBtnDisable.'" ><i class="'.$iconBtnDisable.'"></i></a>';
        
        $data[] = array(
            "ID"=> $row['ID'],
            "CODICE"=> $row['CODICE'],
            "NOTE"=>$row['NOTE'],            
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
    $rec = new AnagraficaMemoryCard($_POST);    
    $response = $rec->Save();
    exit(json_encode($response));
}


function delete(){
    $id = Utils::getFromReq("id",0);          
    $record = new AnagraficaMemoryCard($id);    
    $return = $record->LogicalDelete();
    exit(json_encode($return));
}

function riattiva(){
    $id = Utils::getFromReq("id",0);          
    $record = new AnagraficaMemoryCard($id);    
    $record->CANCELLATO = 0;
    $return = $record->Save();
    exit(json_encode($return));
}
