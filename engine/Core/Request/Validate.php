<?php

namespace Engine\Core\Request;

use Engine\Core\Config\Config;

trait Validate
{
    /**
     * @param mixed $param
     * @param string $key
     * @return bool|string
     * @throws \Exception
     */
    protected function required(mixed $param, string $key): bool|string
    {

        if (!empty($param)) {
            return true;
        }
        return sprintf(Config::item('required', 'messages'), $key);
    }

    /**
     * @param mixed $param
     * @param string $key
     * @return bool|string
     * @throws \Exception
     */
    protected function int(mixed $param, string $key): bool|string
    {
        if (is_numeric($param)) {
            return true;
        }
        return sprintf(Config::item('int', 'messages'), $key);
    }

    /**
     * @return bool
     */
    protected function nullable(): bool
    {
        return true;
    }


    /**
     * @param mixed $param
     * @param string $key
     * @return bool|string
     * @throws \Exception
     */
    protected function array(mixed $param, string $key): bool|string
    {
        if (is_array($param)) {
            return true;
        }
        return sprintf(Config::item('array', 'messages'), $key);
    }

    /**
     * @param mixed $param
     * @param string $pattern
     * @param string $key
     * @return bool|string
     * @throws \Exception
     */
    protected function regex(mixed $param, string $pattern, string $key): bool|string
    {
        if (preg_match($pattern, $param)) {
            return true;
        }
        return sprintf(Config::item('regex', 'messages'), $key);
    }

    /**
     * @param mixed $param
     * @param string $key
     * @return bool|string
     * @throws \Exception
     */
    protected function date(mixed $param, string $key): bool|string
    {

        if (preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}/u', $param)) {
            $date = explode('-', $param);
        }

        if (preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}/u', $param)) {
            $date = array_reverse(explode('.', $param));
        }

        if (checkdate($date[1], $date[2], $date[0])) {
            return true;
        }
        return sprintf(Config::item('date', 'messages'), $key);
    }

    /**
     * @param mixed $param
     * @param string $key
     * @return bool|string
     * @throws \Exception
     */
    protected function string(mixed $param, string $key): bool|string
    {
        if (is_string($param)) {
            return true;
        }
        return sprintf(Config::item('string', 'messages'), $key);
    }

    /**
     * @param mixed $param
     * @param string $key
     * @return bool|string
     * @throws \Exception
     */
    protected function email(mixed $param, string $key): bool|string
    {
        if (preg_match('/^[a-zA-Z0-9_\-.]+@[a-zA-Z0-9_]+\.[a-zA-Z]+/', $param)) {
            return true;
        }
        return sprintf(Config::item('email', 'messages'), $key);
    }

    /**
     * @return bool
     */
    protected function confirmation(): bool
    {
        return true;
    }

    /**
     * @param mixed $param
     * @param string $key
     * @return bool|string
     * @throws \Exception
     */
    protected function bool(mixed $param, string $key): bool|string
    {
        if ($param === true || $param === false || $param === 'true' || $param === 'false') {
            return true;
        }
        return sprintf(Config::item('bool', 'messages'), $key);
    }

    /**
     * @param mixed $param
     * @param string $table
     * @param string $key
     * @return bool|string
     * @throws \Exception
     */
    protected function unique(mixed $param, string $table, string $key): bool|string
    {
        $query = $this->queryBuilder
            ->select()
            ->from($table)
            ->where($key, $param)
            ->sql();

        $data = $this->db->set($query, $this->queryBuilder->values);

        if (!empty($data)) {
            return sprintf(Config::item('unique', 'messages'), $key);
        } else {
            return true;
        }
    }

    /**
     * @param mixed $param
     * @param mixed $length
     * @param string $key
     * @return bool|string
     * @throws \Exception
     */
    protected function max(mixed $param, mixed $length, string $key): bool|string
    {
        if (strlen($param) <= (int)$length) {
            return true;
        }
        return sprintf(Config::item('max', 'messages'), $key, $length);
    }

    /**
     * @param mixed $param
     * @param mixed $length
     * @param string $key
     * @return bool|string
     * @throws \Exception
     */
    protected function min(mixed $param, mixed $length, string $key): bool|string
    {
        if (strlen($param) > (int)$length) {
            return true;
        }
        return sprintf(Config::item('min', 'messages'), $key, $length);
    }

    /**
     * @param mixed $param
     * @param string $key
     * @return bool|string
     * @throws \Exception
     */
    protected function file(mixed $param, string $key): bool|string
    {
        $flag = false;
        if (is_array($param)) {
            foreach ($param as $value) {
                if (is_array($value)) {
                    if (isset($value['tmp_name']) && is_file($value['tmp_name'])) {
                        $flag = true;
                    } else {
                        $flag = false;
                        break;
                    }
                } else {
                    if (isset($param['tmp_name']) && $param['tmp_name']) {
                        $flag = true;
                    }
                }
            }
        }

        return $flag ? true : sprintf(Config::item('file', 'messages'), $key);
    }

    /**
     * @param mixed $param
     * @param string $table
     * @param string $colum
     * @param string $key
     * @return bool|string
     * @throws \Exception
     */
    protected function exist(mixed $param, string $table, string $colum, string $key): bool|string
    {
        $query = $this->queryBuilder
            ->select()
            ->from($table)
            ->where($colum, $param)
            ->sql();

        $data = $this->db->set($query, $this->queryBuilder->values);

        if (!empty($data)) {
            return true;
        }
        return sprintf(Config::item('exist', 'messages'), $key, $table);
    }
}