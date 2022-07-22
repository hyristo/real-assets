<?php
$action=$_REQUEST["action"];
switch($action){

    case 'list':
        list_eventi();
        break;
}

exit();

function list_eventi(){

    $events = Events::ListEventi(0);
    $oggi = new DateTime();
    foreach($events as $row){
        $dtRata = new DateTime($row['START_DATE']);
        $start = strtotime($row['START_DATE']) * 1000;
        $end = strtotime($row['END_DATE']) * 1000;
        $url="#";
        if(intval($row['ID_CONTRATTO'])>0){
            $url = "javascript:goToContratto('".$row['ID_PATRIMONIO']."', '".intval($row['ID_CONTRATTO'])."')";
        }
        $css_event = 'event-info';
        $css_list = 'text-info';
        $stato_event = '';
        if(intval($row['RATA_PAGATA']) > 0 ){
            $css_event = 'event-success';
            $css_list = 'text-success';
            $stato_event = ' Rata pagata';
        }else if(intval($row['RATA_PAGATA']) == 0 && $dtRata >= $oggi){
            $css_event = 'event-warning';
            $css_list = 'text-warning';
            $stato_event = ' Rata in scadenza';
        }else if(intval($row['RATA_PAGATA']) == 0 && $dtRata < $oggi){
            $css_event = 'event-important';
            $css_list = 'text-important';
            $stato_event = ' Rata scaduta';
        }
        $calendar[] = array(
            'id' =>$row['ID'],
            'title_list' => $row['DESCRIPTION'],
            'css_list' => $css_list,
            'title' => $row['TITLE']." - ".$row['DESCRIPTION']. " - ".$stato_event,
            'url' => $url,
            "class" => $css_event,
            'start' => "$start",
            'end' => "$end"
        );
    }
    $calendarData = array(
        "success" => 1,
        "result"=>$calendar);
    exit(json_encode($calendarData));

}