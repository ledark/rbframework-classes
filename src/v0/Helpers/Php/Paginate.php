<?php

namespace RBFrameworks\Helpers\Php;

plugin("session");

class Paginate {
    
    public $limiteMin = 0; //Starter Dinâmico da Paginação Atual
    public $limiteMax = 5; //Itens por Página
    public $maxResults = 0; //Contador Dinâmico do Máximo de Itens
    public $maxPages = 1;
    public $currentPageNumber = 1;
    private $query = null;
    private $database = null;
    private $paginateId = 0;
    
    public function __construct(object $doDBv4, object $QueryConstructor, int $itensPorPagina = 5) {
        $this->setDatabase($doDBv4);
        $this->setQuery($QueryConstructor);
        $this->limiteMax = $itensPorPagina;
        $this->paginateId = md5($this->query->render());
        if($this->retrieveSession()) return $this;
        $this->generateMaxResults();
        $this->generateMaxPages();
        $this->generateCurrentPageNumber();
        $this->generateSession();
        return $this;
    }
      
    private function setDatabase(object $database): void {
        $this->database = $database;
    }
    
    private function setQuery(object $query): void {
        $this->query = clone $query;
    }
    
    private function generateMaxResults(): void {
        $query_maxresults = clone $this->query;
        $query_maxresults->clear()->setField('COUNT(*)');
        $this->maxResults = intval($this->database->push($query_maxresults->render()));
    }

    private function generateMaxPages(): void {
        $pageItens = 0;
        for($resultItem = 1; $resultItem <= $this->maxResults; $resultItem++) {
            $pageItens++;
            if($pageItens >= $this->limiteMax){
                $this->maxPages++;
                $pageItens = 0;
            } 
        }
    }
    
    public function generateSession(array $overwrite = array()): void {       
        if(!isset($_SESSION['Paginate'])) {
            $_SESSION['Paginate'] = array();
        }

        if(!isset($_SESSION['Paginate'][$this->paginateId])) $_SESSION['Paginate'][$this->paginateId] = array(
            'currentPageNumber'     => $this->currentPageNumber,
            'limiteMin'     => $this->limiteMin,
            'limiteMax'     => $this->limiteMax,
            'maxResults'    => $this->maxResults,
            'maxPages'      => $this->maxPages,
            'query'         => serialize($this->query),
        );
        $_SESSION['Paginate'][$this->paginateId] = array_merge($_SESSION['Paginate'][$this->paginateId], $overwrite);
    }
    
    public function retrieveSession(): bool {
        if(!isset($_SESSION['Paginate'][$this->paginateId])) {
            return false;
        } else {
            $this->limiteMin = $_SESSION['Paginate'][$this->paginateId]['limiteMin'];
            $this->limiteMax = $_SESSION['Paginate'][$this->paginateId]['limiteMax'];
            $this->maxResults = $_SESSION['Paginate'][$this->paginateId]['maxResults'];
            $this->maxPages = $_SESSION['Paginate'][$this->paginateId]['maxPages'];
            $this->generateCurrentPageNumber();
            return true;
        }
    }
    
    public function get_paginateId(): string {
        return $this->paginateId;
    }

    public function generateCurrentPageNumber(): void {
        $this->currentPageNumber = ($this->limiteMin+$this->limiteMax) / $this->limiteMax;
    }
    
    public function clear() {
        unset($_SESSION['Paginate'][$this->paginateId]);
        return $this;
    }



    public function displayFile(string $filename): void {
        $file = __DIR__.'/Paginate/'.$filename;
        if(file_exists($file.'.css'))  { echo '<style>'; include($file.'.css'); echo '</style>'; }
        if(file_exists($file.'.html'))  include($file.'.html');
        if(file_exists($file.'.php'))   include($file.'.php');
        if(file_exists($file.'.js'))   { echo '<script type="text/javascript">'; include($file.'.js'); echo '</script>'; }
    }
    
    public function displayNavbar(): void {
        $this->displayFile('bs3navbar');
    }
    
    public static function getQuery(string $paginateId): object {
        if(isset($_SESSION['Paginate'][$paginateId]['query'])) return unserialize($_SESSION['Paginate'][$paginateId]['query']);
    }
    
    public static function getFilters(string $paginateId): array {
        $return = self::getFromSession($paginateId, 'filters');
        if(!is_null($return) and is_array($return)) return $return; 
        return array();
    }
    
    private static function getFromSession(string $paginateId, string $session_key) {
        if(isset($_SESSION['Paginate'][$paginateId][$session_key])) {
            return $_SESSION['Paginate'][$paginateId][$session_key];
        } else {
            return null;
        }
    }
       
    public static function setFilters(string $paginateId, array $filter_values): void {
        $_SESSION['Paginate'][$paginateId]['filters'] = $filter_values;
    }
    
}

