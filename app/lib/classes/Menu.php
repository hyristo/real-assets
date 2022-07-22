<?php

/**
 * class Menu
 *
 * Classe di gestione del Menu dell'applicazione
 */
class Menu {

    public $id = null;
    public $MenuItems = array();
    public $itemClickHandler;

    public function __construct($menuNav = true) {
        $this->itemClickHandler = $this->itemClickHandler;
        if ($menuNav) {
            $this->_loadMenuStaticArray();
        } else {
            $this->_loadNavBarStaticArray();
        }
    }

    private function _loadNavBarStaticArray() {
        global $NAVITEMS;
        foreach ($NAVITEMS as $submodule) {
            //echo "<pre>".print_r($submodule, true)."</pre>";exit();
            $menu = new MenuItem($submodule['text'], $submodule['url'], $submodule['id'], $submodule['handler'], $submodule['icon'], $submodule['description'], $submodule['super_user'], $submodule['MenuItems']);

            $this->_addToMenu($submodule['text'], $menu);
        }
    }

    private function _loadMenuStaticArray() {
        global $MENUITEMS;
        foreach ($MENUITEMS as $submodule) {
            //echo "<pre>".print_r($submodule, true)."</pre>";exit();
            $menu = new MenuItem(Utils::getArrayValueOrNull($submodule, 'text'), Utils::getArrayValueOrNull($submodule, 'url'), Utils::getArrayValueOrNull($submodule, 'id'), Utils::getArrayValueOrNull($submodule, 'handler'), Utils::getArrayValueOrNull($submodule, 'icon'), Utils::getArrayValueOrNull($submodule, 'description'), Utils::getArrayValueOrNull($submodule, 'MenuItems'));

            $this->_addToMenu(Utils::getArrayValueOrNull($submodule, 'text'), $menu);
        }
    }

    private function _loadModules() {
        global $App;
        foreach ($App->Modules as $module) {
            $this->_loadModule($module);
        }
    }

    private function _loadModule($module) {
        foreach ($module->Modules as $submodule) {
            $menu = new MenuItem($submodule->Text, $submodule->URL, $submodule->ID);
            if (count($submodule->Modules) == 0)
                $menu->handler = $this->itemClickHandler;
            else
                $menu->MenuItems = $this->_loadSubModules($submodule);
            $this->_addToMenu($module->Text, $menu);
        }
    }

    private function _loadSubModules($module) {
        $submenus = array();
        foreach ($module->Modules as $submodule) {
            $menu = new MenuItem($submodule->Text, $submodule->URL, $submodule->ID);
            if (count($submodule->Modules) == 0)
                $menu->handler = $this->itemClickHandler;
            else
                $menu->MenuItems = $this->_loadSubModules($submodule);
            $submenus[] = $menu;
        }
        return $submenus;
    }

    private function _addToMenu($menu, $item) {
        if (!array_key_exists($menu, $this->MenuItems))
            $this->MenuItems[$menu] = array();
        $this->MenuItems[$menu][] = $item;
    }

    private function _RenderItem($items) {
        //echo $_SESSION;
        global $LoggedAccount;
        foreach ($items as $k => $v) {
            //if($_SESSION['ruolo'] == $k){

            for ($i = 0; $i < count($v); $i++) {

                $icona = '';
                $pos = strpos($v[$i]->icon, 'fas ');
                if ($pos === false) {
                    $icona = '<span data-feather="' . $v[$i]->icon . '"></span>';
                } else {
                    $icona = '<i class="' . $v[$i]->icon . '"></i>&nbsp;';
                }

                if (count($v[$i]->MenuItems) <= 0) {
                    //echo $v[$i]->super_user;
                    if ($v[$i]->super_user == 0 || $v[$i]->super_user == $LoggedAccount->SUPER_USER) {
                        if ($v[$i]->handler != "") {
                            $render .= '<li class="nav-item" id="li-' . $v[$i]->id . '"><a onClick="' . $v[$i]->handler . '" class="nav-link" href="#" id="a-' . ($v[$i]->id) . '" data-toggle="tooltip" data-placement="bottom" title="' . $v[$i]->description . '"  data-i18n = "">' . $icona . $v[$i]->text . '</a></li>'; /// PRIMO LIVELLO PADRI SENZA FIGLI
                        } else {
                            $render .= '<li class="nav-item" id="li-' . $v[$i]->id . '"><a class="nav-link" href="' . $v[$i]->url . '#' . ($v[$i]->id) . '" id="a-' . ($v[$i]->id) . '" data-toggle="tooltip" data-placement="bottom" title="' . $v[$i]->description . '"  data-i18n = "">' . $icona . $v[$i]->text . '</a></li>'; /// PRIMO LIVELLO PADRI SENZA FIGLI
                        }
                    }
                }
//                        else
//                        {
//                            $render .= '<li class="bold" id="li-'.$v[$i]->id.'" ><a class="collapsible-header waves-effect waves-cyan tooltipped" data-html="true" data-position="right" data-tooltip="'.($v[$i]->description).'" id="a-'.($v[$i]->id).'" href="#">'.$icona.'<span class="menu-title" data-i18n="">'.$v[$i]->text.'</span></a>';/// PRIMO LIVELLO PADRI CON FIGLI
//                            $render .= '<div class="collapsible-body">
//                                        <ul class="collapsible collapsible-sub" data-collapsible="accordion">';                                
//                            foreach ($v[$i]->MenuItems as $v1)
//                            {
//                                $render .= $this->_RenderItem($v1);
//
//                            }
//                            $render .= "</ul></div></li>";
//                        }
            }
            //}
        }

        return $render;
    }

