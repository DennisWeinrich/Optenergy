<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitb36f1d627e26f5b2fac835d793bdb21c
{
    public static $prefixLengthsPsr4 = array (
        'R' => 
        array (
            'ReallySimpleJWT\\' => 16,
        ),
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'ReallySimpleJWT\\' => 
        array (
            0 => __DIR__ . '/..' . '/rbdwllr/reallysimplejwt/src',
        ),
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitb36f1d627e26f5b2fac835d793bdb21c::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitb36f1d627e26f5b2fac835d793bdb21c::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitb36f1d627e26f5b2fac835d793bdb21c::$classMap;

        }, null, ClassLoader::class);
    }
}
