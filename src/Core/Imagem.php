<?php

namespace RBFrameworks\Core;

use RBFrameworks\Core\Utils\Strings\Dispatcher;
use RBFrameworks\Core\Utils\Canvas;
use GuzzleHttp\Client;

/**
 * sample:
 *     $pathImage = substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], '/imagem/')+strlen('/imagem/'));
 *     $imagem = new Imagem($pathImage);
 *     $imagem->render();
 *     exit();
 */

class Imagem {

    public $imagemOriginal;

    public $usewebp = false;
    public $manterCache = false;
    public $width;
    public $height;
    public $resolution;
    public $method;

    public $cache_folder = 'log/cache/fotos/';

    public $fallbackImage = '_app/resources/semfoto.jpg';
    

    public function __construct(string $path, array $config = []) {
		
		if(isset($config['fallbackImage'])) $this->fallbackImage = $config['fallbackImage'];
				
        $this->setOriginalPath($path);
        $this->detectUseWebp();
        $this->detectUseCache();
        $this->detectDimensions();
        $this->detectFallback();
    }

    public function detectFallback(string $return = '') {

        $dimensions = $this->getDimensions();
        $w = $dimensions['width'];
        $h = $dimensions['height'];
        $r = $dimensions['resolution'];
        $m = $dimensions['method'];        

        $imagemNull = $this->fallbackImage;
        $imagemNullResized = 'log/cache/fotos/'.$w.'x'.$h.'sem-foto'.$r.'.jpg';
        $imagemResized = $this->getCachedFilename();

        //Validação dos Diretórios e Arquivo Null
        if(!is_dir(dirname($imagemResized))) exit("ERR_INVALID_DIR: $imagemResized");
        if(!is_dir(dirname($imagemNullResized))) exit("ERR_INVALID_DIR: $imagemNullResized");
        if(!file_exists($imagemNull)) exit("ERR_INVALID_NULL: $imagemNull");        

        if($return == 'imagemResized') return $imagemResized;
        if($return == 'imagemNullResized') return $imagemNullResized;
        if($return == 'imagemNull') return $imagemNull;

    }

    public function detectUseWebp() {
        //WebP Suportado pelo Chrome, CriOS ou Firefox
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') !== false
        ||  strpos($_SERVER['HTTP_USER_AGENT'], 'CriOS') !== false
        ||  strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox') !== false
        ) {
            $usewebp = true;
        } 

        if(isset($_GET['nowebp'])) $usewebp = false;

        //Forçar desativação de webp
        $usewebp = false;

        if(isset($_GET['forcewebp'])) $usewebp = true;

