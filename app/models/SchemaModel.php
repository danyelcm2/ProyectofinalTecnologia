<?php

declare(strict_types=1);

class SchemaModel
{
    public function tables(): array
    {
        $pdo = db_connect();
        $rows = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_NUM);

        return array_map(static fn(array $row): string => (string) $row[0], $rows);
    }

    public function columns(string $table): array
    {
        $table = $this->sanitizeIdentifier($table);
        $pdo = db_connect();
        $stmt = $pdo->query('DESCRIBE `' . $table . '`');
        $rows = $stmt->fetchAll();

        $columns = [];
        foreach ($rows as $row) {
            $columns[] = [
                'field' => $row['Field'],
                'type' => $row['Type'],
                'nullable' => $row['Null'] === 'YES',
                'key' => $row['Key'],
                'default' => $row['Default'],
                'extra' => $row['Extra'],
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

        $fieldList = '`' . implode('`, `', array_keys($filtered)) . '`';
        $placeholders = ':' . implode(', :', array_keys($filtered));

        $sql = 'INSERT INTO `' . $table . '` (' . $fieldList . ') VALUES (' . $placeholders . ')';
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
}
