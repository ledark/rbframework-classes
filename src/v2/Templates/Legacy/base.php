<?php 

use RBFrameworks\Core\Types\File;

$var = function(string $varname) {
    echo $varname;
};
$page = function(string $page = null) {
    $page = is_null($page) ? $this->page : $page;

    $templateObject = new File($page);            
    $templateObject->addSearchFolders($this->getSearchFolders());
    $templateObject->addSearchExtensions($this->getSearchExtensions());
    $templateObject->addSearchExtension('.tmpl');

    include($templateObject->getFilePath());
};
$filePath = (string) $template->getFilePath();

include($filePath); 

?>