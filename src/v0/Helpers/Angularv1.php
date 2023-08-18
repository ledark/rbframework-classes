<?php

namespace RBFrameworks\Helpers;

class Angularv1 {

    

    var $includePath = "_app/class/user/class.Angular/";
    var $ver = "1.7.0";   //Controle de Versão
    var $scripts;
	var $toLoad = array();
	var $app = "mainApp";
	var $controller = "Conteudo";
	var $controllerRequires = array();
	var $replaces = array();
	var $vars;    
	var $modules = array();
    var $interpolate = '{{[ANGULAR]}}'; //Mantenha sempre [ANGULAR] para definir onde irá o código angular
    var $compile = '';
    var $filters = [];
    
    public function addFilter($name, $value):object {
        if(file_exists($value)) $value = file_get_contents($value);
        //if(strpos($value, 'function(') === false) $value = 'function() { return function(input){ console.log("O arquivo de filtro "'.$name.'" nao esta no formato valido"); return input; }}';
        if(strpos($value, 'function(') === false) $value = 'function(input) { console.log(\'filter '.$name.' invalid\'); return input; }';
        $this->filters[$name] = $value;
        return $this;
    }
    
    public function __construct($controller = "meuController", $app = "mainApp") {
        $this->app = $app;
        $this->controller = $controller;
        return $this;
    }

    public function interpolate($startSymbol = '{[', $endSymbol = ']}') {
        $this->interpolate = $startSymbol.'[ANGULAR]'.$endSymbol;
        return $this;
    }
    
    public function compileProvider(array $compileConfigs): object {
        /*
                  
            .config(['\$compileProvider', '\$interpolateProvider', function(\$compileProvider, \$interpolateProvider) {
                \$interpolateProvider.startSymbol('{$interpolate_symbols[0]}').endSymbol('$interpolate_symbols[1]');
                \$compileProvider.aHrefSanitizationWhitelist(/^\s*(https?|ftp|mailto|javascript):/);
            }])
            
         */
        $this->compile = implode("\r\n", $compileConfigs);
        return $this;
    }
    private function getCompile(string $piece):string {
        return $this->compile;
    }

    public function module($string) {
        $this->modules[] = $string;
        return $this;
    }

    /*
     * Informe o nome do arquivo para include através de um simples Required...
     * <script src="https://code.angularjs.org/1.5.8/angular.min.js"></script>
     */
    public function script($file) {
        $this->scripts[] = $file;
        return $this;
    }
    
    

    /*
     * Arquivos para serem carregados.
     * Por padrão, serão inclusos dentro de um controller, mas outras sections poderão ser definidas, que sao:
     *      controller
     */
    public function load($file, $section = 'controller', $encoding = 'ISO-8859-1') {
        $this->fileEncoding[$file] = $encoding;
		$this->toLoad[$section][] = $file;
		return $this;
	}    
    
    public function loadControllers(array $files) {
        foreach($files as $file) {
            $this->load($file);
        }
        return $this;
    }
    

    /*
     * Utilizado pela função smart_replaces para substituir {variáveis}
     */
	public function replace($var, $value) {
		$this->replaces[$var] = $value;
		return $this;
	}
	
	

	public function setScopeVar($var, $value) {
		$this->vars[$var] = $value;
		return $this;
	}
	
	

	//Define o que o controller utilirá em suas chamadas
	public function required($name) {
		$this->controllerRequires[] = $name;
		return $this;
	}
    
    //Permite passar múltiplos requireds como argumentos da mesma função
    //Caso algum dos argumentos não inicie com $, então assume que é um $this->load($file, controller);
    public function requires() {
        $args = func_get_args();
        foreach($args as $arg) {
            $arg = trim($arg);
            if(substr($arg, 0, 1) == '$') {
                $this->controllerRequires[] = $arg;
            } else {
                $this->load($arg);
            }
        }
        return $this;
    }
    
    use Angularv3;

















