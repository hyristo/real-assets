$(document).ready(function () {
    $(".reveal").on('click', function () {
        var $pwd = $("#inputPassword");
        if ($pwd.attr('type') === 'password') {
            $pwd.attr('type', 'text');
            $(".reveal i").removeClass("fa-eye-slash");
            $(".reveal i").addClass("fa-eye");

        } else {
            $pwd.attr('type', 'password');
            $(".reveal i").addClass("fa-eye-slash");
            $(".reveal i").removeClass("fa-eye");
        }
    });
});

function login() {
    $('#loader').show();
    var form = document.getElementById('form-login');
    var formInvio = new FormData(form);
    postdata(WS_CALL + "?module=account&action=login", formInvio, function (response) {
//        console.log(response);
        var risp = jQuery.parseJSON(response);
        console.log(risp);
        if (risp.esito === 1) {
            document.location.href = "modules/private/dashboard_admin.php";
        } else {
            $('#loader').hide();
            if (risp.bottone == 1) {
                $('#token').val(risp.token);
                Swal.fire({
                    title: 'Attenzione',
                    text: risp.erroreDescrizione,
                    type: 'warning',
                    showCancelButton: true,
                    cancelButtonText: "Chiudi",
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: "Invia mail di verifica"
                }).then((result) => {
                    if (result.value) {
                        sendMail();
                    }
                });
            } else {
                functionSwall('error', risp.erroreDescrizione, "");
            }

        }
    });
}
function sendMail() {
    $('#loader').show();
    var token = $('#token').val();
    var Object = {
        token: token
    };
    postdataClassic(WS_CALL + "?module=account&action=sendMail", Object, function (response) {
        $('#loader').hide();
        var risp = jQuery.parseJSON(response);
        if (risp.esito === 1) {
            functionSwall('success', 'Operazione Avvenuta con successo!!', BASE_HTTP + 'login.php');
        } else {
            functionSwall('error', risp.erroreDescrizione, "");
        }
    });
}
function sendPassword() {
    $('#loader').show();
    var email = $('#emailRecupero').val();
    var object = {
        email: email
    };
    if (email != "") {
        postdataClassic(WS_CALL + "?module=account&action=sendPassword", object, function (response) {
            $('#loader').hide();
            var risp = jQuery.parseJSON(response);
            if (risp.esito === 1) {
                $('#exampleModal').modal('toggle');
                functionSwall('success', 'Operazione Avvenuta con successo!!', BASE_HTTP + 'login.php');
            } else {
                functionSwall('error', risp.erroreDescrizione, "");
            }
        });
    } else {
        $('#loader').hide();
        functionSwall('error', "Attenzione inserire l'indirizzo E-mail", "");
    }
}

//function loginJsp() {
//    $('#loader').show();
//    var username = $('#email').val();
//    var password = $('#inputPassword').val();
//    firebase.auth().signInWithEmailAndPassword(username, password)
//            .then(function (result) {
//                var object = {
//                    email: result.user.email,
//                    idToken: result.user.refreshToken,
//                    id: result.user.uid,
//                    emailVerified: result.user.emailVerified
//                };
//                $.ajax({
//                    url: WS_CALL + "?module=accountJsp&action=login",
//                    method: "POST",
//                    data: object,
//                    success: function (response) {
//                        var risp = jQuery.parseJSON(response);
//                        if (risp.esito === 1) {
//                            document.location.href = "dashboard.php";
//                        } else {
//                            $('#loader').hide();
//                            if (risp.bottone == 1) {
//                                $('#token').val(risp.token);
//                                Swal.fire({
//                                    title: 'Attenzione',
//                                    text: risp.erroreDescrizione,
//                                    type: 'warning',
//                                    showCancelButton: true,
//                                    cancelButtonText: "Chiudi",
//                                    confirmButtonColor: '#3085d6',
//                                    cancelButtonColor: '#d33',
//                                    confirmButtonText: "Invia mail di verifica"
//                                }).then((result) => {
//                                    if (result.value) {
//                                        sendMail();
//                                    }
//                                })
//                            } else {
//                                functionSwall('error', risp.erroreDescrizione, "");
//                            }
//                        }
//                    }
//                });
//            }).catch(function (error) {
//        $('#loader').hide();
//        firebaseError(error.code);
//    });
//
//}
