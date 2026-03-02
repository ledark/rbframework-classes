<!DOCTYPE html>
<html lang="pt">
    <head>
        <base href="{httpSite}/admin/">
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>{nomeFantasia}</title>
        <link href="css/styles.css" rel="stylesheet" />
        <link href="css/print.css" rel="stylesheet" media="print" />
        <link href="../assets/sweetalert2/minimal.css" rel="stylesheet" />
        <script src="../assets/fontawsome/v6.3.0/js/all.js"></script>
    </head>
    <body class="sb-nav-fixed">
        <div id="app">
            @include('admin.layout.topnav')

        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        @include('admin.layout.menu')
                    </div>
                    <div class="sb-sidenav-footer">
                        <div class="small">Logado como:</div>
                        <?php /* echo auth('cargo_apelido') */ ?>
                        <span class="badge bg-dark-blue text-dark-blue"><?php /* echo auth('cod') */ ?></span>
                    </div>
                </nav>
            </div>
            <div id="layoutSidenav_content">
                
                <main class="mainPage">@yield('content')</main>

                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; Desenvolvido por AD369 Software House</div>
                            <div id="ping"></div>
                            
                            <div>
                                <a href="#">RBFrameworks</a>
                                &middot;
                                <a href="#">2026</a>
                            </div>
                            
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        </div>
        <script src="{httpSite}/assets/js/htmx.min.js"></script>
        <script src='{httpSite}/assets/js/jquery-4.0.0.min.js'></script>
        <script src="{httpSite}/assets/js/axios.min.js"></script>
        <script src="{httpSite}/assets/bootstrap5/bootstrap.bundle.min.js"></script>
        <script src="{httpSite}/assets/sweetalert2/sweetalert2.min.js"></script>
        <script src="{httpSite}/assets/sweetalert2/swal.config.js"></script>


        
                
    </body>
</html>            