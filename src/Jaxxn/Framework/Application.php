<?php
namespace Jaxxn\Framework;

use Jaxxn\Support\Interfaces\ISingleton;

class Application implements ISingleton
{
    private $baseDir;

    public static $instance;

    function __construct($baseDir)
    {
        $this->baseDir = $baseDir;
        static::$instance = $this;
    }

    /**
     * @return mixed
     */
    public function getBaseDir()
    {
        return $this->baseDir;
    }

    /**
     * @param mixed $baseDir
     */
    public function setBaseDir($baseDir)
    {
        $this->baseDir = $baseDir;
    }

    static function instance()
    {
        return static::$instance;
    }
}
