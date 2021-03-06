<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitcbc2e21cf93f9573570ac3a6b3f8f5cb
{
    public static $prefixLengthsPsr4 = array (
        'F' => 
        array (
            'Firebase\\JWT\\' => 13,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Firebase\\JWT\\' => 
        array (
            0 => __DIR__ . '/..' . '/firebase/php-jwt/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitcbc2e21cf93f9573570ac3a6b3f8f5cb::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitcbc2e21cf93f9573570ac3a6b3f8f5cb::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
