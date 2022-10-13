<?php 

namespace RBFrameworks\Core;

use RBFrameworks\Core\Session;
use RBFrameworks\Core\Plugin;
use RBFrameworks\Core\Config;
use RBFrameworks\Core\Utils\Encoding;
use RBFrameworks\Core\Utils\EncodingJS;
use RBFrameworks\Core\Http as URL;
use RBFrameworks\Core\Types\File;
use RBFrameworks\Core\Legacy\SmartReplace as fn1;

class App92 {

    public $toRedirLogin = false;
    public $toDie = false;
    
    public $session;

    public $dirAdmins = './';
    public $dirAdminsHistory = './';
    
    public function __construct() {

        $this->session = new Session();

        $this->loadHeaders();
        $this->loadCallbacks();
        $this->loadIncudePath();
        $this->loadRBVars();
        $this->loadAuth();        

        Plugin::load('smart_replace');
        Plugin::load('rbincludes');
        Plugin::load('decode_light');
                
    }

    private function loadHeaders(){
        header("Content-Type: text/html; charset=ISO-8859-1",true); 
        header("Cache-Control: no-cache, must-revalidate"); 
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); 
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

        /*
        setlocale(LC_ALL, "pt_BR", "pt_BR.iso-8859-1", "pt_BR.utf-8", "portuguese");
        date_default_timezone_set('America/Sao_Paulo');
        */

        ignore_user_abort(); 
        set_time_limit(500); 

        error_reporting(E_ERROR | E_WARNING | E_PARSE);

        //error_reporting(E_ALL | ^E_NOTICE );

