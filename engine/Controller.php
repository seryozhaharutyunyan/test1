<?php

namespace Engine;

use App\Model\User\User;
use Engine\Core\Auth\Auth;
use Engine\Core\Config\Config;
use Engine\Core\Database\Connection;
use Engine\Core\Database\QueryBuilder;
use Engine\Core\Mail\Mail;
use Engine\Core\Response\Response;
use Engine\Core\Template\View;
use Engine\DI\DI;

abstract class Controller
{
    protected DI $di;
    protected mixed $get = [];
    protected Connection $db;
    protected array $config;
    protected QueryBuilder $query;
    protected Load $load;
    protected Response $response;

    /**
     * @param DI $di
     */
    public function __construct(DI $di)
    {
        $this->di = $di;
        $this->db = Connection::getInstance();
        $this->config = $this->di->get('config');
        $this->load = $this->di->get('load');
        $this->response = $this->di->get('response');

        $this->initVars();

        $this->query = new QueryBuilder();

        $this->receivingLanguage();
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->di->get($key);
    }

    /**
     * @return mixed
     */
    public function getGetParams(): mixed
    {
        return $this->get;
    }

    /**
     * @param mixed $get
     */
    public function setGetParams(mixed $get): void
    {
        $this->get = $get;
    }

    /**
     * @param $key
     * @return mixed|void
     */
    protected function rout($key)
    {
        $routers = $this->di->get('router')->get_routes();

        foreach ($routers as $k => $router) {
            if ($key === $k) {
                return $router['pattern'];
            }
        }
    }

    /**
     * @return $this
     */
    public function initVars(): static
    {
        $vars = array_keys(get_object_vars($this));

        foreach ($vars as $var) {
            if ($this->di->has($var)) {
                $this->{$var} = $this->di->get($var);
            }
        }

        return $this;
    }

    /**
     * @param User $user
     * @param string $token
     * @return string|null
     * @throws \Exception
     */
    protected function updateToken(User $user, string $token): string|null
    {

        if ($user->getToken() === $token) {
            $tokenNew = Auth::createToken();
            if ($tokenNew) {
                $user->setToken($tokenNew);
                $user->save();
                Auth::unAuthorize('session');
                Auth::authorize($tokenNew, 'session');
            }

            return $tokenNew;
        }

        return null;
    }

    private function receivingLanguage(): void
    {
        $sql = 'SHOW TABLES';
        $tables = $this->db->setAll($sql, [], \PDO::FETCH_ASSOC);

        foreach ($tables as $table) {
            if ($table["Tables_in_" . Config::item('db_name', 'database')] === 'settings') {
                $sql = $this->query->select()
                    ->from('settings')
                    ->sql();
                $settings = $this->db->set($sql);
            }
        }

        if (isset($settings) && isset($settings->language)) {
            $_ENV['language'] = $settings->language;
        }
    }
}