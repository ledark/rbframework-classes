<?php

namespace Routes;

use Admin\Blade;

class AdminRoute
{

    /**
     * @route GET /admin/dashboard
     * @route GET /admin/
     * @status 200
     * @response html
     * @utf8 true
     * @before \Admin\Auth::isAnybody
     * @descr Pagina inicial do admin
     **/
    public function adminDashboard(): string
    {
        return Blade::render('admin.dashboard');
    }

}