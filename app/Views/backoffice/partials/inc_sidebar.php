<div id="sidebar" class="active">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header">
            <div class="d-flex justify-content-between">
                <div class="logo">
                    <a href=""></a>
                    <!-- <a href=""><img src="<?php base_url('assets/images/logo/logo.png') ?>" alt="Logo" srcset=""></a> -->
                </div>
                <div class="toggler">
                    <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                </div>
            </div>
        </div>
        <div class="sidebar-menu">
            <ul class="menu">
                <li class="sidebar-title">Menu</li>

                <li class="sidebar-item <?= is_active_route_prefix(['backoffice.index']) ?>">
                    <a href="<?= route_to('backoffice.index') ?>" class='sidebar-link'>
                        <i class="bi bi-grid-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-item <?= is_active_route_prefix(['backofficeproducts.']) ?> has-sub">
                    <a href="#" class='sidebar-link'>
                        <i class="bi bi-bag-fill"></i>
                        <span>Products</span>
                    </a>
                    <ul class="submenu <?= is_active_route_prefix(['backofficeproducts.']) ?>">
                        <li class="submenu-item <?= is_active_route_prefix(['backofficeproducts.products', 'backofficeproducts.add_product']) ?>">
                            <a href="<?= route_to('backofficeproducts.products') ?>">Products</a>
                        </li>
                        <li class="submenu-item <?= is_active_route_prefix(['backofficeproducts.category']) ?>">
                            <a href="<?= route_to('backofficeproducts.category') ?>">Category</a>
                        </li>
                    </ul>
                </li>
                <li class="sidebar-item <?= is_active_route_prefix(['backoffice.campaign']) ?>">
                    <a href="<?= route_to('backoffice.campaign') ?>" class='sidebar-link'>
                        <i class="bi bi-tag-fill"></i>
                        <span>Campaign</span>
                    </a>
                </li>
                

            </ul>
        </div>
        <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
    </div>
</div>