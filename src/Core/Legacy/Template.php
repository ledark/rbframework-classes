<?php 

namespace Sox;

use RBFrameworks\Core\Http;
use RBFrameworks\Core\Assets;
use RBFrameworks\Core\Types\File;
use RBFrameworks\Core\Utils\Strings\Dispatcher;
use RBFrameworks\Core\Assets\StreamFile;
use RBFrameworks\Core\Debug;
use RBFrameworks\Core\Session;
use RBFrameworks\Core\Legacy\Template;

class Render extends \Utils\Render {
    
    public function __construct() {
        parent::__construct('front/startbootstrap-sb-admin-gh-pages/blank', ...func_get_args());
    }

    public static function menuGroup(string $groupName) {
        echo '<div class="sb-sidenav-menu-heading">'.$groupName.'</div>';
    }

    public static function menuItem(string $href, string $text, string $class) {
        echo '<a class="nav-link" href="'.Http::getSite().$href.'">
            <div class="sb-nav-link-icon"><i class="'.$class.'"></i></div>
            '.$text.'
        </a>';
    }

    public static function tableAssetsUsingOficialCDN(array $scripts) {
        $head = '<link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.0.0/af-2.7.0/b-3.0.0/b-colvis-3.0.0/b-html5-3.0.0/b-print-3.0.0/cr-2.0.0/date-1.5.2/fc-5.0.0/fh-4.0.0/kt-2.12.0/r-3.0.0/rg-1.5.0/rr-1.5.0/sc-2.4.0/sb-1.7.0/sp-2.3.0/sl-2.0.0/sr-1.4.0/datatables.min.css" rel="stylesheet">';
        $body = '<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>';
        $body.= '<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>';
        $body.= '<script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.0.0/af-2.7.0/b-3.0.0/b-colvis-3.0.0/b-html5-3.0.0/b-print-3.0.0/cr-2.0.0/date-1.5.2/fc-5.0.0/fh-4.0.0/kt-2.12.0/r-3.0.0/rg-1.5.0/rr-1.5.0/sc-2.4.0/sb-1.7.0/sp-2.3.0/sl-2.0.0/sr-1.4.0/datatables.min.js"></script>';
        Assets::Render('head.end', $head);
        Assets::Render('body.end', $body);
        foreach($scripts as $script) {
            Assets::Render('body.end', '<script src="'.$script.'"></script>');
        }                
    }

    public static function tableAssets(array|string $scripts) {
        //Essencial Assets
        $head = '<link href="{httpSite}front/startbootstrap-sb-admin-gh-pages/assets/datatable/datatables.min.css" rel="stylesheet" />';
        $body = '<script src="{httpSite}front/startbootstrap-sb-admin-gh-pages/assets/datatable/datatables.min.js"></script>';
        $body.= '<script src="{httpSite}front/startbootstrap-sb-admin-gh-pages/assets/datatable/pdfmake.min.js"></script>';
        $body.= '<script src="{httpSite}front/startbootstrap-sb-admin-gh-pages/assets/datatable/vfs_fonts.js"></script>';
        Assets::Render('head.end', $head);
        Assets::Render('body.end', $body);
        if(is_array($scripts)) {
            foreach($scripts as $script) {
                $fileScript = new File($script);
                if($fileScript->hasFile()) {
                    Assets::Render('body.end', '<script src="'.StreamFile::getUri($fileScript->getFilePath()).'"></script>');
                } else {
                    Assets::Render('body.end', '<script src="'.$script.'"></script>');
                }
            }
        } else {
            $scriptDefaultDemoFileContent = file_get_contents(get_root_path().'front/startbootstrap-sb-admin-gh-pages/js/datatables-simple-demo.js');
            $scriptDefaultDemoFileContent = str_replace('#datatablesSimple', $scripts, $scriptDefaultDemoFileContent);
            Assets::Render('body.end', '<script>'.$scriptDefaultDemoFileContent.'</script>');            
        }        
    }

    public static function tableAssetsSimple(array $scripts) {        
        Assets::Render('head.end', '<link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />');
        Assets::Render('body.end', '<script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>');
        foreach($scripts as $script) {
            Assets::Render('body.end', '<script src="'.$script.'"></script>');
        }
    }

