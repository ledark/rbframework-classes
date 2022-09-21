<?php 

//Created: 2021-11-27
namespace RBFrameworks\Core\Database;

use RBFrameworks\Core\Types\PropProps;
use RBFrameworks\Core\Database\Modelv2;
use RBFrameworks\Core\Database\Traits\Modelv1;

class Model extends Modelv2{

  public $model = [];

  use Modelv1;

  public function __construct(PropProps $model) {
    foreach ($model->getValue() as $key => $value) {
      $this->model[] = [$key => $value];
    }
    parent::__construct($this->model);
  }

}