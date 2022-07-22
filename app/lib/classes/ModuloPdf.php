<?php

include_once (ROOT . "lib/api.php");
require_once (ROOT . "lib/modules/tcpdf/tcpdf.php");

class ModuloPdf extends TCPDF {

    //Page header
    public function Header() {
        // Logo + titolone
//                $htmlHeader = '
//                    <table cellpadding="1" cellspacing="1" border="0" >
//                        <tr>
//                            <td align="center" >REPUBBLICA ITALIANA<br/>SERVIZIO FITOSANITARIO ITALIANO</td>
//                        </tr>
//                        <tr>
//                            <td align="center" ><img src="'.ROOT.'/app-assets/images/gallery/LogoRegioneSicilia.png" width="27" alt=""/></td>
//                        </tr>
//                        <tr>
//                            <td align="center" >Regione Sicilia</td>
//                        </tr>
//                        <tr>
//                            <td align="center" >
//                                ASSESSORATO REGIONALE DELL\'AGRICOLTURA DELLO SVILUPPO RURALE E DELLA PESCA MEDITERRANEA<br/>
//                                Dipartimento Regionale Dell\'Agricoltura<br/>
//                                4. Servizio Fitosanitario Regionale e Lotta alla Contraffazione<br/>
//                            </td>
//                        </tr>
//                    </table>';
//                
//                $this->writeHTML($htmlHeader, true, false, true, false, '');
    }

    // Page footer
    public function Footer() {
        
    }

}

class PDF_DOMANDA extends TCPDF {

    private $customFooterText = "";

    //Page header
    public function Header() {
    }

    public function setCustomFooterText($customFooterText) {
        $this->customFooterText = $customFooterText;
    }

    // Page footer
    public function Footer() {
        $this->Cell(0, 10, $this->customFooterText, 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }

}
