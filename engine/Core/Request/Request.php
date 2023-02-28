<?php

namespace Engine\Core\Request;

use Engine\Core\Config\Config;
use Engine\Core\Database\Connection;
use Engine\Core\Database\QueryBuilder;
use Engine\Core\Response\Response;
use Engine\DI\DI;

abstract class Request
{
    use Validate;

    protected DI $di;
    protected array $get = [];
    protected array $post = [];
    protected array $patch = [];
    protected array $put = [];
    protected Connection $db;
    protected QueryBuilder $queryBuilder;
    protected Response $response;


    /**
     * Request Constructor
     */
    public function __construct(DI $di)
    {
        $this->di = $di;
        $this->response = $this->di->get('response');

        $this->queryBuilder = new QueryBuilder();
        $this->db = Connection::getInstance();
    }

    /**
     * @return array
     */
    protected function initPutPatch(): array
    {
        $data = $this->decode();
        return !is_null($data) ? $data : json_decode(file_get_contents('php://input'), true);
    }

    /**
     * @return array
     */
    protected function initPost(): array
    {
        $data = $this->decode();
        $result = !empty($_POST) ? $_POST :
            (!is_null($data) ? $data : json_decode(file_get_contents('php://input'), true));

        return $this->initFiles($result);
    }

    /**
     * @param array $result
     * @return array
     */
    protected function initFiles(array $result): array
    {
        if (!empty($_FILES)) {
            foreach ($_FILES as $k => $value) {
                if (is_array($value['name'])) {
                    $file_count = count($value['name']);
                    $file_keys = array_keys($value);

                    for ($i = 0; $i < $file_count; $i++) {
                        foreach ($file_keys as $key) {
                            $result[$k][$i][$key] = $value[$key][$i];
                        }
                    }
                } else {
                    foreach ($value as $key => $item) {
                        $result[$k][$key] = $item;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * [
     *      "name"=>"required|unique|max:5"
     * ]
     * @return array
     */
    abstract protected function validated(): array;

    /**
     * @return array
     * @throws \Exception
     */
    public function validate(): array
    {

        $data = match ($_SERVER['REQUEST_METHOD']) {
            'POST' => $this->post = $this->initPost(),
            'PATCH' => $this->patch = $this->initPutPatch(),
            'PUT' => $this->put = $this->initPutPatch(),
        };

        $errors = [];
        $validate = $this->validated();
        if (!empty($validate)) {
            foreach ($validate as $key => $value) {
                $validate[$key] = explode('|', $value);
            }
        } else {
            throw new \Exception("you didn't specify a validation condition");
        }

        if (!empty($data)) {
            foreach ($data as $key => $value) {
                foreach ($validate as $k => $item) {
                    if ($key === $k) {
                        foreach ($item as $v) {
                            if (preg_match('/.:./', $v)) {
                                $s = explode(':', $v);
                                if (preg_match('/.,./', $s[1])) {
                                    $s[1] = explode(',', $s[1]);
                                    $message = $this->{$s[0]}($value, $s[1][0], $s[1][1], $key);
                                } else {
                                    $message = $this->{$s[0]}($value, $s[1], $key);
                                }
                            } else {
                                if ($v === 'nullable' && empty($value)) {
                                    continue 2;
                                }

                                if($v==='confirmation' && $value !==$data["confirmation_$key"]){
                                    $errors[$key]=sprintf(Config::item('confirmation', 'messages'), $key);
                                    continue 2;
                                }

                                $message = $this->{$v}($value, $key);
                            }
                            if ($message !== true) {
                                $errors[$key] = $message;
                                continue 2;
                            }
                        }
                    }
                }
            }
        } else {
            $this->response->send(415, Config::item('data', 'messages'));
        }

        if (!empty($errors)) {
            $this->response->setData($errors)->send(415);
        }

        return $data;
    }

    /**
     * @return array|null
     */
    protected function decode(): ?array
    {
        $raw_data = file_get_contents('php://input');
        $boundary = substr($raw_data, 0, strpos($raw_data, "\r\n"));

        if (empty($boundary)) {
            return null;
        }

        $parts = array_slice(explode($boundary, $raw_data), 1);
        $data = [];

        foreach ($parts as $part) {
            if ($part == "--\r\n") {
                break;
            }

            $part = ltrim($part, "\r\n");
            [$raw_headers, $body] = explode("\r\n\r\n", $part, 2);

            $raw_headers = explode("\r\n", $raw_headers);
            $headers = [];

            foreach ($raw_headers as $header) {
                [$name, $value] = explode(':', $header);
                $headers[strtolower($name)] = ltrim($value, ' ');
            }

            if (isset($headers['content-disposition'])) {
                $filename = null;
                $tmp_name = null;
                preg_match(
                    '/^(.+); *name="([^"]+)"(; *filename="([^"]+)")?/',
                    $headers['content-disposition'],
                    $matches
                );
                [, , $name] = $matches;

                if (isset($matches[4])) {
                    $filename = $matches[4];
                    $tmp_name = ROOT_DIR . "\\src\\temp\\" . rand(1000000, 9999999) . substr($filename, 0, strrpos($filename, '.')) . ".tmp";
                }
                if (isset($tmp_name) && !empty($tmp_name)) {
                    file_put_contents($tmp_name, $body);
                    if (preg_match('/^(.+)\[(.+)\]/', $name, $m)) {
                        [, $n, $i] = $m;
                        $data[$n][(int)$i] = [
                            'name' => $filename,
                            'full_path' => $filename,
                            'error' => 0,
                            'tmp_name' => $tmp_name,
                            'size' => filesize($tmp_name),
                            'type' => $headers['content-type']
                        ];
                    } else {
                        $data[$name] = [
                            'name' => $filename,
                            'full_path' => $filename,
                            'error' => 0,
                            'tmp_name' => $tmp_name,
                            'size' => filesize($tmp_name),
                            'type' => $headers['content-type']
                        ];
                    }
                } else {
                    $data[$name] = substr($body, 0, strlen($body) - 2);
                }
            }
        }

        return $data;
    }
}