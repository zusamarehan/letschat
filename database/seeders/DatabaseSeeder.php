<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use RehanSockets\Sockets;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // TODO: Temp Place, need to move to Command
        $wss = (new Sockets())
            ->initiate()
            ->open()
            ->message()
            ->close()
            ->disconnect();

        $wss->server->start();
    }
}
