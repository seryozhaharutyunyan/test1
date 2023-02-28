<?php

namespace Engine\Core\Config;

class Config
{
    /**
     * @param string $key
     * @param string $group
     * @return mixed
     * @throws \Exception
     */
    public static function item(string $key, string $group = 'main'): mixed
    {
        if (!Repository::retrieve($group, $key)) {
            self::file($group);
        }

        return Repository::retrieve($group, $key);
    }

    /**
     * Retrieves a group config items.
     *
     * @param string $group The item group.
     * @return mixed
     * @throws \Exception
     */
    public static function group(string $group): mixed
    {
        if (!Repository::retrieveGroup($group)) {
            self::file($group);
        }

        return Repository::retrieveGroup($group);
    }

    /**
     * @param string $group
     * @return bool
     * @throws \Exception
     */
    public static function file(string $group = 'main'): bool
    {
        $path = path('config') . DS . $group . '.php';

        if ($group === 'messages') {
            $path = path('messages') . DS . $group . '_' . $_ENV['language'] . '.php';
        }

        if (file_exists($path)) {
            $items = include $path;

            if (is_array($items)) {
                foreach ($items as $key => $value) {
                    Repository::store($group, $key, $value);
                }

                return true;
            } else {
                throw new \Exception(
                    sprintf(
                        'Config file <strong>%s</strong> is not a valid array.',
                        $path
                    )
                );
            }
        } else {
            throw new \Exception(
                sprintf(
                    'Cannot load config from file, file <strong>%s</strong> does not exist.',
                    $path
                )
            );
        }

        return false;
    }

}
