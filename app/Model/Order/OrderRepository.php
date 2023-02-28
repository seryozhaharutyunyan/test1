<?php

namespace App\Model\User;

use App\Model\Order\Order;
use Engine\Model;

class OrderRepository extends Model
{
    public function getOrders(): array
    {
        $sql=$this->queryBuilder
            ->select()
            ->from('user')
            ->orderBy('id', 'DESC')
            ->sql();
        return $this->db->setAll($sql);
    }

    public function update(Order $order){

        $order->setEmail('admin@mail.ru');

        $order->save();
    }

}