    public function RenderNavBar($asideId, $menuId) {
        global $LoggedAccount;

        $render = '<nav id="' . $asideId . '" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">'
                . '<div class="sidebar-sticky pt-3">'
                . '<ul class="nav flex-column">';

        $render .= $this->_RenderItem($this->MenuItems);

        $render .= '</ul></div>
            </nav>';

        return $render;
    }

    public function RenderMenuBar($asideId, $menuId) {
        global $LoggedAccount;

        $render = '<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-primary">
                <a class="navbar-brand" href="#">' . APP_NAME . '</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#' . $asideId . '" aria-controls="' . $asideId . '" aria-expanded="false" aria-label="Toggle navigation">
                      <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="' . $asideId . '">
                      <ul class="navbar-nav lg-auto mb-2 mb-lg-0">';//navbar-nav mr-auto
        $render .= $this->_RenderItem($this->MenuItems);

        $render .= '</ul></div> <span class="text-white"><i class="far fa-user"></i>&nbsp;'.$LoggedAccount->Anagrafica->COGNOME.' '.$LoggedAccount->Anagrafica->NOME.'</span>
            </nav>';

        return $render;
    }

    public function RenderMaterialize($asideId, $menuId) {
        global $LoggedAccount;
        $linkDash = "";
        $classNavBar = "sidenav-dark grey darken-4 sidenav-active-rounded";
        if ($LoggedAccount->id_gruppo == GRUPPO_UTENTE) {
            $linkDash = BASE_HTTP . "index.php#DASHBOARD";
            $classNavBar = "sidenav-dark green darken-3 sidenav-active-rounded";
        } else if ($LoggedAccount->id_gruppo == GRUPPO_CORSISTA) {
            $linkDash = BASE_HTTP . "corsista.php#DASHBOARD";
            $classNavBar = "sidenav-light sidenav-active-square";
        } else {
            $linkDash = BASE_HTTP . "admin/index.php#DASHBOARD";
        }


        $render = '<aside id=' . $asideId . ' class="sidenav-main nav-expanded nav-lock nav-collapsible  ' . $classNavBar . ' ">
                <div class="brand-sidebar">
                    <h1 class="logo-wrapper">
                        <a class="brand-logo " href="index.php"><img src="' . BASE_HTTP . '/app-assets/images/logo/fitosan2.png" alt="fitosan"/><span class="logo-text hide-on-med-and-down" style=" font-size: 25px;">Fitosan</span></a>
                        <a class="navbar-toggler" href="#"><i class="material-icons">radio_button_checked</i></a>
                    </h1>
                </div>
                <ul class="sidenav sidenav-collapsible leftside-navigation collapsible sidenav-fixed menu-shadow" id="' . $menuId . '" data-menu="menu-navigation" data-collapsible="accordion">
                    <li class="navigation-header"><a class="navigation-header-text">GENERALE</a><i class="navigation-header-icon material-icons">more_horiz</i></li>
                    <li class="bold"><a class="waves-effect waves-cyan " href="' . $linkDash . '" id="a-DASHBOARD"><i class="fas fa-tachometer-alt"></i><span class="menu-title" data-i18n="">Dashboard</span></a></li>
                ';

        $render .= $this->_RenderItem($this->MenuItems);

        $render .= '</ul>
                <div class="navigation-background"></div><a class="sidenav-trigger btn-sidenav-toggle btn-floating btn-medium waves-effect waves-light hide-on-large-only" href="#" data-target="slide-out"><i class="material-icons">menu</i></a>
            </aside>';

        return $render;
    }

