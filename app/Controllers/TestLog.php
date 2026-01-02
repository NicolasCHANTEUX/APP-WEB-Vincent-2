<?php

namespace App\Controllers;

class TestLog extends BaseController
{
    public function index()
    {
        log_message('error', '=== TEST LOG ERROR ===');
        log_message('info', '=== TEST LOG INFO ===');
        log_message('debug', '=== TEST LOG DEBUG ===');
        
        return 'Logs écrits ! Vérifiez writable/logs/log-' . date('Y-m-d') . '.log';
    }
}
