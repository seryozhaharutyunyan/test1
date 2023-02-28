<?php

function path($section): string
{
    $phatMask = ROOT_DIR . DS .'App'.DS. '%s';
    $phatConfig = ROOT_DIR . DS .'engine'.DS. '%s';


    return match (strtolower($section)) {
        'controller' => sprintf($phatMask, 'Controller'),
        'config' => sprintf($phatConfig, 'Config'),
        'messages' => ROOT_DIR . DS .'messages',
        'model' => sprintf($phatMask, 'Model'),
        'language' => sprintf($phatMask, 'Language'),
        default => ROOT_DIR,
    };
}

function path_content($section = ''): string
{
    return match (strtolower($section)) {
        'themes' => path('content') . '/themes',
        'plugins' => path('content') . '/plugins',
        'uploads' => path('content') . '/uploads',
        default => path('content'),
    };
}

function language(): array
{
    $directory = path('language');
    $list = scandir($directory);
    $languages = [];

    if (!empty($list)) {
        unset($list[0]);
        unset($list[1]);

        foreach ($list as $dir) {
            $pathLangDir = $directory . DS . $dir;
            $pathConfig = $pathLangDir . "/config.json";

            if (is_dir($pathLangDir) && is_file($pathConfig)) {
                $config = file_get_contents($pathConfig);
                $info = json_decode($config);
                $languages[] = $info;
            }

        }
    }

    return $languages;
}

function getPlugins(): array
{
    $pluginsPath = path_content('plugins');
    $list = scandir($pluginsPath);
    $plugins = [];

    if (!empty($list)) {
        foreach ($list as $namePlugin) {

            if ($namePlugin === '.' || $namePlugin === '..') continue;

            $namespace = '\\Plugin\\' . $namePlugin . '\\Plugin';

            if (class_exists($namespace)) {
                $plugin = new $namespace();
                $plugins[$namePlugin] = $plugin->details();
            }
        }
    }

    return $plugins;
}

function get_themes(): array
{
    $themesPath = '../content/themes';
    $list = scandir($themesPath);
    $baseUrl = Engine\Core\Config\Config::item('baseUrl');
    $themes = [];

    if (!empty($list)) {
        foreach ($list as $dir) {
            // Ignore hidden directories.
            if ($dir === '.' || $dir === '..') continue;

            $pathThemeDir = $themesPath . '/' . $dir;
            $pathConfig = $pathThemeDir . '/theme.json';
            $pathScreen = $baseUrl . '/content/themes/' . $dir . '/screen.jpg';

            if (is_dir($pathThemeDir) && is_file($pathConfig)) {
                $config = file_get_contents($pathConfig);
                $info = json_decode($config);

                $info->screen = $pathScreen;
                $info->dirTheme = $dir;

                $themes[] = $info;
            }
        }
    }

    return $themes;
}

function getTypes(string $switch = 'page')
{
    $themePath = path_content('themes') . '/' . \Setting::value('active_theme', 'theme');
    $list = scandir($themePath);
    $types = [];

    if (!empty($list)) {
        foreach ($list as $name) {
            // Ignore hidden directories.
            if ($name === '.' || $name === '..') continue;

            if (\Flexi\Helper\Common::searchMatchString($name, $switch)) {
                $chunk = explode('.', $name, 3);

                if ($chunk[0] == $switch && $chunk[1] == 'twig') continue;

                list($switch, $key, $extension) = $chunk;

                // Ignore files.
                if ($key === $switch || $key === 'layout') continue;

                if (!empty($key)) {
                    $types[$key] = ucfirst($key);
                }
            }
        }
    }

    return $types;
}
