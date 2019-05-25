<?php
namespace Tabula\Database\Adapter;

interface AbstractAdapter {
    public function __construct(string $host, string $database, string $user, string $password, string $port, string $charset);
    public function query();
    public function escape($value);
    public function close();
}