<?php 

namespace RBFrameworks\Core\Templates\Bootstrapv5;

class Template extends \RBFrameworks\Core\Template {
    
    public function __construct() {
        parent::__construct("adad");
    }

    public function render(string $page) {
        echo $this->renderPage(__DIR__."/Pieces/_head.php");
        echo $this->renderPage($page);
        echo $this->renderPage(__DIR__."/Pieces/_foot.php");
    }
}