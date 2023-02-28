<?php

namespace App\Model\User;

use Engine\Model;

class UserRepository extends Model
{
    public function getUsers(): array
    {
        $sql=$this->queryBuilder
            ->select()
            ->from('user')
            ->orderBy('id', 'DESC')
            ->sql();
        return $this->db->setAll($sql);
    }

    public function update(){
        $user=new User(1);
        $user->setEmail('admin@mail.ru');

        $user->save();
    }

}