<?php

namespace App\Controller;

use App\Model\Order\Order;
use App\Request\Order\StoreRequest;
use App\Request\Order\UpdateRequest;

class OrderController extends Controller
{
    /**
     * show all orders
     */
    public function index()
    {
    }

    /**
     * add order
     * @param StoreRequest $request
     */
    public function store(StoreRequest $request)
    {
    }

    /**
     * update order
     * @param UpdateRequest $request
     */
    public function update(UpdateRequest $request)
    {
    }

    /**
     * delete order
     * @param Order $order
     */
    public function delete(Order $order)
    {
    }

    /**
     * set all orders
     */
    public function set()
    {
    }

    /**
     * @param Order $order
     * set one order
     */
    public function setOne(Order $order)
    {
    }

}