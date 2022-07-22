<?php

Utils::checkLogin();
switch ($_POST['action']) {
    case 'download':
        download();
        break;
}

function download() {
    global $LoggedAccount;

    $id_allegato = intval($_POST['id']);
    if($id_allegato>0){
        $allegato = new ContrattiDocumenti($id_allegato);
        $file= $allegato->PATH;
        $path = $allegato->ID_CONTRATTO."/".ROOT_UPLOAD_DOC;
        $folder = File::generateDirForUpload(ROOT_UPLOAD_DOCUMENTI, $path);
        $filePath = $folder . $file;
        if (file_exists($filePath) && filesize($filePath) > 0) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . File::GetValidFilename($file));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filePath));
            ob_clean();
            flush();
            readfile($filePath);
            exit;
        } else {
            $msg = "Non è possibile visualizzare il file richiesto, contattare il supporto tecnico (1316).";
        }
        echo $msg;
    }


}

