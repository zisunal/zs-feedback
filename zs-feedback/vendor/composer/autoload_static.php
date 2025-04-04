<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit8f9247d53b141775c61d4107d6927513
{
    public static $prefixLengthsPsr4 = array (
        'Z' => 
        array (
            'Zs\\ZsFeedback\\' => 14,
            'ZS\\' => 3,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Zs\\ZsFeedback\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
        'ZS\\' => 
        array (
            0 => __DIR__ . '/../..' . '/classes',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit8f9247d53b141775c61d4107d6927513::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit8f9247d53b141775c61d4107d6927513::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit8f9247d53b141775c61d4107d6927513::$classMap;

        }, null, ClassLoader::class);
    }
}
