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
        /* Estilo para dejar espacio para la barra superior */
        .content {
            margin-top: 80px;
            /* Altura de la barra superior */
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
    <? $this->view('templates/topbar'); ?>
    <div class="container-fluid content">
        <div class="row clearfix">
            <div class="col-xs-12">
                <? if ($is_mobile) : ?>                    
                    <h5 class="alert alert-danger"><i class="material-icons" style="vertical-align: middle; display: inline-block;">warning</i>&nbsp;<span>La página esta diseñada para ver en computadora o como mínimo en horizontal</span></h5>
                <? endif; ?>
                <div class="card">
                    <div class="header bg-black" style="padding: 10px;">
                        <div class="row">
                            <div class="col-xs-12">
                                <h2><i class="material-icons">history</i>&nbsp;<span>VER BÚSQUEDAS</span></h2>
                            </div>
                        </div>
                    </div>
                    <div class="body">
                        <? if ($this->session->get_userdata()["alert1"] != 1 || $this->session->get_userdata()["alert2"] != 1) : ?>
                            <div class="row">

                                <div class="col-lg-6 col-md-12">
                                    <? if ($this->session->get_userdata()["alert1"] != 1) : ?>
                                        <div class="alert bg-teal alert-dismissible" role="alert">
                                            <button type="button" class="close" data-value="1" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            Por el momento esto NO ES UN BUSCADOR. Es un explorador de resultados de búsquedas pre-cargadas. Se puede acceder a cada una de las búsquedas, ver los LISTADOS
                                            de los vuelos más baratos, ver el CALENDARIO y además ARMAR TU VIAJE.<br>
                                            Podés seleccionar las fechas entre las cuales tenés pensado realizar el viaje y la estadía mínima y máxima y usar los filtros de arriba de duración, tipo de cabina y cantidad de asientos
                                            para acotar los resultados.
                                        </div>
                                    <? endif; ?>
                                    <? if ($this->session->get_userdata()["alert2"] != 1) : ?>
                                        <!--
                                        <div class="alert bg-green alert-dismissible" role="alert">
                                    <button type="button" class="close" data-value="2" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    Por el momento es GRATUITO, pero más adelante habrá una suscripción SEMANAL de $300. Por ejemplo, si una determinada semana hay promoción a un destino
                                    que te interesa, te podés suscribir y accedés a esa búsqueda y a todas las demás búsquedas que se vayan realizando por 7 días, navegarlas y encontrar los tramos que
                                    más te convengan. Es la única forma de mantener activo el servicio, dado que requiere servidores dedicados y mucho trabajo de desarrollo.
                                </div> -->
                                    <? endif; ?>
                                </div>                            
                            </div>
                        <? endif; ?>
                        <div class="row">
                            <div class="col-xs-12">
                                <? if (isset($searches) && count($searches) > 0) : ?>
                                    <table class="table table-striped table-hover table-condensed table-bordered table-responsive">
                                        <thead>
                                            <tr class="info">
                                                <th class="text-center">&nbsp;</th>
                                                <th class="text-center">INFO</th>
                                                <th class="text-center">Desde ($<?= number_format(round($this->session->userdata('mile_price'), 2), 2, ',', '.') ?> / Milla)</th>
                                                <th class="text-center">Tramos Ida</th>
                                                <th class="text-center">Tramos Regreso</th>
                                                <th class="text-center">Rango Disponible</th>
                                                <th class="text-center">Actualizado al</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <? foreach ($searches as $s) :
                                                if ($s->Valid) :
                                            ?>
                                                    <tr>
                                                        <td class="align-middle text-center">
                                                            <a class="btn bg-orange waves-effect btn-xs " href="<?= base_url() . "view/results/{$s->Search_Id}" ?>">
                                                                <i class="material-icons">visibility</i>
                                                            </a>
                                                        </td>
                                                        <td class="align-middle">
                                                            <?= $s->Des ?>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <b><?= "$" . number_format($s->MinIda + $s->MinVta, 0, ',', '.') ?></b>
                                                        </td>
                                                        <td>
                                                            <table style="width: 100%;" border="0" cellpadding="3">
                                                                <? foreach ($s->Idas as $ida) : ?>
                                                                    <tr>
                                                                        <td style="text-align: right; width: 45%;"><?= "{$ida->OrigCity} ({$ida->Orig})" ?></td>
                                                                        <td style="text-align: center; width: 10%;"><i class="material-icons" style="vertical-align: middle; display: inline-block;">arrow_forward</i>
                                                                        <td style="width: 45%;"><?= "{$ida->DestCity} ({$ida->Dest})" ?></td>
                                                                    </tr>
                                                                <? endforeach; ?>
                                                            </table>
                                                        </td>

                                                        <td>
                                                            <table style="width: 100%;" border="0" cellpadding="3">
                                                                <? foreach ($s->Vueltas as $vta) : ?>
                                                                    <tr>
                                                                        <td style="text-align: right; width: 45%;"><?= "{$vta->OrigCity} ({$vta->Orig})" ?></td>
                                                                        <td style="text-align: center; width: 10%;"><i class="material-icons" style="vertical-align: middle; display: inline-block;">arrow_forward</i>
                                                                        <td style="width: 45%;"><?= "{$vta->DestCity} ({$vta->Dest})" ?></td>
                                                                    </tr>
                                                                <? endforeach; ?>
                                                            </table>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <?= date("d/m/Y", strtotime($s->FDesde)) . " - " . date("d/m/Y", strtotime($s->FHasta)) ?>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <?= date("d/m/Y H:i", strtotime($s->LastUpdate)) ?>
                                                        </td>
                                                    </tr>
                                            <? endif;
                                            endforeach; ?>
                                        </tbody>
                                    </table>
                                <? else : ?>
                                    <h5 class="alert alert-info m-t--10">No hay búsquedas disponibles por el momento.</h5>
                                <? endif; ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <a class="btn bg-blue waves-effect" href="https://www.youtube.com/watch?v=xE5NI_299m0" target="_blank">
                                    <i class="material-icons">help</i><span><b>VER VIDEO DE INTRODUCCIÓN Y AYUDA<b></span>
                                </a>
                            </div>
                        </div>                       
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= base_url() ?>js/admin.js"></script>
    <script>
        $(function() {
            $('.close').click(function() {
                v = $(this).data('value');
                dm(v);
            });
        });

        function dm(v) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '<?= base_url() . "View/ax_dismiss/" ?>' + v,
                success: function(response) {}

            });
        }
    </script>


</body>

</html>