        ini_set('allow_url_fopen', true);
        ini_set('allow_url_include', true);
        //ini_set('display_startup_errors', false);

    }

    private function loadCallbacks(){

    }

    private function loadIncudePath(){
        //Include Paths
        /*
        set_include_path(
            '.' 
            .PATH_SEPARATOR.get_framework_dir().'_app/' 
            .PATH_SEPARATOR.get_framework_dir().'_app/class/' 
            .PATH_SEPARATOR.get_framework_dir().'_app/functions/' 
            .PATH_SEPARATOR.get_framework_dir().'_app/js/'
            .PATH_SEPARATOR.get_framework_dir().'_app/class/class.Ecommerce/'
            .PATH_SEPARATOR.get_framework_dir().'_app/class/composer/firephp/firephp-core/lib/'
        );
        */
    }

    private function loadRBVars(){

        global $RBFolders;
        global $dirRB;

        //RBVars
        if($GLOBALS['RBVarsGenerate']) {
            
            //Definição de Variáveis
            $h = (empty($_SERVER['HTTPS'])) ? 'http://':'https://';
            
            //A URL será limpa (necessitará do .htaccess) ou não?
            if(isset($GLOBALS['url_clean']) and $GLOBALS['url_clean']) $d = ''; else $d = '?pag='; $d = '';

            //Define +httpVars
            $a = explode('/',$_SERVER['SCRIPT_NAME']);
            $b = count($a)-1;
            $c = "";
            for($i=0;$i<$b; $i++) {
                $c.=$a[$i].'/';
            }
            $b--;
            $RBVarsTemp = array(
                'httpHost'	=>	$h.$_SERVER['SERVER_NAME'].'/'
            ,	'httpSite'	=>	$h.$_SERVER['SERVER_NAME'].$c
            ,	'httpLink'	=>	$h.$_SERVER['SERVER_NAME'].$c.$d
            ,	'httpAjax'	=>	$h.$_SERVER['SERVER_NAME'].$c.'index.php?doAjax=init&pag='
            ,	'httpREST'	=>	$h.$_SERVER['SERVER_NAME'].$c.'index.php?doData=init&response=json&pag=doREST&doREST='
            ,	'httpData'	=>	$h.$_SERVER['SERVER_NAME'].$c.'index.php?doData=init&response=json&pag='
            ,	'httpEval'	=>	$h.$_SERVER['SERVER_NAME'].$c.'index.php?doEval=init&pag='
            ,	'httpFile'	=>	''
            );
            unset( $a, $b, $c, $h, $d );
            
            $towrite = "<?php\n";
            
            //Extract
            foreach($RBVarsTemp as $var => $val) {
                global $$var;
                $$var = $val;
                $towrite.= "\$$var = '$val';\n";
            }
            
            file_put_contents( '_app/vars.inc', $towrite);		
            
        } else { 
            include('_app/vars.inc');
        }

        //Definição da GETPAG Inicial
        if(!isset($_GET['pag']) or empty($_GET['pag'])) $_GET['pag'] = $pagDefault;
        if(substr($_GET['pag'], -1) == '/') $_GET['pag'] = $_GET['pag'].'index';		
        if(!isset($dirRB)) $dirRB = '';
        $subDiretorio = explode('/',$_GET['pag'], 3);
        if(count($subDiretorio) > 1) {
            
            $RBFolders = array(
                'dirRaiz' 	=> $subDiretorio[0]
            ,	'pag' 		=> $subDiretorio[0].'/'.$subDiretorio[1]
            ,	'params'	=> (isset($subDiretorio[2])) ? @explode('/', $subDiretorio[2]) : ''
            ,	'dirRB'		=> $dirRB
            );
            
        } else {
            
            $RBFolders = array(
                'dirRaiz' 	=> '0'
            ,	'pag' 		=> $_GET['pag']
            ,	'params'	=> ''
            ,	'dirRB'		=> $dirRB
            );
            
        }
        unset($subDiretorio, $dirRB, $pagDefault);

    }

    private function loadAuth(){

        global $dirRB;
        //if($_GET['pag'] == 'admin') URL::redir(HTTPSITESSL .'login/');

        //Default
        $RBAuthDefaults = array(
            'folder' => 'sys/'
        ,	'pagDefault' => 'admin-home'
        ,	'pagLogin' => 'admin'
        );

        //Tentar Autenticação
        if(isset($_GET['doAuth'])) {
            header("Content-Type: application/json"); 
            $postdata = file_get_contents("php://input");
            $postdata = utf8_encode($postdata);
            $request = json_decode($postdata);
            if( is_object($request) ) {
                //Métrica de Autenticação
                    //include('sys/_class/class.Admins.php');
                    if(!file_exists($this->dirAdmins.'Admins.php')) throw new \Exception("Arquivo ".$this->dirAdmins.'Admins.php'." não encontrado.");
                    if(!file_exists($this->dirAdminsHistory.'AdminsHistory.php')) throw new \Exception("Arquivo ".$this->dirAdminsHistory.'AdminsHistory.php não encontrado');

                    include($this->dirAdmins.'Admins.php');
                    include($this->dirAdminsHistory.'AdminsHistory.php');

                    $r = $Admins
                        ->autenticar(
                            $request->login
                        ,	$request->senha
                        );
                    if(count($r)) {
                        $r[0]['redir'] = ( empty($_SESSION['redir']) ? $RBAuthDefaults['pagDefault'] : $_SESSION['redir'] );
                        $_SESSION['RBAuth'] = array(
                            'isLogged'	=>	true
                        ,	'folder'	=>	'sys/'
                        ,	'data'		=>	$r
                        ,	'aa'		=>	time()
                        );
                        $AdminsHistory->addlog('admin.login.success', $r);
                    } else {
                        $r = array('error' => 101, 'errorv' => utf8_encode('Login ou Senha Inválidos'));
                        $AdminsHistory->addlog('admin.login.error', array('login'=>$request->login, 'senha' => $request->senha));
                    }
                    Encoding::DeepEncode($r);
                echo json_encode($r);
            } else {
                $r = array('error' => 101, 'errorv' => utf8_encode('Erro de Autenticação'));
                echo json_encode($r);
            }
            die();
        }

        if(isset($_GET['site'])) {
            unset($_SESSION['RBAuth']);
            session_destroy();
            URL::redir("home");
            exit();
        }

        //Forçar Redirect
        if(isset($_SESSION['RBAuth']) and !isset($_GET['site']) ) {
            if(!empty($_SESSION['RBAuth']['data'][0]['redir'])) {
                $url2redir = $httpLink.$_SESSION['RBAuth']['data'][0]['redir'];
                unset($_SESSION['RBAuth']['data'][0]['redir']);
                URL::redir($url2redir);
            }
        }
        if(isset($_SESSION['redir']) and !empty($_SESSION['redir'])) {
            $url2redir = $_SESSION['redir'];
            $url2redir = str_replace($httpHost, '', $url2redir);
            $url2redir = str_replace($httpLink, '', $url2redir);
            $url2redir = str_replace($httpSite, '', $url2redir);
            $url2redir = $httpLink.$url2redir;
            unset($_SESSION['redir']);
            URL::redir($url2redir);
        }

        //if( !file_exists )

        /*
        //Forçar Autenticação
        $_SESSION['RBAuth'] = array(
            'isLogged'	=>	true
        ,	'folder'	=>	'sys/'
        ,	'data'		=>	$Database->result
        ,	'aa'		=>	time()
        );
        */

        //Pasta de Navegação
        if(isset($_SESSION['RBAuth'])) {
            $RBFolders['dirRB'] = ($_SESSION['RBAuth']['isLogged']) ? $_SESSION['RBAuth']['folder'] : $RBFolders['dirRB'];
        }

        //Ajuste de 08/03/2017 para Acessar o Site mesmo quando Logado no Sistema
        if(isset($RBFolders) and !file_exists($RBFolders['dirRB'].$_GET['pag'].'.php') ) {
            if(file_exists('htm/'.$_GET['pag'].'.php')) {
                $dirRB = 'htm/';
                $RBFolders['dirRB'] = $dirRB;
            }
        }        

    }

    public function specialEcom() {
        if(isset($_SESSION['isProcutPage'])) unset($_SESSION['isProcutPage']);
    }

    public function run() {

        global $RBFolders;
        global $RBVars;
        global $dirRB;

        //Definição da Requisição
        if(isset($_GET['doAjax']) and $_GET['doAjax'] == 'init') {
            $RBFolders['request'] = 'isAjax';
        } else 
        if(isset($_GET['doEval']) and $_GET['doEval'] == 'init') {
            $RBFolders['request'] = 'isEval';
        } else 
        if(isset($_GET['doData']) and $_GET['doData'] == 'init') {
            $RBFolders['request'] = 'isData';
        } else {
            $RBFolders['request'] = 'isHtml';
        }

        //Definição do Response
        $_GET['response'] = !empty($_GET['response']) ? $_GET['response'] : 'html';
        switch($_GET['response']) {
            case 'plaintext':
                header("Content-Type: text/plain"); 
            break;
            case 'json':
                header("Content-Type: application/json"); 
            break;
            case 'xml':
                header("Content-Type: application/xml"); 
            break;
            case 'html':
                if(isset($_GET['angular'])) echo '<script src="https://code.angularjs.org/1.5.8/angular.min.js"></script>';
                if(isset($_GET['bootstrap'])) echo '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"/>';
                if(isset($_GET['jquery'])) { echo '
                <!--//Jquery//-->
                <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
                <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
                ';  }
            break;	
        }

        //logdebug("v88 :: Request: ".$RBFolders['request'] ." com uma Response esperada de: ".$_GET['response']);

        //Execução da Requisição
        switch($RBFolders['request']) {
            case 'isAjax':
                //logdebug('Ajax Start');
                //logdebug('plugin decode_light loaded');
                PLugin::load('decode_light');
                if(is_array($_POST)) {
                    foreach($_POST as $chave => $valor) {
                        $_AJAX[$chave] = EncodingJS::decode($valor);
                    }
                }
                if( !empty($_GET['doExec']) ) {
                    include('_app/execs/'.$_GET['doExec'].'.php');
                }
                if(empty($pag)) $pag = $_GET['pag'];
                //logdebug($RBFolders['dirRB'].$pag.'.php incluído');
                include($RBFolders['dirRB'].$pag.'.php');
            break;
            case 'isEval':
                if( !empty($_GET['doExec']) ) {
                    include('_app/execs/'.$_GET['doExec'].'.php');
                }
                Plugin::load('encrypt');
                eval( decrypt($_GET['pag']) );
            break;	
            case 'isData':
                $file2include = '_app/execs/'.$_GET['pag'].'.php';
                if(file_exists($file2include)) {
                    include($file2include);
                    exit();
                }
            break;
            case 'isHtml':
            
                /*
                
                Nota importante:
                
                O único arquivo que é carregado no include é o _filter. Pois dentro do _filter é onde acontece todos os includes, incluindo o da Página.
                As verificações abaixo se resumem a descobrir o arquivo filter correto.
                E também, se a página solicitada existe, apenas para evitar do _filter ser carregado em vão.
                
                */
            
                //Processamento de Script adicional ?doExec=arquivo [Deprecate]
                if( !empty($_GET['doExec']) ) include('_app/execs/'.$_GET['doExec'].'.php');
                
                //case Mobiles
                $isMobile = '';
                /*
                $detect = new mobile();
                if($detect->isMobile()) $isMobile = '-mobile';
                if($forceMobile) $isMobile = '-mobile';
                */
                
                //Variáveis de Rastreamento e Log
                $includehash = date('d/m/Y H:i:s').": ";
                $toInclude = array();
                
                //Primeira Tentativa: Diretório Padrão
                ob_start();
                extract($RBFolders);
                //Base dos Includes
                $RBIncludes = array(
                    "{$dirRB}{$pag}.!filter{$isMobile}.php"
                ,	"{$dirRB}{$dirRaiz}/_filter{$isMobile}.php"
                ,	"{$dirRB}_filter{$isMobile}.php"
                ,	"{$dirRB}{$pag}.php"
                );            

                //unset($dirRaiz, $pag, $params, $request);
                $file2include = $RBIncludes[3];
                
                //Includes, em ordem de prioridade
                if( file_exists($RBIncludes[0]) ) {
                    $toInclude[] = $RBIncludes[0];
                    $render_include = true;
                } else 
                if( file_exists($RBIncludes[1]) ) {
                    $toInclude[] = $RBIncludes[1];
                } else 
                if( file_exists($RBIncludes[2]) ) {
                    $toInclude[] = $RBIncludes[2];
                } else
                if( file_exists($RBIncludes[3]) ) {
                    $toInclude[] = $RBIncludes[3];
                    $render_include = true;
                }
                
                $includehash.= "TRY1:$file2include|";
                
                /*
                A Variável $toInclude já está pronta para ser processada. Porém, ela só poderá ser processada se a ?pag existir.
                Caso não exista a ?pag, então verifica se estava procurando na pasta sys, porém, está na htm
                */
                if( !file_exists($file2include) ) {
                    if(!$render_include and $dirRB == 'sys/') {
                        $toInclude = array();
                        $dirRB = 'htm/';
                        $RBFolders['dirRB'] = $dirRB;
                        $RBIncludes = array(
                            "{$dirRB}{$pag}.!filter{$isMobile}.php"
                        ,	"{$dirRB}{$dirRaiz}/_filter{$isMobile}.php"
                        ,	"{$dirRB}_filter{$isMobile}.php"
                        ,	"{$dirRB}{$pag}.php"
                        );
                        unset($dirRaiz, $pag, $params, $request);
                        $file2include = $RBIncludes[3];

                        //Includes, em ordem de prioridade
                        if( file_exists($RBIncludes[0]) ) {
                            $toInclude[] = $RBIncludes[0];
                            $render_include = true;
                        } else 
                        if( file_exists($RBIncludes[1]) ) {
                            $toInclude[] = $RBIncludes[1];
                            //include($RBIncludes[1]);
                            //$render_include = true;
                        } else 
                        if( file_exists($RBIncludes[2]) ) {
                            $toInclude[] = $RBIncludes[2];
                            //include($RBIncludes[2]);
                            //$render_include = true;
                        } else
                        if( file_exists($RBIncludes[3]) ) {
                            //include($RBIncludes[3]);
                            $toInclude[] = $RBIncludes[3];
                            $render_include = true;
                        }
                    }
                    $includehash.= "TRY2:$file2include|";
                }
                
                //Até aqui houve apenas duas tentativas:
                /*
                1) Incluso na pasta padrão (htm se não logado, ou sys se logado)
                2) Se não está na pasta padrão, então caso esteja logado, procura na pasta htm.
                */
                if( file_exists($file2include) ) {
                    $includehash.= "OK!";
                
                    foreach($toInclude as $i => $r) {
                        include($r);
                    }
                
                //Nada foi incluído até aqui. Porque?
                } else {
                    
                    //Permissão do Arquivo?
                    $file2include = str_replace($dirRB, $RBAuthDefaults['folder'], $file2include);
                    if( file_exists($file2include) ) {
                        $_SESSION['redir'] = $_GET['pag'];
                        $includehash.= "ACCESSO NEGADO!";
                        $toRedirLogin = true;
                    
                    } else {
                    //Arquivo Inexistente?
                        $includehash.= "NOT FOUND!";
                        $toDie = true;		
                    }
                }
                
                ob_start();
                print_r($RBFolders);
                print_r($toInclude);
                //logdebug("Processamento da v88 index \r\n".ob_get_clean()."\r\n$includehash");
                
                        if($toRedirLogin) {
                    URL::redir($RBAuthDefaults['pagLogin']);
                }
                if($toDie) {
                    die('A página solicitada não foi encontrada.');	
                }

                echo fn1::smart_replace(ob_get_clean());		
            break;
        }
    }


}