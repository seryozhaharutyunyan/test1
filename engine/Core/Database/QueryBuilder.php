<?php

namespace Engine\Core\Database;

class QueryBuilder
{
    protected array $sql = [];
    public array $values = [];
    protected string $table='';

    /**
     * @param string $fields
     * @return $this
     */
    public function select(string $fields = '*'): static
    {
        $this->reset();
        $this->sql['select'] = "SELECT {$fields} ";

        return $this;
    }

    public function delete(): static
    {
        $this->reset();
        $this->sql['delete'] = "DELETE ";

        return $this;
    }

    /**
     * @param string $table
     * @return $this
     */
    public function from(string $table): static
    {
        $this->sql['from'] = " FROM {$table} ";
        $this->table=$table;

        return $this;
    }

    /**
     * @param string $column
     * @param string $value
     * @param string $operator
     * @return $this
     */
    public function where(string $column, string $value, string $operator = '=', string $order = ''): static
    {
        if (isset($this->sql['where'])) {
            $i = count($this->sql['where']);
        } else {
            $i = 0;
        }

        $this->sql['where'][$i]['string'] = "{$column} {$operator} ?";
        if(!empty($this->table)){
            $this->sql['where'][$i]['string'] = "{$this->table}.{$column} {$operator} ?";
        }

        if ($i !== 0) {
            if ($order === '') {
                $order = 'and';
            }
            $this->sql['where'][$i]['order'] = strtoupper($order);
        }
        $this->values[] = $value;
        return $this;
    }

    /**
     * @param string $field
     * @param string $order
     * @return $this
     */
    public function orderBy(string $field, string $order = 'ASC'): static
    {
        $this->sql['order_by'] = " ORDER BY {$field} {$order}";
        return $this;
    }

    /**
     * @param string|integer $number
     * @return $this
     */
    public function limit(string|int $number): static
    {
        $this->sql['limit'] = " LIMIT {$number}";
        return $this;
    }

    /**
     * @param string $table
     * @return $this
     */
    public function update(string $table): static
    {
        $this->reset();
        $this->sql['update'] = "UPDATE {$table} ";
        return $this;
    }

    /**
     * @param string $table
     * @return $this
     */
    public function insert(string $table): static
    {
        $this->reset();
        $this->sql['insert'] = "INSERT INTO {$table} ";

        return $this;
    }

    public function join(string $table, string $ownerKey, string $foreignKey, string $order='LEFT'): static
    {
        $this->sql['join']=" {$order} JOIN {$table} ON {$this->table}.{$foreignKey} = {$table}.{$ownerKey} ";

        return $this;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function set(array $data = []): static
    {
        $this->sql['set'] = "SET ";
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $this->sql['set'] .= "{$key} = ?,";
                $this->values[] = $value;
            }
        }
        $this->sql['set']=rtrim($this->sql['set'], ',').' ';
        return $this;
    }

    /**
     * @return string
     */
    public function sql(): string
    {
        $sql = '';
        if (!empty($this->sql)) {
            foreach ($this->sql as $key => $value) {
                if ($key == 'where') {
                    $sql .= ' WHERE ';
                    foreach ($value as $where) {
                        $sql .= $where['string'];
                        if (count($value) > 1 && next($value)) {
                            $i = key($value);
                            $sql .= " " . $value[$i]['order'] . " ";
                        }
                    }
                } else {
                    $sql .= $value;
                }
            }
        }
        return $sql;
    }

    /**
     * Reset Builder
     */
    public function reset(): void
    {
        $this->sql = [];
        $this->values = [];
        $this->table='';
    }
}