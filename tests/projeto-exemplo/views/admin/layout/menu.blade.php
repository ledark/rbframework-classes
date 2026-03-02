<div class="nav">
    <div class="sb-sidenav-menu-heading">Gestão do Sistema</div>
    @include('admin.layout.menu-item', [        'title' => 'Dashboard',        'url' => 'admin',    ])
    @include('admin.layout.menu-item', [        'title' => 'Usuários',        'url' => 'admin/users',        'icon' => 'fas fa-users'    ])
</div>