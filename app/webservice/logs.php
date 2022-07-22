<?php

switch($_REQUEST['action'])
{
	case "list":
            listRecs();
	break;
        case "load";
            loadRec();
        break;
	case "listLogs":
            listLogs();
        break;
	case "listParsing":
            listParsing();
        break;
        case "listDataTable":
            listDataTable();
            break;
        
}
exit();

function listDataTable(){
    $draw = $_POST['draw'];
    $row = $_POST['start'];
    $rowperpage = $_POST['length']; // Rows display per page
    $columnIndex = $_POST['order'][0]['column']; // Column index
    $columnName = $_POST['columns'][$columnIndex]['data']; // Column name
    $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
    $searchValue = $_POST['search']['value']; // Search value
    $data = array();
    $searchArray = array();
    ## Search     
    if($searchValue != ''){
       $searchQuery = " AND ( lower(module) LIKE :module OR lower(action) LIKE :action OR lower(username) LIKE :username ) ";
       $searchArray = array( 
            'module'=>"%$searchValue%",
            'action'=>"%$searchValue%",
            'username'=>"%$searchValue%"
       );
    }
    
    //"<pre>".print_r($_POST, true)."</pre>";
    
    $res = Logs::LoadDataTable($searchQuery, $searchArray, $columnName,$columnSortOrder, $row, $rowperpage);
    
    //echo "<pre>".print_r($res, true)."</pre>";
    
    foreach($res['empRecords'] as $row){
        
        $fnVisualizza = '<a href="'.BASE_HTTP.'modules/logs/logs.php?mode=viewLog&id='.($row['ID']).'" class="mb-6 btn-floating waves-effect waves-light purple lightrn-1"><i class="far fa-window-restore"></i></a>';
        //$fnDisable = '<a href="#" '.$onclickDisable.' rel="'.RELUPDATE.'"  class="btn-floating mb-1 '.$colorBtnDisable.' waves-effect waves-light "><i class="material-icons dp48">'.$iconBtnDisable.'</i></a>';
        
        
        
        $data[] = array(
            "ID"=>$row['ID'],
            "IP"=>$row['IP'],
            "USERNAME"=>$row['USERNAME'],
            "DATA"=> Date::FormatDate($row['DATA']),
            "MODULE"=>$row['MODULE'],
            "ACTION"=>$row['ACTION'],
            "visualizza"=>$fnVisualizza
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

function loadRec()
{
    $ID = $_REQUEST['id'];
    $log = new Logs($ID);
    $logArray = Utils::ObjectToArray($log); 
    $as = Utils::GetTimestamp($logArray['Data']);
    $logArray['DataOra'] = date('d/m/Y H:i:s', $as);
    $record[] = $logArray;
    exit(Utils::jsonEncodeDbRows($record, count($record)));
}

function listLogs()
{
    
    $id_operatore = $_REQUEST['id_operatore'];
    $dal = trim(stripslashes($_REQUEST['Dal']));
    $al = trim(stripslashes($_REQUEST['Al']));
    $Action = '';//$_REQUEST['action'];

    $record = array();
    $count = 0;
    $record = Logs::Load($dal, $al, $id_operatore, $Action);
	//else $record = Logs::Load($ID_Account, $data, $Action, $limit, $start, $count, true);
    exit(json_encode($record));
}

function listParsing()
{
	$ID = $_REQUEST['id'];
	$oldValue = $_REQUEST['oldValue'];
	
        $log = new Logs($ID);
        
        $record = $log->ParseMessage();
        exit(json_encode($record));
}