    /**
     * table function cria uma tabela com os dados passados
     *
     * @param string $title
     * @param array $data
     * @param string $id
     * @param boolean $capture
     * @param array $dict
     *  Um dicionário ($dict) pode ser enviado como ['field_name' => 'Nome do Campo', 'field_name2' => 'Nome do Campo 2']
     *  Ou se preferir, pode senviar também callbacks para os valores, exemplo:
     * ['field_name' => function($value, $context = 'thead|tbody') { return $value; }]
     * Nos nomes dos campos dentro do dicionário use também '+field_name' para adicionar um campo novo ou '-field_name' para remover um campo
     * @return void
     */
    public static function table(string $title, array $data, string $id = 'datatablesSimple', bool $capture = false, array $dict = []) {
        
        //Normalize Dict
        foreach($dict as $key => $value) {
            if(is_int($key)) {
                $dict[$value] = $value;
                unset($dict[$key]);
            }
        }

        //Resolve Dict Utilitie
        $resolveDict = function($key, $value, $context) use ($dict) {
            if(isset($dict['-'.$key])) {
                return null;
            }
            if(isset($dict[$key])) {
                if(is_callable($dict[$key])) {
                    return $dict[$key]($value, $context);
                } else {
                    if($context == 'thead') {
                        return $dict[$key];
                    } else {
                        return $value;                    
                    }
                }
            } else {
                if($context == 'thead') {
                    $value = str_replace('_', ' ', $key);
                    $value = ucfirst($value);
                }
                return $value;
            }
        };

        //Create new Columns
        foreach($dict as $key => $value) {
            if($key[0] == '+') {
                foreach($data as $i => $row) {
                    foreach($row as $rkey => $rvalue) {
                        $data[$i][substr($key, 1)] = '::executeCallback::';
                    }
                }
            }
        }


        

        
        /*
        //Order Columns
        $columnsOrdered = [];
        foreach(array_keys($columnsOrder) as $key) {
            $columnsOrdered[] = $key;
        }
        
        //Nomes das Colunas na Ordem Original
        $columnsOriginal = [];
        foreach(array_keys($data[0]) as $key) {
            $key = $resolveDict($key, $key, 'thead');
            if(is_null($key)) continue;
            if(in_array($key, $columnsOrdered)) continue;
            $columnsOriginal[] = $key;
        }

        //Merge Columns
       // $columnsOrdered = array_merge($columnsOrdered, $columnsOriginal);
       $columnsOrdered = array_merge($columnsOrdered, $columnsOriginal);
       unset($columnsOriginal);
       */

        //Create tHead
        $theadColumns = [];
        if(!isset($data[0] )) $data[0] = $data;
        foreach($data[0] as $key => $value) {
            $key = $resolveDict($key, $key, 'thead');
            if(is_null($key)) continue;
            $theadColumns[] = $key;
        }
        $thead = implode('', array_map(function($column) { return '<th scope="col">'.$column.'</th>'; }, $theadColumns));
        unset($theadColumns);

        //Create tBody
        $tbody = '';
        foreach($data as $row) {
            $tbody .= '<tr>';
            foreach($row as $key => $value) {
                if($value == '::executeCallback::') {
                    $value = $dict['+'.$key]($row);
                } else {
                    $value = $resolveDict($key, $value, 'tbody');
                    if(is_null($value) and is_null($resolveDict($key, $value, 'thead'))) continue;
                    if(is_array($value)) {
                        $value = self::table($key, $value, md5(serialize($value)), true);
                    }
                }
                $tbody .= '<td>'.$value.'</td>';

            }
            $tbody .= '</tr>';
        }
        if(empty($title)) {
            $return = Template::usar('RenderTable-withoutCard', ['title' => $title, 'id' => $id, 'thead' => $thead, 'tbody' => $tbody]);
        } else {
            $return = Template::usar('RenderTable-withCard', ['title' => $title, 'id' => $id, 'thead' => $thead, 'tbody' => $tbody]);
        }
        if($capture) return $return; else echo $return;
    }

