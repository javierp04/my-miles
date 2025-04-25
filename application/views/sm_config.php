<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>MY MILES - Administra tus búsquedas Smiles</title>
    <!-- Favicon-->
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">

    <!-- Bootstrap Core Css -->
    <link href="<?= base_url() ?>plugins/bootstrap/css/bootstrap.css" rel="stylesheet">

    <!-- Bootstrap Select Css -->
    <link href="<?= base_url() ?>css/style.css?v=<?= rand(1, 10000) ?>" rel="stylesheet">
    <link href="<?= base_url() ?>plugins/node-waves/waves.css" rel="stylesheet" />
    <link href="<?= base_url() ?>plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css" rel="stylesheet" />
    <link href="<?= base_url() ?>plugins/animate-css/animate.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css">
    <link href="<?= base_url() ?>css/themes/all-themes.css" rel="stylesheet" />

    <!-- Jquery Core Js -->
    <script src="<?= base_url() ?>plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap Core Js -->
    <script src="<?= base_url() ?>plugins/bootstrap/js/bootstrap.js"></script>


    <script src="<?= base_url() ?>plugins/node-waves/waves.js"></script>
    <script src="<?= base_url() ?>plugins/sweetalert/sweetalert.min.js"></script>

    <!-- Slimscroll Plugin Js -->
    <script src="<?= base_url() ?>plugins/jquery-slimscroll/jquery.slimscroll.js"></script>

    <!-- Bootstrap Material Datetime Picker Plugin Js -->
    <script src="<?= base_url() ?>plugins/autosize/autosize.js"></script>
    <script src="<?= base_url() ?>plugins/momentjs/moment.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>    
    <script src="<?= base_url() ?>plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js"></script>    
</head>

<body class="theme-orange">
    <!-- Page Loader -->
    <div class="page-loader-wrapper">
        <div class="loader">
            <div class="preloader">
                <div class="spinner-layer pl-red">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>
            </div>
            <p>Please wait...</p>
        </div>
    </div>
    <!-- #END# Page Loader -->
    <!-- Overlay For Sidebars -->
    <div class="overlay"></div>
    <!-- #END# Overlay For Sidebars -->
    <?
    $this->view('templates/topbar');
    $this->view('templates/sidebar');
    ?>

    <section class="content">
        <div class="container-fluid">
            <div class="row clearfix">
                <div class="col-xs-12">
                    <div class="card">
                        <div class="header bg-black" style="padding: 10px;">
                            <div class="row">
                                <div class="col-xs-12">
                                    <h2><i class="material-icons">settings</i>&nbsp;<span>CONFIGURACIÓN</span></h2>
                                </div>
                            </div>                            
                        </div>
                        <div class="body">
                            <div class="preloader-generic text-center" id="loadingPage" style="display: none;">
                                <div class="preloader pl-size-xs">
                                    <div class="spinner-layer pl-indigo">
                                        <div class="circle-clipper left">
                                            <div class="circle"></div>
                                        </div>
                                        <div class="circle-clipper right">
                                            <div class="circle"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    <h4>Contenido</h4>
                                </div>                                
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    <div id="tabConfig" class="table-responsive">
                                    </div>
                                </div>
                            </div>
                            <div class="preloader-generic text-center" id="loadingRes" style="display: none;">                            
                                <div class="preloader">
                                    <div class="spinner-layer pl-indigo">
                                        <div class="circle-clipper left">
                                            <div class="circle"></div>
                                        </div>
                                        <div class="circle-clipper right">
                                            <div class="circle"></div>
                                        </div>
                                    </div>
                                </div>
                                <p>procesando...</p>
                            </div>                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="<?= base_url() ?>js/admin.js"></script>
    <script>

        $(function() {
           
        });


        function show_wait_page(value) {
            if (value) {
                $("#loadingPage").show();
            } else {
                $("#loadingPage").hide();
            }
        }

    

    </script>
</body>
</html>