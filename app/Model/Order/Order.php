<?php

namespace App\Model\Order;

use Engine\Core\Database\ActiveRecord;


class Order
{
    use ActiveRecord;
    protected string $table="orders";
    protected int $id;

    /**
     * тут должны быть все свойства со свити геттерамии сеттерами.
     * они должны быть private как у User
     */

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }


}