    public function RenderStaticNavBar($asideId, $menuId) {
        global $LoggedAccount, $MENUITEMS;
        //Utils::print_array($MENUITEMS);exit();
        $navbar = '<nav class="navbar navbar-expand-xl navbar-dark fixed-top bg-primary" id="' . $asideId . '">';
        //$navbar .= '<a class="navbar-brand" href="#">'.APP_NAME.' - <span class="badge badge-dark">ApplicationVersion::get()</span></a>';
        $navbar .= '<h6 class="my-auto"><a class="navbar-brand" href="#"><i class="fab fa-battle-net"></i>&nbsp;' . APP_NAME . '</a> <span class="text-white">&nbsp;'.$LoggedAccount->Anagrafica['COGNOME'].' '.$LoggedAccount->Anagrafica['NOME'].'</span></h6>';
        $navbar .= '<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#' . $menuId . '" aria-controls="' . $menuId . '" aria-expanded="false" aria-label="Toggle navigation">'
                . '<span class="navbar-toggler-icon"></span>'
                . '</button>';
        $navbar .= '<div class="collapse navbar-collapse" id="' . $menuId . '">';
        if (count($MENUITEMS)) {
            $navbar .= '<ul class="navbar-nav ml-auto mb-2 mb-xl-0">';
            foreach ($MENUITEMS as $value) {
                
                if ($value['super_user'] == 1 && ($LoggedAccount->SUPER_USER == 0 && !in_array($LoggedAccount->GRUPPO_ID , $value['group_id']))) {
                    continue;
                }
                if (isset($value['children']) && count($value['children']) > 0) {

                    $navbar .= '<li class="nav-item dropdown">'
                            . '<a class="nav-link dropdown-toggle" href="#" id="' . $value['id'] . '" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">'
                            . '<i class="' . $value['icon'] . '"></i>&nbsp;' . $value['text'] . '</a>'
                            . '<div class="dropdown-menu" aria-labelledby="' . $value['id'] . '">';
                    foreach ($value['children'] as $v) {
                        
                        if ($v['super_user'] == 1 && ($LoggedAccount->SUPER_USER == 0 && !in_array($LoggedAccount->GRUPPO_ID , $v['group_id'])))
                            continue;
                        if ($v['handler'] != "") {
                            $navbar .= '<a class="dropdown-item" id="' . $v['id'] . '" onclick="' . $v['handler'] . '" href="#"><i class="' . $v['icon'] . '"></i>&nbsp;' . $v['text'] . '</a>';
                        } else {
                            $navbar .= '<a class="dropdown-item" id="' . $v['id'] . '" href="' . $v['url'] . '"><i class="' . $v['icon'] . '"></i>&nbsp;' . $v['text'] . '</a>';
                        }
                    }


                    $navbar .= '</div>';
                    $navbar .= '</li>';
                } else {


                    if ($value['handler'] != "") {
                        $navbar .= '<li class="nav-item">'
                                . '<a class="nav-link" id="' . $value['id'] . '" onClick="' . $value['handler'] . '" href="#" title="' . $value['description'] . '"><i class="' . $value['icon'] . '"></i>&nbsp;' . $value['text'] . '</a>'
                                . '</li>';
                    } else {
                        $navbar .= '<li class="nav-item">'
                                . '<a class="nav-link" id="' . $value['id'] . '" href="' . $value['url'] . '" title="' . $value['description'] . '"><i class="' . $value['icon'] . '"></i>&nbsp;' . $value['text'] . '</a>'
                                . '</li>';
                    }
                }
            }
            $navbar .= '</ul>';
        }


        $navbar .= '</div> ';
        $navbar .= '</nav>';
        return $navbar;
    }
    
    public static function renderHomeStaticMenu() {
        $render = "";
        //if (!Utils::isStatoSportelloManutezione() && (Utils::isStatoSportelloPresentazione()) && Utils::PeriodoFaq()) {
        if (!Utils::isStatoSportelloManutezione() && Utils::PeriodoFaq()) {
            $render .= '<li class="nav-item" id="li-DASHBOARD">
                <a class="nav-link" href="faq.php">
                    <i class="fas fa-tasks"></i><span data-feather="faq"></span> F.A.Q.
                </a>
            </li>';
        }
//        if (Utils::isStatoSportelloManifestazione()) {
//            $render .= '<li class="nav-item" id="li-DASHBOARD">
//                <a class="nav-link" href="registrazione.php">
//                    <i class="fas fa-user-alt"></i><span data-feather="registrazione"></span> Registrazione
//                </a>
//            </li>';
//        }

        if (!Utils::isStatoSportelloManutezione() && (Utils::isStatoSportelloPresentazione(true))) {
            $render .= '<li class="nav-item" id="li-DASHBOARD">
                <a class="nav-link" href="loginspid.php">
                    <i class="fas fa-sign-in-alt"></i><span data-feather="login"></span> Login
                </a>
            </li>';
        }
        return $render;
    }
    
