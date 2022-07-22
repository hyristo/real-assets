<!doctype html>
<? include 'lib/api_s.php'; ?>
<html lang="en">
    <?
    include_once ROOT . 'layout/head.php';    
    ?>
    <body>
        <?
        include_once ROOT . 'layout/header_home.php';    
        include_once ROOT . 'layout/loader.php';
        ?>
        <header class="mastheadlogin masthead-page mb-5 ">
        <?
                include_once ROOT . 'layout/header_svg.php';
                ?>
        </header>
         <main role="main">
        <form class="form-signin text-center bg-light" id="form-login" action="javascript:login()">
            <h1 class="mb-3 font-weight-normal"><i class="fab fa-battle-net"></i>&nbsp;<?= APP_NAME ?></h1>
            <small class="text-info"><?=APP_DESCR?></small>
            <div class="input-group">
                <label for="inputEmail" class="sr-only">Email</label>
                <input type="email" id="email" class="form-control" name="email" placeholder="Email" required autofocus>
            </div>
            <div class="input-group">                
                <input type="password" id="inputPassword" name="password" class="form-control" id="inputPassword" placeholder="Password" required>
                <span class="input-group-btn">
                    <button class="btn btn-default reveal" type="button"><i class="fas fa-eye-slash"></i></button>
                </span>
            </div>
            <button class="btn btn-lg btn-primary btn-block"  id="submitValidation" type="submit">Entra</button>
            <br>
            <div class="row">
                <div class="col-sm-12 text-center">
                    <a  href="#" data-toggle="modal" data-target="#exampleModal"><b>Hai dimenticato la password?</b></a>
                </div>
            </div>            
        </form>
        <input type="hidden" id="token"  name="token" class="form-control"/>
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Hai dimenticato la password?</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <small id="emailHelp" class="form-text text-muted">Inserisci la tua mail per ricevere un link per il reset della password</small>
                            </div>
                        </div><br>
                        <div class="row">
                            <div class="col-sm-12">
                                <input type="email" class="form-control" name="emailRecupero" id="emailRecupero" aria-describedby="emailHelp" placeholder="Indirizzo email">
                            </div>
                        </div>
                        <br>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
                        <button type="button" onclick="sendPassword()" class="btn btn-primary">Invia</button>
                    </div>
                </div>
            </div>
        </div>
        </main>
        <? include_once ROOT . 'layout/footer.php'; ?>
        <script src="js/utility.js" type="text/javascript"></script>
        <script src="js/login.js" type="text/javascript"></script>
    </body>
</html>
