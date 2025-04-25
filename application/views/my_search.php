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
    <link href="<?= base_url() ?>plugins/animate-css/animate.css" rel="stylesheet" />
    <link href="<?= base_url() ?>css/themes/all-themes.css" rel="stylesheet" />

    <!-- Jquery Core Js -->
    <script src="<?= base_url() ?>plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap Core Js -->
    <script src="<?= base_url() ?>plugins/bootstrap/js/bootstrap.js"></script>


    <script src="<?= base_url() ?>plugins/node-waves/waves.js"></script>
    <script src="<?= base_url() ?>plugins/sweetalert/sweetalert.min.js"></script>

    <!-- Slimscroll Plugin Js -->
    <script src="<?= base_url() ?>plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
    <style>
        .old-search {
            background-color: #EEEEEE;
        }
        .has-view {
            background-color: #FFEFD5;
        }
    </style>

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
                                    <h2><i class="material-icons">history</i>&nbsp;<span>MIS BÚSQUEDAS</span></h2>
                                </div>
                            </div>
                        </div>
                        <div class="body">
                            <div class="row">
                                <div class="col-xs-12">
                                    <a href="<?= base_url() . "smiles" ?>" class="btn bg-green">NUEVA BUSQUEDA</a>
                                </div>
                            </div>
                            <div class="preloader-generic text-center" id="loadingRes" style="display: none;">
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
                                <p>procesando...</p>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    <div id="tabSearch" class="table-responsive">
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="<?= base_url() ?>js/admin.js"></script>
    <script>
        var ways;

        $(function() {

            listSearches();
        });

        function listSearches() {
            $("#tabResults").html("");
            show_wait_page(true);
            $.ajax({
                type: 'POST',
                data: {
                    is_ajax: 1
                },
                url: '<?= base_url() . "Smiles/ax_list_my_searches" ?>',
                success: function(response) {
                    if (response.nosession) {
                        showNoSessionMsg();
                        return;
                    }
                    show_wait_page(false);
                    $("#tabSearch").html(response.html);
                }
            });
        }

        function show_wait_page(value) {
            if (value) {
                $("#loadingRes").show();
            } else {
                $("#loadingRes").hide();
            }
        }

        $(document).on('click', '.btn-delete', function() {
            var id = $(this).data("value");
            swal({
                title: "Eliminar historial de búsqueda",
                text: "¿Esta seguro que desdea eliminar el historial de búsqueda?",
                icon: "info",
                buttons: true
            }).then((willEnable) => {
                if (willEnable) {
                    confirmDelete(id);
                }
            });

        });
        <? if ($rol_id == 1) : ?>
            $(document).on('click', '.btn-invalidate', function() {
                var id = $(this).data("value");
                swal({
                    title: "Invalidar Resultados",
                    content: {
                        element: 'div',
                        attributes: {
                            innerHTML: '¿Esta seguro que desdea invalidar los resultados de la búsqueda seleccionada?<br><br>' +
                                'Desde <input id="swDesde" placeholder="Desde"><br>' +
                                'Hasta <input id="swHasta" placeholder="Hasta"><br>'
                        }
                    },
                    icon: "info",
                    buttons: true,                    
                }).then((result) => {
                    if (result) {                        
                        confirmInvalidate(id, $("#swDesde").val(), $("#swHasta").val());
                    }
                });

            });

            $(document).on('click', '.btn-addview', function() {
                var id = $(this).data("value");
                var view = $(this).data("view");
                if (!view) {
                    swal({
                        title: "Agregar a Vista Pública",
                        text: "¿Esta seguro que desdea agrgegar a vista pública los resultados de la búsqueda seleccionada?",
                        content: {
                            element: "input",
                            attributes: {
                                placeholder: "Escribe aquí..."
                            }
                        },
                        icon: "info",
                        buttons: true
                    }).then((value) => {
                        if (value) {
                            confirmAddView(id, value);
                        }
                    });
                } else {
                    swal({
                        title: "Remover de Vista Pública",
                        text: "¿Esta seguro que desdea REMOVER a vista pública los resultados de la búsqueda seleccionada?",                        
                        icon: "info",
                        buttons: true
                    }).then((value) => {
                        if (value) {
                            confirmAddView(id, '');
                        }
                    });

                }

            })

            $(document).on('click', '.btn-extend', function() {
                var id = $(this).data("value");
                var view = $(this).data("view");
                if (!view) {
                    swal({
                        title: "Extender validez de búsqueda",
                        text: "¿Esta seguro que desdea extender la validez de la búsqueda?",
                        content: {
                            element: "input",
                            attributes: {
                                type : "number",
                                placeholder: "Cantidad de horas"
                            }
                        },
                        icon: "info",
                        buttons: true
                    }).then((value) => {
                        if (value) {
                            confirmExtend(id, value);
                        }
                    });
                } 
            })

            function confirmExtend(id, hours) {
                show_wait_page(true);
                $.ajax({
                    type: 'POST',
                    data: {
                        is_ajax: 1,
                        hours : hours
                    },
                    url: '<?= base_url() . "Smiles/ax_extend_search" ?>/' + id,
                    success: function(response) {
                        if (response.nosession) {
                            showNoSessionMsg();
                            return;
                        }
                        if (response == "1") {
                            show_wait_page(false);
                            listSearches();
                        }
                    }
                });
            }

            function confirmAddView(id, info) {
                show_wait_page(true);
                $.ajax({
                    type: 'POST',
                    data: {
                        is_ajax: 1,
                        info : info
                    },
                    url: '<?= base_url() . "Smiles/ax_toogle_public_view" ?>/' + id,
                    success: function(response) {
                        if (response.nosession) {
                            showNoSessionMsg();
                            return;
                        }
                        if (response == "1") {
                            show_wait_page(false);
                            listSearches();
                        }
                    }
                });
            }

            function confirmInvalidate(id, desde, hasta) {
                show_wait_page(true);
                $.ajax({
                    type: 'POST',
                    data: {
                        is_ajax: 1,
                        desde : desde,
                        hasta : hasta
                    },
                    url: '<?= base_url() . "Smiles/ax_invalidate_search" ?>/' + id,
                    success: function(response) {
                        if (response.nosession) {
                            showNoSessionMsg();
                            return;
                        }
                        if (response == "1") {
                            show_wait_page(false);
                            listSearches();
                        }
                    }
                });

            }
        <? endif; ?>

        function confirmDelete(id) {
            show_wait_page(true);
            $.ajax({
                type: 'POST',
                data: {
                    is_ajax: 1
                },
                url: '<?= base_url() . "Smiles/ax_delete_my_search" ?>/' + id,
                success: function(response) {
                    if (response.nosession) {
                        showNoSessionMsg();
                        return;
                    }
                    if (response == "1") {
                        show_wait_page(false);
                        listSearches();
                    }
                }
            });
        }

        function showNoSessionMsg() {
            swal({
                title: "Se ha perdido la sesión",
                text: "Por favor vuelva a iniciar sesión. Ha sido deslogueado por inactividad",
                icon: "error",
                button: "OK"
            }).then((ok) => {
                if (ok) {
                    window.location.href = '<?= base_url() ?>start/not_validated';
                }
            });
        }
    </script>
</body>

</html>