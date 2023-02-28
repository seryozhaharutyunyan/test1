<?php

namespace Engine\Core\Migration;

trait MigrationBuilder
{
    protected function get(): void
    {
        $flag=false;
        if ($this->table !== 'migrations') {
            $sql = $this->queryBuilder->select()
                ->from('migrations')
                ->sql();
            $migrations = $this->db->setAll($sql);
            if (str_starts_with($this->create, 'DROP TABLE')
                || str_starts_with($this->create, 'DROP INDEX')
                || preg_match('/^ALTER TABLE [a-z_]+ DROP COLUMN/', $this->create)) {
                foreach ($migrations as $table) {
                    if ($this->migrationName === $table->name) {
                        $sql = $this->queryBuilder->delete()
                            ->from('migrations')
                            ->where('name', $this->migrationName)
                            ->sql();
                        $flag=true;
                    }
                }

            } else {
                foreach ($migrations as $table) {
                    if ($this->migrationName === $table->name) {
                        return;
                    }
                }

                $sql = $this->queryBuilder->insert('migrations')
                    ->set(['name' => $this->migrationName])
                    ->sql();

                $flag=true;
            }
        }

        if (\preg_match('/^CREATE\s+TABLE\s+/', $this->create)) {
            if (str_ends_with($this->create, ',')) {
                $this->create = \rtrim($this->create, ',') . '); ';
            }
        } else {
            $this->create = \rtrim($this->create, ',');
            $this->create .= ';';
        }

        if($flag || $this->table==='migrations'){
            $this->db->execute($this->create);
            if (isset($sql) && $this->table !== 'migrations') {
                $this->db->execute($sql, $this->queryBuilder->values);
            }
        }
    }


    /**
     * @return $this
     */
    protected function id(): static
    {
        $this->create .= "id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,";

        return $this;
    }

    /**
     * @param string $name
     * @param int $length
     * @return $this
     */
    protected function varchar(string $name, int $length = 255): static
    {
        $this->create .= " $name VARCHAR($length) NOT NULL,";

        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    protected function text(string $name): static
    {
        $this->create .= " $name TEXT NOT NULL,";

        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    protected function int(string $name): static
    {
        $this->create .= " $name INT NOT NULL,";

        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    protected function bigInt(string $name): static
    {
        $this->create .= " $name BIGINT NOT NULL,";

        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    protected function usignetBigInt(string $name): static
    {
        $this->create .= " $name BIGINT UNSIGNED NOT NULL,";

        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    protected function tinyInt(string $name): static
    {
        $this->create .= " $name TINYINT NOT NULL,";

        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    protected function smallInt(string $name): static
    {
        $this->create .= " $name SMALLINT NOT NULL,";

        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    protected function mediumInt(string $name): static
    {
        $this->create .= " $name MEDIUMINT NOT NULL,";

        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    protected function float(string $name): static
    {
        $this->create .= " $name FLOAT NOT NULL,";

        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    protected function double(string $name): static
    {
        $this->create .= " $name DOUBLE NOT NULL,";

        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    protected function longText(string $name): static
    {
        $this->create .= " $name LONGTEXT  NOT NULL,";

        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    protected function date(string $name): static
    {
        $this->create .= " $name DATE  NOT NULL,";

        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    protected function dateTime(string $name): static
    {
        $this->create .= " $name DATETIME  NOT NULL,";

        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    protected function time(string $name): static
    {
        $this->create .= " $name TIME  NOT NULL,";

        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    protected function timeStamp(string $name): static
    {
        $this->create .= " $name TIMESTAMP  NOT NULL,";

        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    protected function json(string $name): static
    {
        $this->create .= " $name JSON  NOT NULL,";

        return $this;
    }

    /**
     * @param string $name
     * @param array $values
     * @return $this
     */
    protected function enum(string $name, array $values): static
    {
        $strQuery = " $name ENUM (";
        foreach ($values as $item) {
            $strQuery .= "'$item',";
        }
        $strQuery = \rtrim($strQuery, ',');
        $strQuery .= ") NOT NULL,";
        $this->create .= $strQuery;

        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    protected function bool(string $name): static
    {
        $this->create .= " $name BOOL  NOT NULL,";

        return $this;
    }


    /**
     * @return $this
     */
    protected function nullable(): static
    {
        if (\preg_match('/\s+?(NOT\s+?NULL),$/', $this->create)) {
            $this->create = \preg_replace('/\s+?(NOT\s+?NULL),$/', ' NULL,', $this->create);
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function unique(): static
    {
        if (\preg_match('/\s+?(NOT\s+?NULL),$/', $this->create)) {
            $this->create = \rtrim($this->create, ',');
            $this->create .= " UNIQUE,";
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function createAt(): static
    {
        $this->create .= " create_at DATETIME DEFAULT CURRENT_TIMESTAMP,";

        return $this;

    }

    /**
     * @return $this
     */
    protected function updateAt(): static
    {
        $this->create .= " update_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";

        return $this;
    }

    /**
     * @param string|int $value
     * @return $this
     */
    protected function defaultValue(string|int $value): static
    {
        if (\preg_match('/\s+?(NOT\s+?NULL),$/', $this->create)) {
            $this->create = \preg_replace('/\s+?(NOT\s+?NULL),$/', " DEFAULT '$value',", $this->create);
        }

        return $this;
    }

    /**
     * @param string $table
     * @param string $key
     * @param string $foreingKey
     * @return $this
     */
    protected function foreingKey(string $table, string $key, string $foreingKey): static
    {
        $this->create .= " FOREIGN KEY ($foreingKey) REFERENCES $table($key),";

        return $this;
    }

    /**
     * @return $this
     */
    protected function dropTable(): static
    {
        $this->create = "DROP TABLE $this->table";

        return $this;
    }

    /**
     * @return $this
     */
    protected function addColumn(): static
    {
        $this->create = "ALTER TABLE $this->table ADD ";

        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    protected function dropColumn(string $name): static
    {
        $this->create = "ALTER TABLE $this->table DROP COLUMN $name";

        return $this;
    }

    /**
     * @param string $column
     * @return $this
     */
    protected function index(string $column): static
    {
        $name = "idx_$this->table";
        $name .= "_$column";
        if (str_ends_with($this->create, ',')) {
            $this->create = \rtrim($this->create, ',') . '); ';
        }
        $this->create .= "CREATE INDEX $name ON $this->table (";
        $this->create .= "$column); ";

        return $this;
    }

    /**
     * @param string $name name idx_table_column name
     * @return $this
     */
    protected function dropIndex(string $name): static
    {
        $this->create = "DROP INDEX {$name} ON {$this->table}";

        return $this;
    }
}