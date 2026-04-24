<?php
declare(strict_types=1);

function getConnection(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = getenv('DB_HOST') ?: '127.0.0.1';
    $port = getenv('DB_PORT') ?: '3306';
    $database = getenv('DB_NAME') ?: 'aluguel_veiculos';
    $user = getenv('DB_USER') ?: 'root';
    $password = getenv('DB_PASS') ?: 'your_password';
    $autoSeed = readBooleanEnv('DB_AUTO_SEED', true);

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTIN,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $serverDsn = sprintf('mysql:host=%s;port=%s;charset=utf8mb4', $host, $port);

    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $database);

    try {
        $serverPdo = new PDO($serverDsn, $user, $password, $options);
        ensureDatabaseExists($serverPdo, $database);

        $pdo = new PDO($dsn, $user, $password, $options);
        bootstrapDatabaseIfNeeded($pdo, $autoSeed);
    } catch (PDOException $exception) {
        throw new PDOException(
            'Falha na conexao com o banco de dados. Verifique as credenciais e se o MySQL esta em execucao.',
            (int) $exception->getCode(),
            $exception
        );
    }

    return $pdo;
}

function readBooleanEnv(string $name, bool $default): bool
{
    $value = getenv($name);

    if ($value === false || $value === '') {
        return $default;
    }

    $parsedValue = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    return $parsedValue ?? $default;
}

function ensureDatabaseExists(PDO $pdo, string $database): void
{
    $safeDatabase = str_replace('`', '``', $database);
    $pdo->exec(
        sprintf(
            'CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci',
            $safeDatabase
        )
    );
}

function bootstrapDatabaseIfNeeded(PDO $pdo, bool $autoSeed): void
{
    $existingTables = countExistingCoreTables($pdo);

    if ($existingTables === 0) {
        executeSqlFile($pdo, dirname(__DIR__) . '/database/schema.sql');
        if ($autoSeed) {
            executeSqlFile($pdo, dirname(__DIR__) . '/database/seed.sql');
        }
        return;
    }

    if ($existingTables === 3 && $autoSeed && areCoreTablesEmpty($pdo)) {
        executeSqlFile($pdo, dirname(__DIR__) . '/database/seed.sql');
        return;
    }

    if ($existingTables > 0 && $existingTables < 3) {
        throw new PDOException('Estrutura de banco parcial detectada. Ajuste as tabelas manualmente antes de continuar.');
    }
}

function countExistingCoreTables(PDO $pdo): int
{
    return (int) $pdo->query(
        "SELECT COUNT(*)
        FROM information_schema.tables
        WHERE table_schema = DATABASE()
          AND table_name IN ('usuarios', 'veiculos', 'alugueis')"
    )->fetchColumn();
}

function areCoreTablesEmpty(PDO $pdo): bool
{
    return (int) $pdo->query('SELECT COUNT(*) FROM usuarios')->fetchColumn() === 0
        && (int) $pdo->query('SELECT COUNT(*) FROM veiculos')->fetchColumn() === 0
        && (int) $pdo->query('SELECT COUNT(*) FROM alugueis')->fetchColumn() === 0;
}

function executeSqlFile(PDO $pdo, string $filePath): void
{
    $sqlScript = file_get_contents($filePath);

    if ($sqlScript === false) {
        throw new PDOException('Nao foi possivel ler o arquivo SQL de inicializacao.');
    }

    $statements = preg_split('/;\s*(?:\r?\n|$)/', $sqlScript);

    if ($statements === false) {
        throw new PDOException('Nao foi possivel processar o script SQL de inicializacao.');
    }

    foreach ($statements as $statement) {
        $trimmed = trim($statement);

        if ($trimmed === '' || str_starts_with($trimmed, '--') || str_starts_with($trimmed, '#')) {
            continue;
        }

        if (preg_match('/^CREATE\s+DATABASE\b/i', $trimmed) === 1 || preg_match('/^USE\s+/i', $trimmed) === 1) {
            continue;
        }

        $pdo->exec($trimmed);
    }
}