    /*
     * Processar o Angular e todo o código HTML válido
     */
    
        
    public function render($capture = true) {
        

        
        if($capture) ob_start();
        
        $includePath = $this->includePath;
        $requires = implode(",", $this->controllerRequires);
        $this->modules = implode(",", $this->modules);
        
        
        
        echo "<!--//AngularJS::ini//-->\r\n";
        
        

        //LoadScripts
        foreach($this->scripts as $script) {
            echo '<script type="module" src="'.$script.'"></script>';
        }
        
        /*
        if(is_array($this->scripts))
            plugin('rbincludes');
        foreach($this->scripts as $script) {
            
            include_conditional($script, false);
        }
        */
        
        $interpolate_symbols = explode('[ANGULAR]', $this->interpolate);

            
            

        //Abertura do mainScript
        ob_start();
		echo "<script>";
        $modules = !empty($this->modules) ? ", [".$this->modules."]" : "";
        $modules = ($modules == ", ['']") ? ', []' : $modules;
		echo "\r\n\twindow.angular.module('$this->app'{$modules})
            .config(function(\$interpolateProvider){
            \$interpolateProvider.startSymbol('{$interpolate_symbols[0]}').endSymbol('$interpolate_symbols[1]');
        })\r\n\t\t";
        
            
            //Filters
            foreach($this->filters as $name => $value) {
                echo ".filter('{$name}', {$value})\r\n\t\t";
            }
        
        
        //Includes Separados por Sessões
        foreach($this->toLoad as $section => $file) {
            switch($section) {
                case 'controller':
                        $controllers = array();
                        ob_start();
						echo ".controller('$this->controller', function($requires) {\r\n";
                        if(is_array($this->vars)) {
                            foreach($this->vars as $var => $value) {
                                
                                if(is_bool($value)) {
                                    $value = ($value === true) ? 'true' : 'false';
                                    echo "\t\t\t\$scope.$var = $value;\r\n";
                                } else
                                if(is_int($value)) {
                                    echo "\t\t\t\$scope.$var = $value;\r\n";
                                } else
                                if(is_array($value)) {
                                    $value = $this->array2javascriptobject($value);
                                    echo "\t\t\t\$scope.$var = $value;\r\n";
                                } else {
                                    echo "\t\t\t\$scope.$var = \"$value\";\r\n";
                                }
                            }
                        }
						foreach($file as $f) {
                            
                            //Incluir Arquivo para $content
                            ob_start();
                            if(file_exists($includePath.$f)) $f = $includePath.$f;
                            if(file_exists($f)) { 
                                include($f); 
                            } else {

							
							

                                //Arquivo não exite, será uma URL?
                                if(substr($f, 0, 4) == 'http') {
                                    $cloud = (new cUrlProcess($f))
                                        ->setOption(CURLINFO_HEADER_OUT, false)
                                        ->setOption(CURLOPT_HEADER, false)
                                        ->setMethod("GET")
                                        ->exec();
                                    //echo $cloud['result'];
                                } else {
                                
                                    if(is_developer()) {
                                        echo "\t\t\tconsole.error(\"fail to load $f \");"; 
                                        echo $f;
                                    }
                                }							
							
							
                                }
                            $content = ob_get_clean();
                            
                            //Detecção de Encoding
                            $encoding_file = (isset($this->fileEncoding[$f])) ? $this->fileEncoding[$f] : 'ISO-8859-1';
                            $encoding_detect = \Encoding::detect($content);
                            if(is_developer()) {
                                echo "console.info(\"arquivo $f is set as {$encoding_file}\");";
                                echo "console.info(\"arquivo $f is detected as {$encoding_detect}\");";
                            }

                            switch("{$encoding_file}/$encoding_detect") {
                                case 'UTF-8/ISO-8859-1':
                            $content = utf8_encode($content);
                                break;
                                case 'ISO-8859-1/ISO-8859-1':
                                    $content = \Encoding::fixUTF8($content);
                                break;
                            }
                            
                            

                            
                            
                            

                            
                            
                            /*
                            if(!mb_check_encoding($content, 'UTF-8')
                                OR !($content === mb_convert_encoding(mb_convert_encoding($content, 'UTF-32', 'UTF-8' ), 'UTF-8', 'UTF-32'))) {

                                $content = mb_convert_encoding($content, 'UTF-8', 'pass'); 
                            }
                            */
                            //Exibição Final
                            
                            echo $content;
                            
                            
                            
                            /*
                            if(mb_detect_encoding($content) == "UTF-8") {
                                $content = utf8_encode($content);
                            } else {
                                //$content = utf8_encode($content);
                                //$content = Encoding::toLatin1($content);
                                $content = Encoding::toUTF8($content);
                                //$content = Encoding::toISO8859($content);
                            }
                            */
                            
                            
                            

							echo "\r\n";
						}
                        if(isset($_SESSION['RBComponent_toAngularJs'])) { echo $_SESSION['RBComponent_toAngularJs']."\r\n"; unset($_SESSION['RBComponent_toAngularJs']); }
						echo "\r\n})\r\n";
                        $controllers[] = ob_get_clean();
					break;
					case 'function':
						$functions = array();
						foreach($file as $f) {
							ob_start();
                            if(file_exists($includePath.$f)) $f = $includePath.$f;
							include($f);
							echo "\r\n";
							$functions[] = ob_get_clean();
						}
					break;
					case 'directive':
						$directives = array();
						foreach($file as $f) {
							ob_start();
                            if(file_exists($includePath.$f)) $f = $includePath.$f;
							include($f);
							echo "\r\n";
							$directives[] = ob_get_clean();
						}
					break;
					default:
						foreach($file as $f) {
							include($f);
							echo "\r\n";
						}
					break;
            }
        }
        
        

        if( isset($directives) and is_array($directives) ) {
            foreach($directives as $f) {
                echo $f;
                echo "\r\n";
            }
        }
        
        

        if( isset($controllers) and is_array($controllers) ) {
            foreach($controllers as $f) {
                echo $f;
                echo "\r\n";
            }
        }
        if( isset($functions) and is_array($functions) ) {
            foreach($functions as $f) {
                echo $f;
                echo "\r\n";
            }
        }
        
        

        //Finalização do mainScript
        echo "</script>";
		$html = smart_replace(null, $this->replaces, true);
		echo utf8_decode($html);
        
        

        echo "<!--//AngularJS::end//-->\r\n";
        
        if($capture) {
            $code = ob_get_clean();
            return $code;
            /*
            file_put_contents('log/'.md5($code).'.js', $code);
            return '<script src="'.'log/'.md5($code).'.js'.'"></script>';
             * 
             */
        }
        
    }
    
