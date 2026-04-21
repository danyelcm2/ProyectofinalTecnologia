<?php

declare(strict_types=1);

class SchemaModel
{
    public function tables(): array
    {
        $pdo = db_connect();
        $driver = $this->driver();

        if ($driver === 'pgsql') {
            $rows = $pdo->query("SELECT tablename FROM pg_catalog.pg_tables WHERE schemaname = 'public' ORDER BY tablename")->fetchAll(PDO::FETCH_NUM);
        } else {
            $rows = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_NUM);
        }

        return array_map(static fn(array $row): string => (string) $row[0], $rows);
    }

    public function columns(string $table): array
    {
        $table = $this->sanitizeIdentifier($table);
        $pdo = db_connect();
        $driver = $this->driver();

        if ($driver === 'pgsql') {
            $stmt = $pdo->prepare(
                "SELECT
                    c.column_name AS field,
                    c.data_type AS type,
                    CASE WHEN c.is_nullable = 'YES' THEN true ELSE false END AS nullable,
                    CASE WHEN pk.column_name IS NOT NULL THEN 'PRI' ELSE '' END AS key_name,
                    c.column_default AS default_value,
                    CASE WHEN c.column_default LIKE 'nextval(%' THEN 'auto_increment' ELSE '' END AS extra
                 FROM information_schema.columns c
                 LEFT JOIN (
                    SELECT kcu.column_name
                    FROM information_schema.table_constraints tc
                    JOIN information_schema.key_column_usage kcu
                      ON tc.constraint_name = kcu.constraint_name
                     AND tc.table_schema = kcu.table_schema
                   WHERE tc.constraint_type = 'PRIMARY KEY'
                     AND tc.table_schema = 'public'
                     AND tc.table_name = :table_name
                 ) pk ON pk.column_name = c.column_name
                 WHERE c.table_schema = 'public'
                   AND c.table_name = :table_name
                 ORDER BY c.ordinal_position"
            );
            $stmt->bindValue(':table_name', $table);
            $stmt->execute();
            $rows = $stmt->fetchAll();
        } else {
            $stmt = $pdo->query('DESCRIBE ' . $this->quoteIdentifier($table));
            $rows = $stmt->fetchAll();
        }

        $columns = [];
        foreach ($rows as $row) {
            $field = (string) ($row['Field'] ?? $row['field'] ?? '');
            $type = (string) ($row['Type'] ?? $row['type'] ?? '');
            $nullRaw = $row['Null'] ?? $row['nullable'] ?? false;
            $isNullable = is_bool($nullRaw) ? $nullRaw : strtoupper((string) $nullRaw) === 'YES';
            $key = (string) ($row['Key'] ?? $row['key'] ?? $row['key_name'] ?? '');
            $default = $row['Default'] ?? $row['default'] ?? $row['default_value'] ?? null;
            $extra = (string) ($row['Extra'] ?? $row['extra'] ?? '');

            $columns[] = [
                'field' => $field,
                'type' => $type,
                'nullable' => $isNullable,
                'key' => $key,
                'default' => $default,
                'extra' => $extra,
            ];
        }

        return $columns;
    }

    public function insert(string $table, array $payload): void
    {
        $table = $this->sanitizeIdentifier($table);
        $columns = $this->columns($table);

        $allowed = [];
        foreach ($columns as $column) {
            $isAutoIncrement = stripos($column['extra'], 'auto_increment') !== false;
            if ($isAutoIncrement) {
                continue;
            }
            $allowed[] = $column['field'];
        }

        $filtered = [];
        foreach ($allowed as $field) {
            if (array_key_exists($field, $payload) && $payload[$field] !== '') {
                $filtered[$field] = $payload[$field];
            }
        }

        if ($filtered === []) {
            throw new InvalidArgumentException('No se recibieron campos validos para insertar.');
        }

        $quotedFields = array_map(fn(string $field): string => $this->quoteIdentifier($field), array_keys($filtered));
        $fieldList = implode(', ', $quotedFields);
        $placeholders = ':' . implode(', :', array_keys($filtered));

        $sql = 'INSERT INTO ' . $this->quoteIdentifier($table) . ' (' . $fieldList . ') VALUES (' . $placeholders . ')';
        $pdo = db_connect();
        $stmt = $pdo->prepare($sql);

        foreach ($filtered as $field => $value) {
            $stmt->bindValue(':' . $field, $value);
        }

        $stmt->execute();
    }

    public function sanitizeIdentifier(string $identifier): string
    {
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $identifier)) {
            throw new InvalidArgumentException('Identificador invalido.');
        }

        return $identifier;
    }

    private function quoteIdentifier(string $identifier): string
    {
        $safe = $this->sanitizeIdentifier($identifier);
        if ($this->driver() === 'pgsql') {
            return '"' . $safe . '"';
        }

        return '`' . $safe . '`';
    }

    private function driver(): string
    {
        return (string) (db_selected_meta()['driver'] ?? 'mysql');
    }
}
