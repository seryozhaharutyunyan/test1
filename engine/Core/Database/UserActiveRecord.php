<?php

namespace Engine\Core\Database;

use Engine\Core\Auth\Auth;

trait UserActiveRecord
{
    use ActiveRecord;
    /**
     * @param string $email
     * @param string $password
     * @return object|array|null
     */
    public function attempt(string $email, string $password): object|array|null
    {
        $find = $this->db->set(
            $this->queryBuilder
                ->select()
                ->from($this->getTable())
                ->where('email', $email,'=', 'AND')
                ->where('password', Auth::encryptPassword($password))
                ->sql(),
            $this->queryBuilder->values
        );

        return $find ?? null;
    }
}