        $this->usewebp = $usewebp;
    }

    //A ideia é manter o cache para requisições mais rápidas
    public function detectUseCache() {
        $manterCache = $this->manterCache;
        if(isset($_GET['cache'])) $manterCache = true; 
        if(isset($_GET['nocache'])) $manterCache = false;  
        $this->manterCache = $manterCache;      
    }

    public function isCacheEnabled():bool {
        return $this->manterCache;
    }

    public function isWebp():bool {
        return $this->usewebp;
    }

    public function detectDimensions() {
        $w = (!isset($_GET['w'])) ? 253 : intval($_GET['w']);
        $h = (!isset($_GET['h'])) ? 270 : intval($_GET['h']);
        $r = (!isset($_GET['r'])) ? 100 : intval($_GET['r']);
        $m = (!isset($_GET['m'])) ? 'crop' : $_GET['m']; //crop ou preenchimento
        $this->setDimensions($w, $h, $r, $m);
    }
    public function setDimensions($width, $height, $resolution, $method) {
        $this->width          =         $width ;
        $this->height         =         $height;
        $this->resolution     =         $resolution;
        $this->method         =         $method;
    }

    public function getDimensions():array {
        return [
            'width'         =>        $this->width          ,
            'height'        =>        $this->height          ,
            'resolution'    =>        $this->resolution     ,
            'method'        =>        $this->method         ,
        ];
    }

    public function setOriginalPath(string $path):object {
        $this->imagemOriginal = $path;


        $this->path = $path;
        $this->pathInfo = parse_url($this->path);
        if(isset($this->pathInfo['query'])) {
            parse_str($this->pathInfo['query'], $_GET);
        }
        return $this;
    }

    public function getImagemOriginal() {
        return $this->imagemOriginal;
    }

    public function getCachedFilename(string $sufix = ''):string {

        $imagemOriginal = $this->getImagemOriginal();

        $imagemResized = (strpos($imagemOriginal, '/') !== false) ? explode('/', $imagemOriginal) : $imagemOriginal;

        if(is_array($imagemResized)) {
            
            
            
            $img = array_pop($imagemResized);
            

            
            $imagemResized = array_pop($imagemResized).'-'.$img;
            

            if(strpos($imagemResized, '.') !== false) {
                $dotSeparation = explode('.', $imagemResized);
                $imagemResized = reset($dotSeparation);
            }

            unset($img);
        }

        $dimensions = $this->getDimensions();
        $w = $dimensions['width'];
        $h = $dimensions['height'];
        $r = $dimensions['resolution'];
        $m = $dimensions['method'];

        $imagemResized =  $this->cache_folder. $w.'x'.$h.'-'.Dispatcher::file($imagemResized).'-'.$r.$sufix.'.jpg';
        return $imagemResized;

    }

    public function isRemote() {
        $imagemOriginal = $this->getImagemOriginal();
        if(substr($imagemOriginal, 0, 4) == 'http') return true;
        return false;
    }

    public function saveImagem(string $localSource, string $imagemResized) {

        $dimensions = $this->getDimensions();
        $w = $dimensions['width'];
        $h = $dimensions['height'];
        $r = $dimensions['resolution'];
        $m = $dimensions['method'];               
        
        /*
        if(filesize($imagemOriginal) > 300000) {
            header('Expires: Thu, 01-Jan-70 00:00:01 GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            header('Cache-Control: no-store, no-cache, must-revalidate');
            header('Cache-Control: post-check=0, pre-check=0', false);
            header('Pragma: no-cache');        
            header('Content-Type:image/jpeg');
            header('Content-Length: ' . filesize($imagemOriginal));
            readfile($imagemOriginal);
            exit();
        }
        */
        
        //Toda Renderização da Original acontece aqui
        $Canvas = new Canvas();
        $Canvas
            ->carrega($localSource)
            ->redimensiona($w, $h, $m)
            ->grava($imagemResized, $r)
        ;
    }

    public function debug() {

        $localSource = $this->isRemote() ? $this->getCachedFilename('ori') : $this->getImagemOriginal(); 
        $resizedImage = $this->getCachedFilename(); 


        echo "<pre>";

        echo "A imagem a ser processada provem de: ". $this->getImagemOriginal();
        echo "<br/>";
        echo $this->isRemote() ? 'isRemote' : 'isLocal';
        echo "<br/>";
        echo "<br/>";
        echo "localSource: ".$localSource;
        echo "<br/>";

        echo "resizedImage: ".$resizedImage;
        echo "<br/>";        

        echo $this->getCachedFilename();
        if($this->isWebp()) echo "Usará WEBP!";
        
        print_r($this->pathInfo);
        
        echo "Dimensions:";
        print_r($this->getDimensions());
        echo "</pre>";
    }

    /*
    As condicionais para a renderização de uma imagem serão centradas na URL, que naturalmente é a $this->getImagemOriginal():

        Se $this->isCacheEnabled()

     */
    public function render() {

        if(isset($_GET['cache']) and $_GET['cache'] == 'clear') {
            if(file_exists($this->getCachedFilename())) {
                unlink($this->getCachedFilename());
            }
            if(file_exists($this->getCachedFilename('ori'))) {
                rename($this->getCachedFilename('ori'), $this->getCachedFilename('ori').'-'.time());
            }
        }

        //Garantir que exista uma cópia local em caso de arquivos remotos
        if($this->isRemote()) {
            if(!file_exists($this->getCachedFilename('ori'))) {
                file_put_contents(
                    $this->getCachedFilename('ori'), 
                    $this->guzzle_request($this->getImagemOriginal())
                );     
            }
            $localSource = $this->getCachedFilename('ori'); 
        } else{
            $localSource = $this->getImagemOriginal();
        }

        //Garantir que $resizedImage contenha um arquivo processado
        $resizedImage = $this->getCachedFilename();
        if(!file_exists($resizedImage)) {
            $this->saveImagem($localSource, $resizedImage);
        }

        //Se você já possui um localSource e um resizedImage, então resta exibir o que tem (em caso de cache ativo) ou reprocessar (em caso de cache inativo)
        if(!$this->isCacheEnabled()) {
            $this->saveImagem($localSource, $resizedImage);
        } 

        $this->renderImage($resizedImage);
        
/*        

        if($this->isCacheEnabled()) {
            if(file_exists($this->getCachedFilename())) {
                $this->renderImage($this->getCachedFilename());
            } else {
                file_put_contents(
                    $this->getCachedFilename(), 
                    file_get_contents($localSource)
                );    
            }
        } else {
            echo 'THIS IS NO CACHE ACTIVE';
        }
        */




/*
        if(file_exists($this->getCachedFilename())) {
            $this->renderImage();
        } else {
            if($this->isRemote()) {
                file_put_contents(
                    $this->getCachedFilename(), 
                    $this->guzzle_request($this->getImagemOriginal())
                );
                $this->renderImage();
            } else {

                if(!file_exists($this->getImagemOriginal())) {
                    $this->renderNull();
                }

            }
        }
        */
        $this->debug();
        
    }

    public function saveOriginalImagem() {

        $imagemResized = $this->getCachedFilename();
        $manterCache = $this->isCacheEnabled();
        $imagemOriginal = $this->getImagemOriginal();        

        $dimensions = $this->getDimensions();
        $w = $dimensions['width'];
        $h = $dimensions['height'];
        $r = $dimensions['resolution'];
        $m = $dimensions['method'];               
        
        /*
        if(filesize($imagemOriginal) > 300000) {
            header('Expires: Thu, 01-Jan-70 00:00:01 GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            header('Cache-Control: no-store, no-cache, must-revalidate');
            header('Cache-Control: post-check=0, pre-check=0', false);
            header('Pragma: no-cache');        
            header('Content-Type:image/jpeg');
            header('Content-Length: ' . filesize($imagemOriginal));
            readfile($imagemOriginal);
            exit();
        }
        */
        
        //Toda Renderização da Original acontece aqui
        $Canvas = new Canvas();
        $Canvas
            ->carrega($imagemOriginal)
            ->redimensiona($w, $h, $m)
            ->grava($imagemResized, $r)
        ;
        
        if($this->isWebp()) {
            $imageResouce = imagecreatefromjpeg($imagemResized);
            imagewebp ( $imageResouce, $imagemResized, $r );
            imagedestroy($imageResouce);
        }
    
        header('Content-Type:image/jpeg');
        header('Content-Length: ' . filesize($imagemResized));
        readfile($imagemResized);
        exit();        
    }


    private function guzzle_request($uri, $method = 'GET', $params = []) {
        try {

        ob_start();
        $client = new Client();
        $res = $client->request($method, $uri);
        ob_get_clean();
        if($res->getStatusCode() == 200) {
            return $res->getBody();
        } else {
            $this->renderNull();
        }

    } catch (\Exception $e) {
        $this->renderNull();
    }
        return '';
        
    }    

    public function renderNull() {
        $imagemNull = $this->detectFallback('imagemNull');
        $imagemNullResized = $this->detectFallback('imagemNullResized');


        
        if(file_exists($imagemNullResized)) {
            header('Content-Type:image/jpeg');
            header('Content-Length: ' . filesize($imagemNullResized));
            readfile($imagemNullResized);
            exit();            
        } else {

            $dimensions = $this->getDimensions();
            $w = $dimensions['width'];
            $h = $dimensions['height'];
            $r = $dimensions['resolution'];
            $m = $dimensions['method'];            

            $Canvas = new Canvas();
            $Canvas
                ->carrega($imagemNull)
                ->redimensiona($w, $h, $m)
                ->grava($imagemNullResized, $r)
            ;    
        
            header('Content-Type:image/jpeg');
            header('Content-Length: ' . filesize($imagemNullResized));
            readfile($imagemNullResized);
            exit();    
        }
        exit();
    }

    public function renderImage(string $path) {
        if(file_exists($path)) {
            header('Expires: Thu, 01-Jan-70 00:00:01 GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            header('Cache-Control: no-store, no-cache, must-revalidate');
            header('Cache-Control: post-check=0, pre-check=0', false);
            header('Pragma: no-cache');        
            header('Content-Type:image/jpeg');
            header('Content-Length: ' . filesize($path));
            readfile($path);
            exit();
        } else {
            $this->renderNull();
        }
    }

    public function renderImageConditional() {

        $imagemResized = $this->getCachedFilename();
        $manterCache = $this->isCacheEnabled();
        $imagemOriginal = $this->getImagemOriginal();

        if(file_exists($imagemResized) and $manterCache == true) {
            header('Expires: Thu, 01-Jan-70 00:00:01 GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            header('Cache-Control: no-store, no-cache, must-revalidate');
            header('Cache-Control: post-check=0, pre-check=0', false);
            header('Pragma: no-cache');        
            header('Content-Type:image/jpeg');
            header('Content-Length: ' . filesize($imagemResized));
            readfile($imagemResized);
            exit();
        
        } elseif(file_exists($imagemOriginal)) {

            $dimensions = $this->getDimensions();
            $w = $dimensions['width'];
            $h = $dimensions['height'];
            $r = $dimensions['resolution'];
            $m = $dimensions['method'];               
            
            /*
            if(filesize($imagemOriginal) > 300000) {
                header('Expires: Thu, 01-Jan-70 00:00:01 GMT');
                header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
                header('Cache-Control: no-store, no-cache, must-revalidate');
                header('Cache-Control: post-check=0, pre-check=0', false);
                header('Pragma: no-cache');        
                header('Content-Type:image/jpeg');
                header('Content-Length: ' . filesize($imagemOriginal));
                readfile($imagemOriginal);
                exit();
            }
            */
            
            //Toda Renderização da Original acontece aqui
            $Canvas = new Canvas();
            $Canvas
                ->carrega($imagemOriginal)
                ->redimensiona($w, $h, $m)
                ->grava($imagemResized, $r)
            ;
            
            if($this->isWebp()) {
                $imageResouce = imagecreatefromjpeg($imagemResized);
                imagewebp ( $imageResouce, $imagemResized, $r );
                imagedestroy($imageResouce);
            }
        
            header('Content-Type:image/jpeg');
            header('Content-Length: ' . filesize($imagemResized));
            readfile($imagemResized);
            exit();
            
        }
        
    }    
}