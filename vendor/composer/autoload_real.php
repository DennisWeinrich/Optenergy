<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInitb36f1d627e26f5b2fac835d793bdb21c
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        require __DIR__ . '/platform_check.php';

        spl_autoload_register(array('ComposerAutoloaderInitb36f1d627e26f5b2fac835d793bdb21c', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInitb36f1d627e26f5b2fac835d793bdb21c', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInitb36f1d627e26f5b2fac835d793bdb21c::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
