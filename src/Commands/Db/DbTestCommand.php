<?php

namespace Kiwilan\Steward\Commands\Db;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Kiwilan\Steward\Commands\Commandable;

class DbTestCommand extends Commandable
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:test
                            {--c|credentials : Show database credentials}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test database connection';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->title();

        $credentials = $this->optionBool('credentials');
        $success = false;

        $this->info('Testing database connection...');
        $this->newLine();

        $connection = Schema::getConnection();

        $driver = $connection->getDriverName();
        $database = $connection->getDatabaseName();
        $host = $connection->getConfig('host');
        $port = $connection->getConfig('port');
        $username = $connection->getConfig('username');
        $password = $connection->getConfig('password');

        $this->comment("Driver: {$driver}");
        $this->comment("Database: {$database}");
        $this->comment("Host: {$host}");
        $this->comment("Port: {$port}");

        if ($credentials) {
            $this->comment("Username: {$username}");
            $this->comment("Password: {$password}");
        }

        $this->newLine();

        $this->comment("Try to ping database at {$host}:{$port}...");
        $available = $this->pingDatabase($host, $port);
        $this->info($available ? 'Database is available.' : 'Database is not available.');

        $this->newLine();

        if ($available) {
            $success = $this->testConnection($connection);
        } else {
            $this->comment('Try to get error information...');
            $this->getError($connection);

            $success = false;
        }

        if (! $success) {
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function pingDatabase(string $url, int|string $port): bool
    {
        $connection = @fsockopen($url, $port, $errno, $errstr, 1);
        if (is_resource($connection)) {
            fclose($connection);

            return true;
        }

        return false;
    }

    private function getError(\Illuminate\Database\Connection $connection): void
    {
        try {
            $connection->getPdo();
        } catch (\Throwable $th) {
            $this->error($th->getMessage());
        }
    }

    private function testConnection(\Illuminate\Database\Connection $connection): bool
    {
        $driverName = null;
        $serverInfo = null;
        $clientVersion = null;
        $serverVersion = null;
        $connectionStatus = null;

        try {
            $pdo = $connection->getPdo();

            $driverName = $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME);
            $serverInfo = $pdo->getAttribute(\PDO::ATTR_SERVER_INFO);
            $clientVersion = $pdo->getAttribute(\PDO::ATTR_CLIENT_VERSION);
            $serverVersion = $pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);
            $connectionStatus = $pdo->getAttribute(\PDO::ATTR_CONNECTION_STATUS);
        } catch (\Exception $e) {
            $this->error('Connection failed.');

            return false;
        }

        $this->alert('Connection successful.');
        $this->comment("Driver name: {$driverName}");
        $this->comment("Server info: {$serverInfo}");
        $this->comment("Client version: {$clientVersion}");
        $this->comment("Server version: {$serverVersion}");
        $this->comment("Connection status: {$connectionStatus}");

        return true;
    }
}
