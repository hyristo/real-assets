$(document).ready(function () {
var ul = document.querySelector('#spid-idp-list-medium-root-post');
for (var i = ul.children.length; i >= 0; i--) {
	ul.appendChild(ul.children[Math.random() * i | 0]);
}
});

function entraSpid(is) {
    var url = DOMINIO_SPID+eval(is);
    //console.log(url);    
    document.location.href = url;
}

function loginSpid() {
    $('#loader').show();
    var form = document.getElementById('form-login');
    var formInvio = new FormData(form);
    postdata(WS_CALL + "?module=account&action=login_spid", formInvio, function (response) {
          $('#loader').hide();
        var risp = jQuery.parseJSON(response);
        if (risp.esito === 1) {
            document.location.href = "dashboard.php";
        } else {
            $('#loader').hide();
            functionSwall('error', risp.erroreDescrizione, "");

        }
    });
}

//function loginSpidClasse() {
//    $('#loader').show();
//    var form = document.getElementById('form-login');
//    var formInvio = new FormData(form);
//    postdata(WS_CALL + "?module=account&action=login_spid", formInvio, function (response) {
//          $('#loader').hide();
//        var risp = jQuery.parseJSON(response);
//        if (risp.esito === 1) {
//            document.location.href = "dashboard.php";
//        } else {
//            $('#loader').hide();
//            functionSwall('error', risp.erroreDescrizione, "");
//
//        }
//    });
//}