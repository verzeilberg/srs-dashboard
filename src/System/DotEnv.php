<?php

namespace App\System;

use Symfony\Component\Dotenv\Dotenv AS De;

class DotEnv {

    /** @var DotEnv  */
    private $DotEnv;

    public function __construct()
    {
        $this->DotEnv = new De();
        $this->DotEnv->usePutenv();
        $this->DotEnv->loadEnv(__DIR__.'/../../.env');
    }
}
