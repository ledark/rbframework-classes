<?php 

namespace RBFrameworks\Core\Database;

class Query {    
    
    use Traits\QueryVariables;
    use Traits\QuerySelect;
    use Traits\QueryUpdate;
    use Traits\QueryInsert;
    use Traits\QueryDelete;
    use Traits\QueryWhere;

}