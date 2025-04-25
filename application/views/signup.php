<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>MY MILES - Buscador Smiles</title>
    <!-- Favicon-->
    <link rel="icon" href="<?= base_url() ?>/favicon.ico" type="image/x-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">

    <!-- Bootstrap Core Css -->
    <link href="<?= base_url() ?>plugins/bootstrap/css/bootstrap.css" rel="stylesheet">

    <!-- Waves Effect Css -->
    <link href="<?= base_url() ?>plugins/node-waves/waves.css" rel="stylesheet" />

    <!-- Animation Css -->
    <link href="<?= base_url() ?>plugins/animate-css/animate.css" rel="stylesheet" />

    <!-- Custom Css -->
    <link href="<?= base_url() ?>css/style.css" rel="stylesheet">
</head>

<body class="login-page">
    <div class="login-box">
        <div class="logo">
            <a href="javascript:void(0);"><b>MY MILES</b></a>
        </div>
        <div class="card">
            <div class="body">
                <form class="form-signin" method="post" id="login-form">
                    <div class="msg"><b>Registrarse como nuevo usuario</b></div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">person</i>
                        </span>
                        <div class="form-line">
                            <input type="text" class="form-control" name="name" placeholder="Nombre" required autofocus>
                        </div>
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">email</i>
                        </span>
                        <div class="form-line">
                            <input type="email" class="form-control" name="email" placeholder="Dirección E-Mail" required>
                        </div>
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">lock</i>
                        </span>
                        <div class="form-line">
                            <input type="password" class="form-control" name="password" minlength="6" placeholder="Contraseña" required>
                        </div>
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">lock</i>
                        </span>
                        <div class="form-line">
                            <input type="password" class="form-control" name="confirm" minlength="6" placeholder="Confirmar Contraseña" required>
                        </div>
                    </div>
                    <button id="btnLogin" class="btn btn-block btn-lg bg-orange waves-effect" type="submit">REGISTRARSE</button>

                    <div class="m-t-25 m-b--5 align-center">
                        <a href="<?= base_url() ?>">Ya estas registrado?</a>
                    </div>
                </form>
                <div class="row m-t-5 m-b--10">
                    <div class="col-xs-1"></div>
                    <div id="divError" class="col-xs-10 alert alert-danger" style="display: none;">
                    </div>
                    <div class="col-xs-1"></div>
                </div>


            </div>
        </div>
    </div>


    <!-- Bootstrap core JavaScript-->
    <!-- jQuery -->
    <script src="<?= base_url() ?>plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="<?= base_url() ?>plugins/bootstrap/js/bootstrap.min.js"></script>

    <!-- Core plugin JavaScript-->
    <!-- Bootstrap Core Js -->
    <script src="<?= base_url() ?>plugins/bootstrap/js/bootstrap.js"></script>
    <!-- Waves Effect Plugin Js -->
    <script src="<?= base_url() ?>plugins/node-waves/waves.js"></script>
    <!-- Validation Plugin Js -->
    <script src="<?= base_url() ?>plugins/jquery-validation/jquery.validate.js"></script>
    <!-- Custom Js -->
    <script src="<?= base_url() ?>js/admin.js"></script>

    <script>
        $('document').ready(function() {
            /* validation */
            $("#login-form").validate({
                rules: {
                    name: {
                        required: true,
                    },
                    password: {
                        required: true,
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    confirm: {
                        required: true,
                        equalTo: '[name="password"]'
                    },

                },
                messages: {
                    name: {
                        required: "complete su nombre"
                    },
                    password: {
                        required: "please enter your password"
                    },
                    confirm: {
                        required: "ingrese la confirmacion",
                        equalTo: "las contraseñas no coinciden"
                    },
                    email: "please enter your email address",
                },
                submitHandler: submitForm
            });
        });

        function submitForm() {
            var data = $("#login-form").serialize();
            $.ajax({

                type: 'POST',
                url: "<?= site_url('start/sign_up_process') ?>",
                data: data,
                beforeSend: function() {
                    $("#btn-login").html('REGISTRANDOSE');
                },
                success: function(response) {
                    console.log(response);
                    if (response == "ok") {
                        $("#btn-login").html('REDIRIGIENDO');
                        setTimeout(' window.location.href = "<?= base_url() ?>smiles"; ', 1000);
                    } else {
                        $("#btn-login").html('REGISTRARSE');
                        $("#divError").html(response);
                        $("#divError").show();
                    };
                }
            });

            return false;
        }
    </script>

</body>

</html>