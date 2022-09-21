<?php 

namespace RBFrameworks\Core;

class TemplateController extends Template {

    public $templatePage = 'bootstrap5';
    public $prefixContent = '<div class="container">';
    public $sufixContent = '</div>';

    public function __construct(string $headerPage, string $footerPage) {
        $this->headerPage = $headerPage;
        $this->footerPage = $footerPage;
    }

    public function setPrefixContent(string $content) {
        $this->prefixContent = $content;
    }
    public function setSufixContent(string $content) {
        $this->sufixContent = $content;
    }

    public function setTemplatePage(string $templatePage) {
        $this->templatePage = $templatePage;
    }

    public function renderPages(array $pages = []) {
        $return = $this->renderPage($this->headerPage);
        $return.= $this->prefixContent;
        foreach($pages as $page) {
            $return .= $this->renderPage($page);
        }
        $return.= $this->sufixContent;
        $return.= $this->renderPage($this->footerPage);
        return $this->renderPage($this->templatePage, ['page' => $return]);
    }
}