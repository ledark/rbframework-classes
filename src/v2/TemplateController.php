<?php 

namespace RBFrameworks\Core;

class TemplateController extends Template {

    public $headerPage;
    public $footerPage;
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
        $return = $this->renderTemplate($this->headerPage);
        $return.= $this->prefixContent;
        foreach($pages as $page) {
            $return .= $this->renderTemplate($page);
        }
        $return.= $this->sufixContent;
        $return.= $this->renderTemplate($this->footerPage);
        return $this->renderTemplate($this->templatePage, ['page' => $return]);
    }
}