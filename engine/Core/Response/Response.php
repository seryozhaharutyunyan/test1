<?php

namespace Engine\Core\Response;

use Engine\Core\Auth\Auth;
use Engine\Core\Config\Config;
use Engine\Core\Router\Router;
use Engine\DI\DI;
use Engine\Helper\Request;
use Engine\Helper\RequestHelper;

class Response
{
    protected array $headers = [
        'Content-Type' => 'application/json',
        'Pragma' => 'no-cache',
        'Cache-Control'=>'no-store, no-cache, must-revalidate'
    ];
    protected mixed $data = [];
    protected Router $router;
    protected DI $di;

    /**
     * @throws \Exception
     */
    public function __construct(DI $di)
    {
        $this->di = $di;
        $this->router = $this->di->get('router');
    }

    protected function getMethod(string $rout)
    {
        foreach ($this->router->get_routes() as $route) {
            if ($route['pattern'] === $rout) {
                return $route['method'];
            }
        }
    }

    public function setHeader($key, $value): void
    {
        $this->headers[$key] = $value;
    }


    /**
     * @param int $status
     * @param string $message
     * @param bool $RESPOND_WITH_REQUEST
     * @return void
     * @throws \Exception
     */
    public function send(int $status = 200, string $message = '', bool $RESPOND_WITH_REQUEST = false): void
    {
        ob_clean();
        ob_start();
        $rout = $_SERVER['REQUEST_URI'];
        $method = $this->getMethod($rout);

        $request = new \stdClass();

        if($status>=200 && $status<300){
            $request=$this->success($request, $message);
        }
        if($status>=400 && $status<500){
            $request=$this->error($request, $message);
        }

        if ($RESPOND_WITH_REQUEST) {
            $request->_Request = $GLOBALS['Request'];
        };

        http_response_code($status);

        $token=Auth::getToken();
        if($token){
            $this->setHeader("Authorization", "Bearer $token");
        }

        $this->setHeader("Access-Control-Allow-Methods", $method);
        $this->setHeader("Access-Control-Allow-Origin", Config::item('host', 'cors'));

        $this->headersInit();

        if (RequestHelper::is('ajax')) {
            echo json_encode($request);
        }

        die();
    }

    protected function success(\stdClass $request, $message): \stdClass
    {
        if (!empty($this->data)) {
            $request->data = $this->data;
        }

        if (!empty($message)) {
            $request->message = $message;
        }

        return $request;
    }

    protected function error(\stdClass $request, $message): \stdClass
    {
        if (!empty($this->data)) {
            $request->errors = $this->data;
        }

        if (!empty($message)) {
            $request->error = $message;
        }

        return $request;
    }

    protected function headersInit(): void
    {

        foreach ($this->headers as $key => $value) {
            header("$key: $value");
        };
    }


    public function setData(mixed $data): static
    {
        $this->data = $data;

        return $this;
    }
}