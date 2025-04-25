<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>MY MILES - Buscador Smiles</title>
    <!-- Favicon-->
    <link rel="icon" href="<?=base_url()?>/favicon.ico" type="image/x-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">

    <!-- Bootstrap Core Css -->
    <link href="<?=base_url()?>plugins/bootstrap/css/bootstrap.css" rel="stylesheet">

    <!-- Waves Effect Css -->
    <link href="<?=base_url()?>plugins/node-waves/waves.css" rel="stylesheet" />

    <!-- Animation Css -->
    <link href="<?=base_url()?>plugins/animate-css/animate.css" rel="stylesheet" />

    <!-- Custom Css -->
    <link href="<?=base_url()?>css/style.css" rel="stylesheet">
</head>

<body class="login-page">
    <div class="login-box">
        <div class="logo">
            <a href="javascript:void(0);"><b>MY MILES</b></a>
        </div>
        <div class="card">
            <div class="body">
                <form  class="form-signin" method="post" id="login-form">
                    <div class="msg"><h4>Inicio de sesión</h4></div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">email</i>
                        </span>
                        <div class="form-line">
                            <input type="email" class="form-control" name ="inputEmail" id="inputEmail" placeholder="E-Mail" required autofocus>
                        </div>
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">lock</i>
                        </span>
                        <div class="form-line">
                            <input type="password" class="form-control" name="inputPassword" id="inputPassword"  placeholder="Contraseña" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-2 p-t-5">
                            
                        </div>
                        <div class="col-xs-8">
                            <button class="btn btn-block bg-orange waves-effect" name="btn-login" id="btn-login" type="submit">INGRESAR</button>
                        </div>
                        <div class="col-xs-2">
                            
                        </div>
                    </div>      
                    <div class="align-center">
                        <a href="<?= base_url() . "start/signup"?>">Registrate aquí!?</a>
                    </div>             
                    <div class="row m-t-5 m-b--10">
                        <div class="col-xs-1"></div>
                        <div id="divError" class="col-xs-10 alert alert-danger hidden"> 
                        </div>
                        <div class="col-xs-1"></div>
                    </div>
                </form>
                

            </div>
        </div>
    </div>

	
  <!-- Bootstrap core JavaScript-->
  <!-- jQuery -->
 <script src="<?=base_url()?>plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap Core JavaScript -->
  <script src="<?=base_url()?>plugins/bootstrap/js/bootstrap.min.js"></script>
  
  <!-- Core plugin JavaScript-->
  <script type="text/javascript" src="<?=base_url()?>js/login.js"></script>

    <!-- Bootstrap Core Js -->
    <script src="<?=base_url()?>plugins/bootstrap/js/bootstrap.js"></script>
    <!-- Waves Effect Plugin Js -->
    <script src="<?=base_url()?>plugins/node-waves/waves.js"></script>
    <!-- Validation Plugin Js -->
    <script src="<?=base_url()?>plugins/jquery-validation/jquery.validate.js"></script>
    <!-- Custom Js -->
    <script src="<?=base_url()?>js/admin.js"></script>	
	
    <script>
        var msg = '<?= $msg ?>';
        if (msg) {
            $("#divError").removeClass("hidden");            
            $("#divError").html(msg);            
        }
        function submitForm() {  
            var data = $("#login-form").serialize();
            $.ajax({

                type : 'POST',
                url  : "<?=site_url('start/login_process')?>",
                data : data,
                beforeSend: function() { 
                  $("#divError").fadeOut();
                  $("#btn-login").html('INGRESANDO');
                },
                success :  function(response) {   
                    if(response=="ok") {
                        $("#btn-login").html('INICIANDO SESION');
                        setTimeout(' window.location.href = "<?=base_url()?>smiles"; ', 1000);
                    } else {
                        $("#divError").removeClass("hidden");
                        $("#divError").fadeIn(1000, function(){      
                            $("#divError").html(response);
                            $("#btn-login").html('INGRESAR');
                        });
                    }
                }
            });
            return false;
        }

    </script>

</body>

</html>