    public static function jsModules(array $scripts):void {

        $postScript = '';

        $file = function(string $fileWithLine):string {
            $parts = explode(':', $fileWithLine);
            $file = array_shift($parts);            
            return dirname($file).'/'.basename($file, '.php');
        };

        $backtrace = Debug::getFileBacktrace();
        
        $fileA = $file(array_pop($backtrace));
        $fileB = $file(array_pop($backtrace));
        $fileC = $file(array_pop($backtrace));

        if(file_exists($fileA.'.js')) $scripts['app'] = $fileA.'.js';
        if(file_exists($fileA.'.vue.js')) $scripts['app2'] = $fileA.'.vue.js';
        if(file_exists($fileB.'.js')) $scripts['app3'] = $fileB.'.js';
        if(file_exists($fileB.'.vue.js')) $scripts['app4'] = $fileB.'.vue.js';
        if(file_exists($fileC.'.js')) $scripts['app5'] = $fileC.'.js';
        if(file_exists($fileC.'.vue.js')) $scripts['app6'] = $fileC.'.vue.js';

        $modules = [];
        foreach($scripts as $name => $path) {
            $file = (new File($path))
                ->addSearchFolders([
                    get_root_path().'front/dashboard/',
                    get_root_path().'front/startbootstrap-sb-admin-gh-pages/js/',
                ])
                ->clearSearchExtensions()
                ->addSearchExtensions(['', '.js', '.vue.js']);
            $content = $file->getFileContents();
            if(strpos($content, '[ignoreRender()]') !== false) {

            } else
            if(strpos($content, '[preventRender()]') !== false) {
                ob_start();
                echo '<script type="module">'; 
                include($file->getFilePath());
                echo '</script>';
                $postScript .= ob_get_clean();
            } else {
                $modules[$name] = $file->getFilePath();
            }


        }
        StreamFile::jsModules($modules);

        echo $postScript;
    }

    /**
     * jsModulesSession function cria a sessão js_modules caso não exista e adiciona os scripts a ela
     * Esses scripts são renderizados no final do documento com a função jsModulesRender
     *
     * @param array $scripts
     * @return void
     */
    public static function jsModulesSession(array $scripts):void {
        if(!isset($_SESSION['js_modules']) or !is_array($_SESSION['js_modules'])) {
            $_SESSION['js_modules'] = [];
        }
        $_SESSION['js_modules'] = array_merge($_SESSION['js_modules'], $scripts);
    }

    public static function jsModulesRender():void {
        if(isset($_SESSION['js_modules']) and is_array($_SESSION['js_modules'])) {
            self::jsModules($_SESSION['js_modules']);
        }
    }

    /**
     * jsInjectorVueScriptData function você passa qualquer pedaço de código e ele captura os tokens em pedaços
     *
     * @param string $vuejscode
     * @return string
     */
    public static function jsInjectorVueScriptBlocks(string $vuejscode):void {

        if(file_exists($vuejscode)) {
            ob_start();
            include($vuejscode);
            $vuejscode = ob_get_clean();
        }
        
        $piece = function(string $start, string $end, string $vuejscode):string {
            $start = str_replace('/', '\/', $start);
            $end = str_replace('/', '\/', $end);
            $re = '/'.$start.'\s(.*)'.$end.'/misx';
            preg_match_all($re, $vuejscode, $matches, PREG_SET_ORDER, 0);
            if(!isset($matches[0]) or !isset($matches[0][1])) return '';
            return $matches[0][1];
        };


        self::jsModulesBlocksSession('vuejs.before',        $piece('//VueJS::insertBefore::ini', '//VueJS::insertBefore::end', $vuejscode));
        self::jsModulesBlocksSession('vuejs.components',    $piece('//VueJS::Components::ini', '//VueJS::Components::end', $vuejscode));
        self::jsModulesBlocksSession('vuejs.data',          $piece('//VueJS::Data::ini', '//VueJS::Data::end', $vuejscode));
        self::jsModulesBlocksSession('vuejs.computed',      $piece('//VueJS::Computed::ini', '//VueJS::Computed::end', $vuejscode));
        self::jsModulesBlocksSession('vuejs.methods',       $piece('//VueJS::Methods::ini', '//VueJS::Methods::end', $vuejscode));
        self::jsModulesBlocksSession('vuejs.mounted',       $piece('//VueJS::Mounted::ini', '//VueJS::Mounted::end', $vuejscode));

    }

    public static function jsModulesBlocksSession(string $name, string $code = null):void {
        
        if(!isset($_SESSION['js_modules_blocks']) or !is_array($_SESSION['js_modules_blocks'])) {
            $_SESSION['js_modules_blocks'] = [];
        }
        if(!isset($_SESSION['js_modules_blocks'][$name])) {
            $_SESSION['js_modules_blocks'][$name] = '';
        }
        if(!is_null($code)) {
            $_SESSION['js_modules_blocks'][$name] .= $code;
        }
        if(is_null($code)) {
            echo $_SESSION['js_modules_blocks'][$name];
            $_SESSION['js_modules_blocks'][$name] = '';
        }
    }

}