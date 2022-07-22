<?php

$mode = $_REQUEST['action'];
switch ($mode) {
    case 'loadStatistiche':
        loadStatistiche();
        break; // 
    case 'loadStatisticheMese':
        loadStatisticheMese();
        break; // 
}


function loadStatistiche() { 
    global $MESI;
    $rec = array();
    $rec_mese = array();
    $anno = date('Y');   
    $datasets= array();    
    $datasets_mese= array();    
    $donazioni = new Donazioni();           
    $stats = $donazioni->GetStaticsPro();   

    $datasets['labels'] = array();    
    $label_tmp = '';
    foreach($stats as $v){
        if($label_tmp!=$v['DESCRIZIONE']){
            $label_tmp = $v['DESCRIZIONE'];
            $datasets['labels'][]= $v['DESCRIZIONE'];
        }
        
        
        $datasets[] = $v['TOTALE'];
        
    }
    //Utils::print_array($datasets);
    
    $label_tmp_mese = '';
    $mese_tmp = '';
    $labels = array();
    $stats_mese = $donazioni->GetStaticsProMese($anno);
    foreach($stats_mese as $v){
        if($mese_tmp!=$v['MESE']){
            $mese_tmp = $v['MESE'];
            $labels[$v['MESE']]= $MESI[$v['MESE']];
        }
        if($label_tmp_mese!=$v['DESCRIZIONE']){
            $rgb = Utils::randomColor();
            $label_tmp_mese = $v['DESCRIZIONE'];
            $datasets_mese[$v['DESCRIZIONE']]['label']= $v['DESCRIZIONE'];            
            //$datasets[$v['DESCRIZIONE']]['borderColor']= 0;            
            $datasets['backgroundColor'][] = "rgba(".$rgb['rgb'].")";
           
        }
        $datasets_mese[$label_tmp_mese]['data'][$MESI[$v['MESE']]] = $v['TOTALE'];
        $datasets_mese[$v['DESCRIZIONE']]['backgroundColor'][]= "rgba(".$rgb['rgb'].")";
        
        //$datasets['label'][$v['DESCRIZIONE']] = $v['TOTALE'];
        
        //$datasets[$v['MESE']][] = $v['TOTALE'];
        
    }

    foreach ($labels as $k => $value) {        
        $rec_mese['labels'][] = $value;
    }
    //Utils::print_array($datasets);
    foreach ($datasets_mese as $k => $value) {      
        $rec_mese['datasets'][] = $value;
    }



    
    $rec['pie']['datasets'] = $datasets;  
    $rec['bar'] = $rec_mese;  
    
    exit(json_encode(array($rec)));
}

function loadStatisticheMese(){
    global $MESI;
    
    $anno = date('Y');
    $donazioni = new Donazioni();           
    $stats = $donazioni->GetStaticsProMese($anno);

    $label_tmp = '';
    $mese_tmp = '';
    $labels = array();
    $datasets= array();
    
    //Utils::print_array($rgb);
    foreach($stats as $v){
        if($mese_tmp!=$v['MESE']){
            $mese_tmp = $v['MESE'];
            $labels[$v['MESE']]= $MESI[$v['MESE']];
        }
        if($label_tmp!=$v['DESCRIZIONE']){
            $rgb = Utils::randomColor();
            $label_tmp = $v['DESCRIZIONE'];
            $datasets[$v['DESCRIZIONE']]['label']= $v['DESCRIZIONE'];            
            //$datasets[$v['DESCRIZIONE']]['borderColor']= 0;            
           
        }
        $datasets[$label_tmp]['data'][$MESI[$v['MESE']]] = $v['TOTALE'];
        $datasets[$v['DESCRIZIONE']]['backgroundColor'][]= "rgba(".$rgb['rgb'].")";
        
        //$datasets['label'][$v['DESCRIZIONE']] = $v['TOTALE'];
        
        //$datasets[$v['MESE']][] = $v['TOTALE'];
        
    }
    //Utils::print_array($datasets);
    foreach ($labels as $k => $value) {        
        $rec['labels'][] = $value;
    }
    //Utils::print_array($datasets);
    foreach ($datasets as $k => $value) {      
        $rec['datasets'][] = $value;
    }
    
    
    //$rec['datasets'][] = $datasets;  


    exit(json_encode(array($rec)));
}
