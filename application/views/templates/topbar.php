<!-- Top Bar -->
<nav class="navbar">
    <div class="container-fluid">
        <div class="navbar-header">
            <a href="javascript:void(0);" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false"></a>
            <a href="javascript:void(0);" class="bars"></a>
            <a class="navbar-brand" href="#">MY MILES - ADMINISTRA TUS BÚSQUEDAS SMILES</a>
        </div>
        <? if (!$is_free_view) : ?>
            <div class="collapse navbar-collapse" id="navbar-collapse">
                <ul class="nav navbar-nav navbar-right">
                    <!-- Notifications -->
                    <li class="dropdown">
                        <a href="javascript:void(0);" class="text-center">
                            <i class="material-icons" style="vertical-align: middle; display: inline-block;">flight</i><span style="font-size: 16px;"><b>Créditos</b></span>
                            <div style="font-size: 16px;">
                                <b>
                                    <span id="spReqUsed"><?= $user_request_count ?></span> / <span><?= $total_requests ?></span>
                                </b>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        <? endif; ?>
    </div>
</nav>
<!-- #Top Bar -->