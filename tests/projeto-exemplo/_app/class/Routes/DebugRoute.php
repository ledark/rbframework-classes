<?php

namespace Routes;

use RBFrameworks\BladeOne;

class DebugRoute
{

    /**
     * @route GET /api/session
     * @status 200
     * @response json
     * @utf8 true
     **/
    public function debugPage(): array
    {
        return $_SESSION;
    }

}