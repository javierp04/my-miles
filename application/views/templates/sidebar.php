<? $ctler = $this->router->fetch_class(); ?>
<section>
    <!-- Left Sidebar -->
    <aside id="leftsidebar" class="sidebar">
        <!-- User Info -->
        <!--
        <div style="text-align:center; padding-top:5px;" class="user-info bg-blue">
            <div class="user-data">
                <div class="info-container">
                    <div class="m-l-10 name" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Nombre Usuario</div>                   
                </div>
            </div>          
        </div>
        -->
        <!-- #User Info -->
        <!-- Menu -->
        <?
            $segments = explode('/', current_url());
            $last = end($segments);
            $op = $this->session->get_userdata();
            if ($op != null) {
                $rol_id = $op["Rol_Id"];
            } else {
                echo "NO VALIDADO";
                exit;
            }
        ?>
        <div class="menu">            
            <ul class="list">
                <li class="header">PANEL DE GESTION</li>
                <li class="<?= strpos(current_url(), "my_search") > 0 ? "active" : "" ?>">
                    <a href="<?=base_url() . "smiles/my_search"?>">
                        <i class="material-icons">history</i>
                        <span>Mis Búsquedas</span>
                    </a>
                </li>               
                <li class="<?= strpos(current_url(), "smiles") > 0  && $last == "smiles" ? "active" : "" ?>">
                    <a href="<?=base_url() . "smiles" ?>">
                        <i class="material-icons">flight</i>
                        <span>Buscar en Smiles</span>
                    </a>                    
                </li>               
                <li class="<?= strpos(current_url(), "favorites") > 0  && $last == "favorites" ? "active" : "" ?>">
                    <a href="<?=base_url() . "smiles/favorites" ?>">
                        <i class="material-icons">star</i>
                        <span>Favoritos</span>
                    </a>                    
                </li>               
                <? if ($rol_id == 1) : ?>
                    <li class="<?= strpos(current_url(), "config") > 0 ? "active" : "" ?>">
                    <a href="<?=base_url() . "smiles/config"?>">
                        <i class="material-icons">settings</i>
                        <span>Configuración</span>
                    </a>
                </li>

                <? endif; ?>
            </ul>                   
        </div>
        <!-- #Menu -->    
        <!-- Footer -->
        <div class="legal">
            <div class="copyright">
               <b><?= $op["Op_Name"]?></b>
            </div>
            <div class="version">                   
				<a href="<?=base_url() . "start/logout"?>">Cerrar Sesión</a>
            </div>
        </div>
        <!-- #Footer -->
    </aside>
    <!-- #END# Left Sidebar -->
</section>