    function array2javascriptobject(array $array, string $open = '{ ', string $close = ' }'):string {
        $res = $open;
        foreach($array as $chave => $value) {
            $res.= $chave.': ';
            if(is_int($value)) {
                $res.= $value;
            } else
            if(is_array($value)) {
                $res.= $this->array2javascriptobject($value, '[', ']');
            } else {
                $res.= "'{$value}'";
            }
            $res.= ', ';
        }
        $res = rtrim($res, ', ');
        $res.= $close;
        return $res;
    }
    
    /**
	* @deprecate
	*/
	
	 public function renderold() {
        
        plugin('rbincludes');
        
        $includePath = $this->includePath;
        $requires = implode(",", $this->controllerRequires);
        
        echo "<!--//AngularJS::ini//-->\r\n";
        
        //LoadScripts
        if(is_array($this->scripts))
        foreach($this->scripts as $script) {
            include_conditional($script, false);
        }
        
        //Abertura do mainScript
        ob_start();
		echo "<script>";
		echo "
		\r\n
		window.angular.module('$this->app', [])	\r\n";
        
        //Includes Separados por Sessões
        foreach($this->toLoad as $section => $file) {
            switch($section) {
                case 'controller':
                        $controllers = array();
                        ob_start();
						echo ".controller('$this->controller', function($requires) {\r\n";
                        if(is_array($this->vars)) {
                            foreach($this->vars as $var => $value) {
                                echo "\t\$scope.$var = \"$value\";\r\n";
                            }
                        }
						foreach($file as $f) {
                            ob_start();
                            if(file_exists($includePath.$f)) $f = $includePath.$f;
                            
                            if(file_exists($f)) { 
                                include($f); 
                                
                            } else {
                                //Arquivo não exite, será uma URL?
                                if(substr($f, 0, 4) == 'http') {
                                    $cloud = (new cUrlProcess($f))
                                        ->setOption(CURLINFO_HEADER_OUT, false)
                                        ->setOption(CURLOPT_HEADER, false)
                                        ->setMethod("GET")
                                        ->exec();
                                    //echo $cloud['result'];
                                } else {
                                
                                    //echo "console.error(\"arquivo $f não existe\");"; 
                                }
                            }
                            $content = ob_get_clean();
                            if(mb_detect_encoding($content) == "UTF-8") 
                            $content = utf8_encode($content);
                            echo $content;
                            
							echo "\r\n";
						}
						echo "\r\n})\r\n";
                        $controllers[] = ob_get_clean();
					break;
					case 'function':
						$functions = array();
						foreach($file as $f) {
							ob_start();
                            if(file_exists($includePath.$f)) $f = $includePath.$f;
							include($f);
							echo "\r\n";
							$functions[] = ob_get_clean();
						}
					break;
					case 'directive':
						$directives = array();
						foreach($file as $f) {
							ob_start();
                            if(file_exists($includePath.$f)) $f = $includePath.$f;
							include($f);
							echo "\r\n";
							$directives[] = ob_get_clean();
						}
					break;
					default:
						foreach($file as $f) {
							include($f);
							echo "\r\n";
						}
					break;
            }
        }
        
        if( isset($directives) and is_array($directives) ) {
            foreach($directives as $f) {
                echo $f;
                echo "\r\n";
            }
        }
        
        if( isset($controllers) and is_array($controllers) ) {
            foreach($controllers as $f) {
                echo $f;
                echo "\r\n";
            }
        }
        if( isset($functions) and is_array($functions) ) {
            foreach($functions as $f) {
                echo $f;
                echo "\r\n";
            }
        }
        
        //Finalização do mainScript
        echo "</script>";
		$html = smart_replace(null, $this->replaces, true);
		echo utf8_decode($html);
        
        echo "<!--//AngularJS::end//-->\r\n";
        
    }
    

    
    
}
