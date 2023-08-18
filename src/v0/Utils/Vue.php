<?php

namespace RBFrameworks\Utils;

/**
 * Utilização comum seria:
 * (new RBFrameworks\Helpers\Vue())->useComponents(['nome'])->run();
 *
 * @version 2.1
 * @author Ricardo Bermejo
 */
class Vue {
    
    public $components = [];
    public $components_replaces = [];
    public $verbose = false;
    public $inline = false;
    public $captureRender = false;
    public $global_replaces = [];
    
    private $search_paths = [
        '',
        'sys/vue/',
        'sys/vue/components/',
    ];
    
    private $search_extensions = ['', '.js', '.vue'];
    
    public function __construct() {
        $this->addPath(dirname(debug_backtrace()[0]['file']));
    }
    
    public function addPath(string $pathname): object {
        if(substr($pathname, -1) != '/') $pathname.= '/';
        array_unshift($this->search_paths, $pathname);
        return $this;
    }
    
    public function verbose(bool $active = true) {
        $this->verbose = $active;
        return $this;
    }
    
    public function inline(bool $active = true) {
        $this->inline = $active;
        return $this;
    }
    
    public function captureRender(bool $active = true) {
        $this->captureRender = $active;
        return $this;
    }
    
    public function replaces($replaces) {
        if(is_array($replaces)) {
            $this->global_replaces = array_merge($this->global_replaces, $replaces);
        }
        return $this;
    }
    
    public function useComponentsDir(string $path_to_components, array $common_replaces = []) {
        if(is_dir($path_to_components)) {
            $this->addPath($path_to_components);
            $components = [];
            foreach (new \DirectoryIterator($path_to_components) as $fileInfo) {
                if($fileInfo->isDot()) continue;
                $components[] = $fileInfo->getFilename();
            }
            $this->useComponents($components, $common_replaces);
        }
        return $this;
    }
    
    public function useComponents(array $components, array $common_replaces = []) {
        foreach($components as $file => $replaces) {
            if(is_numeric($file) and is_string($replaces)) {
                $this->useComponent($replaces);
            } else
            if(is_string($file) and is_array($replaces)) {
                $this->useComponent($file, array_merge($common_replaces, $replaces));
            }            
        }
        return $this;
    }
    
    public function useComponent(string $name, array $replaces = []): object {
        foreach($this->search_paths as $path) {
            foreach($this->search_extensions as $ext) {
                if(file_exists($path.$name.$ext)) {
                     if($this->verbose) echo "<script>console.info(\"O arquivo VUE [{$path}{$name}{$ext}] foi inclu�do com sucesso!\");</script>";
                    $this->addFileComponent($path.$name.$ext, $replaces);
                    return $this;
                } else {
                    if($this->verbose) echo "<script>console.error(\"O arquivo VUE [{$path}{$name}{$ext}] n�o pode ser inclu�do\");</script>";
                }
            }
        }
        echo "<script>console.error(\"Nenhum arquivo VUE [$name] n�o pode ser inclu�do\");</script>";
        return $this;
    }
    
    public function addFileComponent(string $file, array $replaces = []) {
        $this->components[] = $file;
        $this->components_replaces[$file] = $replaces;
        return $this;
    }

    public function smart_replacef(string $file, array $replaces = []):string {
        plugin("smart_replace");
        return smart_replacef($file, $replaces, true);
    }
    
    public function run() {
        plugin("smart_replace");
        if($this->captureRender) ob_start ();
        if($this->inline) {
            echo '<script language="javascript">'."\r\n";
            foreach($this->components as $component_file) {
                if($this->verbose) echo 'console.warn("'.$component_file.'"); ';
                echo smart_replacef($component_file, array_merge($this->global_replaces, $this->components_replaces[$component_file]))."\r\n";
            }
            echo "\r\n".'</script>';
        } else {
            plugin("files");
            foreach($this->components as $component_file) {
                echo tagfilestream($component_file, array_merge($this->global_replaces, $this->components_replaces[$component_file]), 'js', 'language="javascript"');
            }
        }
        if($this->captureRender) return ob_get_clean();
    }
    
    
    
}