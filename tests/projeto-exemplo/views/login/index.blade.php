<!DOCTYPE html>
<html lang="pt">
<head>
    <base href="{httpSite}/login/" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo</title>
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">    
    <link rel="stylesheet" href="css/normalize.css">
    <style>@php include(get_root_path("views/login/css/style.css")) @endphp</style>
    <script src="js/prefixfree.min.js"></script>
    <script src="js/localStorage.js"></script>
</head>
<body>

    <div v-cloak id="app" class="wrapper">
        <form class="login" method="post" v-bind:class="{ready: isPageReady}">
            <input type="hidden" name="silent" value=""/>
            <p class="title">Usu&aacute;rio</p>
            <div v-if="alertMessage.length" class="alert-message alert alert-danger">{[alertMessage]}</div>
            <input type="text" id="username" name="username" placeholder="login" v-model="login" required><i class="fa fa-user"></i>
            <input type="password" id="password" name="password" placeholder="senha" v-model="senha" required><i class="fa fa-key"></i>
            <div class="checkbox">
                <label>
                    <input id="relembrar" type="checkbox" v-on:change="toogleRelembrar()">
                    relembrar
                </label>
            </div>
            <button type="submit" v-on:click.prevent.stop="doLogin()">
                <i class="spinner"></i>
                <span class="state">Entrar</span>
            </button>
        </form>
        <!--<footer><a target="blank" href="https://ad369.com.br">httpstudio.com.br</a></footer>-->
    </div>

    <script src="{httpSite}/assets/js/axios.min.js"></script>
    <script src='{httpSite}/assets/js/jquery-4.0.0.min.js'></script>
    <script type="module"><?php include(get_root_path("views/login/js/script.js")) ?></script>
</body>
</html>