    public static function renderInfoStaticMenu(){
        $render = "";        
        $render .= '<li class="nav-item" id="li-LOGOUT">
            <a class="nav-link" href="'.BASE_HTTP.'logout.php">
                <i class="fas fa-sign-out-alt"></i><span data-feather="login"></span> Esci
            </a>
        </li>';        
        return $render;
    }
    
    public function RenderStaticDashboard($asideId) {
        global $LoggedAccount, $MENUITEMS;
        $navbar = '<div class="row p-5" id="' . $asideId . '">';
        
        //$navbar .= '<a class="navbar-brand" href="#">'.APP_NAME.' - <span class="badge badge-dark">ApplicationVersion::get()</span></a>';
        $navbar .= '<h3 class="col-sm-12 text-white">Benvenuto '.$LoggedAccount->Anagrafica['COGNOME'].' '.$LoggedAccount->Anagrafica['NOME'].'</h3>';
        $navbar .= '<h6 class="col-sm-12 text-white">Questi sono i servizi attivi:</h6>';
        if (count($MENUITEMS)) {            
            foreach ($MENUITEMS as $value) {
                
                if ($value['super_user'] == 1 && ($LoggedAccount->SUPER_USER == 0 && !in_array($LoggedAccount->GRUPPO_ID , $value['group_id']))) {
                    continue;
                }
                if (isset($value['children']) && count($value['children']) > 0) {
                    foreach ($value['children'] as $v) {
                        if ($v['super_user'] == 1 && ($LoggedAccount->SUPER_USER == 0 && !in_array($LoggedAccount->GRUPPO_ID , $v['group_id'])))
                                            continue;
                        $navbar .='<div class="col-sm-4 p-1">
                                <div class="card bg-warning mb-3" style="width: 18rem;">                                    
                                    <div class="card-body">
                                      <h5 class="card-title">'.$v['text'].'</h5>
                                      <small class="card-text">'.$v['description'].'</small>';

                        $navbar .='</div>';
                        if ($v['handler'] != "") {
                            $navbar .= '<div class="card-footer"><a class="btn bg-card6" id="' . $v['id'] . '" onclick="' . $v['handler'] . '" href="#"><i class="' . $v['icon'] . '"></i></a></div>';
                        } else {
                            $navbar .= '<div class="card-footer"><a class="btn bg-card6" id="' . $v['id'] . '" href="' . $v['url'] . '"><i class="' . $v['icon'] . '"></i></a></div>';
                        }
                        $navbar .='</div>
                            </div>';
                    }
                } else {
                    if($value['id'] == 'DASHBOARD' || $value['id'] == 'LOGOUT'){
                        continue;
                    }
                    $navbar .='<div class="col-sm-4 p-1">
                                <div class="card bg-warning mb-3" style="width: 18rem;">                                    
                                    <div class="card-body">
                                      <h5 class="card-title">'.$value['text'].'</h5>
                                      <small class="card-text">'.$value['description'].'</small>';
                        

                        $navbar .='</div>';
                                if ($value['handler'] != "") {
                                    $navbar .= '<div class="card-footer"><a class="btn bg-card6" id="' . $value['id'] . '" onclick="' . $value['handler'] . '" href="#"><i class="' . $value['icon'] . '"></i></a></div>';
                                } else {
                                    $navbar .= '<div class="card-footer"><a class="btn bg-card6" id="' . $value['id'] . '" href="' . $value['url'] . '"><i class="' . $value['icon'] . '"></i></a></div>';
                                }
                    $navbar .='</div>
                            </div>';

                    
                }
            }            
        }


        $navbar .= '</div> ';        
        return $navbar;
    }

}

class MenuItem {

    public $text = null;
    public $description = null;
    public $url = null;
    public $icon = null;
    public $id = null;
    public $handler = null;
    public $super_user = null;
    public $group_id = null;
    public $MenuItems = array();

    public function __construct($_text, $_url = null, $_id = null, $_handler = null, $_icon = null, $_desc = null, $_super_user = null, $group_id = null, $_child = array()) {
        $this->text = $_text;
        $this->url = $_url;
        $this->icon = $_icon;
        $this->id = $_id;
        $this->handler = $_handler;
        $this->description = $_desc;
        $this->super_user = $_super_user;
        $this->group_id = $group_id;
        $this->MenuItems = $_child;
    }

    public function RenderMaterialize() {
        
    }

    public function HasChild() {
        return count($this->MenuItems) > 0;
    }

}
