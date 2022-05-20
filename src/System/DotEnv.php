<?php

namespace App\System;

use Symfony\Component\Dotenv\Dotenv AS De;

class DotEnv {

    /** @var DotEnv  */
    private $DotEnv;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->DotEnv = new De();
        $this->DotEnv->usePutenv();
        $this->DotEnv->loadEnv(__DIR__.'/../../.env');
    }

    /**
     * @param string $variableName
     * @return array|false|string
     */
    public function getEnv(string $variableName)
    {
        return getenv($variableName);
    }
}
