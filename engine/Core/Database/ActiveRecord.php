<?php

namespace Engine\Core\Database;

use Engine\DI\DI;
use \ReflectionClass;
use \ReflectionProperty;

trait ActiveRecord
{
    protected Connection $db;
    protected QueryBuilder $queryBuilder;
    protected object|array $data;

    /**
     * ActiveRecord constructor.
     * @param int $id
     */
    public function __construct(int $id = 0)
    {
        $this->db = Connection::getInstance();
        $this->queryBuilder = new QueryBuilder();

        if ($id) {
            $this->setId($id);
            $this->getData();
        }
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @return void
     */
    protected function getData(): void
    {
        $data = $this->findOne();
        $properties = $this->getProperties();

        foreach ($properties as $value) {
            foreach ($data as $key => $item) {
                if ($value->getName() === $key) {
                    if($i=strpos($key, '_')){
                        $key=substr($key,0, $i).ucfirst(substr($key,$i+1));
                    }
                    $this->{"set".ucfirst($key)}($item);
                }
            }
        }
    }

    /**
     * @return object|null
     */
    protected function findOne(): ?object
    {
        $find = $this->db->set(
            $this->queryBuilder
                ->select()
                ->from($this->getTable())
                ->where('id', $this->id)
                ->sql(),
            $this->queryBuilder->values
        );

        return $find ?? null;
    }

    /**
     *  Save UserActiveRecord
     */
    public function save()
    {
        $properties = $this->getIssetProperties();

        try {
            if (isset($this->id)) {
                $this->db->execute(
                    $this->queryBuilder->update($this->getTable())
                        ->set($properties)
                        ->where('id', $this->id)
                        ->sql(),
                    $this->queryBuilder->values
                );
            } else {
                $this->db->execute(
                    $this->queryBuilder->insert($this->getTable())
                        ->set($properties)
                        ->sql(),
                    $this->queryBuilder->values
                );
            }

            return $this->db->lastInsertId();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @return array
     */
    private function getIssetProperties(): array
    {
        $properties = [];

        foreach ($this->getProperties() as $key => $property) {
            if (isset($this->{$property->getName()})) {
                $properties[$property->getName()] = $this->{$property->getName()};
            }
            if($this->{$property->getName()}===null){
                $properties[$property->getName()]=null;
            }


        }

        return $properties;
    }

    /**
     * @return ReflectionProperty[]
     */
    private function getProperties(): array
    {
        $reflection = new ReflectionClass($this);
        return $reflection->getProperties(ReflectionProperty::IS_PRIVATE);
    }
}