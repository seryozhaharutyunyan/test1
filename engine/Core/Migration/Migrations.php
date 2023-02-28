<?php

namespace Engine\Core\Migration;

class Migrations extends Migration
{
    protected string $table = 'migrations';

    public function start()
    {
        $this->id()
            ->varchar('name', 255)->unique()
            ->createAt()
            ->get();
    }

    public function rollback()
    {
        $this->dropTable()->get();
    }
}