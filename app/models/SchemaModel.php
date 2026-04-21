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
        $foreignKeys = $this->foreignKeyMap($table);

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
                'reference' => $foreignKeys[$field] ?? null,
                'options' => isset($foreignKeys[$field]) ? $this->foreignOptions($foreignKeys[$field]) : [],
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

    private function foreignKeyMap(string $table): array
    {
        $pdo = db_connect();
        $driver = $this->driver();

        if ($driver === 'pgsql') {
            $stmt = $pdo->prepare(
                "SELECT
                    kcu.column_name AS source_column,
                    ccu.table_name AS target_table,
                    ccu.column_name AS target_column
                 FROM information_schema.table_constraints tc
                 JOIN information_schema.key_column_usage kcu
                   ON tc.constraint_name = kcu.constraint_name
                  AND tc.table_schema = kcu.table_schema
                 JOIN information_schema.constraint_column_usage ccu
                   ON ccu.constraint_name = tc.constraint_name
                  AND ccu.table_schema = tc.table_schema
                 WHERE tc.constraint_type = 'FOREIGN KEY'
                   AND tc.table_schema = 'public'
                   AND tc.table_name = :table_name"
            );
            $stmt->bindValue(':table_name', $table);
            $stmt->execute();
            $rows = $stmt->fetchAll();
        } else {
            $stmt = $pdo->prepare(
                "SELECT
                    COLUMN_NAME AS source_column,
                    REFERENCED_TABLE_NAME AS target_table,
                    REFERENCED_COLUMN_NAME AS target_column
                 FROM information_schema.KEY_COLUMN_USAGE
                 WHERE TABLE_SCHEMA = DATABASE()
                   AND TABLE_NAME = :table_name
                   AND REFERENCED_TABLE_NAME IS NOT NULL"
            );
            $stmt->bindValue(':table_name', $table);
            $stmt->execute();
            $rows = $stmt->fetchAll();
        }

        $map = [];
        foreach ($rows as $row) {
            $source = (string) ($row['source_column'] ?? '');
            $targetTable = (string) ($row['target_table'] ?? '');
            $targetColumn = (string) ($row['target_column'] ?? '');

            if ($source === '' || $targetTable === '' || $targetColumn === '') {
                continue;
            }

            $map[$source] = [
                'table' => $this->sanitizeIdentifier($targetTable),
                'column' => $this->sanitizeIdentifier($targetColumn),
            ];
        }

        return $map;
    }

    private function foreignOptions(array $reference): array
    {
        $pdo = db_connect();
        $table = $this->sanitizeIdentifier((string) ($reference['table'] ?? ''));
        $idColumn = $this->sanitizeIdentifier((string) ($reference['column'] ?? 'id'));

        if ($table === '' || $idColumn === '') {
            return [];
        }

        $labelColumn = $this->resolveLabelColumn($table, $idColumn);
        $quotedTable = $this->quoteIdentifier($table);
        $quotedId = $this->quoteIdentifier($idColumn);
        $quotedLabel = $this->quoteIdentifier($labelColumn);

        $sql = 'SELECT ' . $quotedId . ' AS value, ' . $quotedLabel . ' AS label FROM ' . $quotedTable . ' ORDER BY ' . $quotedLabel . ' ASC LIMIT 500';
        $rows = $pdo->query($sql)->fetchAll();

        $options = [];
        foreach ($rows as $row) {
            $options[] = [
                'value' => (string) ($row['value'] ?? ''),
                'label' => (string) ($row['label'] ?? ''),
            ];
        }

        return $options;
    }

    private function resolveLabelColumn(string $table, string $idColumn): string
    {
        $pdo = db_connect();
        $quotedTable = $this->quoteIdentifier($table);
        $driver = $this->driver();
        $columns = [];

        if ($driver === 'pgsql') {
            $stmt = $pdo->prepare(
                "SELECT column_name
                 FROM information_schema.columns
                 WHERE table_schema = 'public'
                   AND table_name = :table_name"
            );
            $stmt->bindValue(':table_name', $table);
            $stmt->execute();
            $rows = $stmt->fetchAll();
            foreach ($rows as $row) {
                $columns[] = (string) ($row['column_name'] ?? '');
            }
        } else {
            $rows = $pdo->query('DESCRIBE ' . $quotedTable)->fetchAll();
            foreach ($rows as $row) {
                $columns[] = (string) ($row['Field'] ?? '');
            }
        }

        $preferred = ['nombre', 'name', 'descripcion', 'description', 'titulo', 'title', 'email'];
        foreach ($preferred as $candidate) {
            if (in_array($candidate, $columns, true)) {
                return $candidate;
            }
        }

        foreach ($columns as $column) {
            if ($column !== $idColumn && !str_ends_with($column, '_id') && stripos($column, 'id_') !== 0) {
                return $column;
            }
        }

        return $idColumn;
    }
}
