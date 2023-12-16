<?php

namespace RehanSockets;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use OpenSwoole\Server as ServerAlias;
use OpenSwoole\WebSocket\Server;
use OpenSwoole\WebSocket\Frame;
use OpenSwoole\Constant;
use OpenSwoole\Table;
use OpenSwoole\Http\Request;
use Faker\Factory;
class Sockets
{
    public $server;
    public $table;
    public $faker;
    public function __construct()
    {
        $this->server = new Server("127.0.0.1", 9501, ServerAlias::SIMPLE_MODE, Constant::SOCK_TCP);

        $this->faker = Factory::create();
        $this->table = new Table(1024);
        $this->table->column('fd', Table::TYPE_INT, 4);
        $this->table->column('name', Table::TYPE_STRING, 16);
        $this->table->create();
    }

    public function initiate(): Sockets
    {
        $this->server->on("Start", function (Server $server) {
            echo "Swoole WebSocket Server is started at " . $server->host . ":" . $server->port . "\n";
        });

        return $this;
    }

    public function open(): Sockets
    {
        $this->server->on('Open', function (Server $server, Request $request) {
            $fd = $request->fd;
            $clientName = $this->faker->firstName;
            $this->table->set($request->fd, [
                'fd' => $fd,
                'name' => sprintf($clientName)
            ]);

            $user = new User();
            $user->name = $clientName;
            $user->save();

            echo "Connection <{$fd}> open by {$clientName}. Total connections: " . $this->table->count() . "\n";
            foreach ($this->table as $key => $value) {
                if ($key == $fd) {
                    $this->server->push($request->fd, "Welcome {$clientName}, there are " . $this->table->count() . " connections");
                } else {
                    $this->server->push($key, "A new client ({$clientName}) is joining to the party");
                }
            }
        });

        return $this;
    }

    public function message(): Sockets
    {
        $this->server->on('Message', function (Server $server, Frame $frame) {
            $sender = $this->table->get(strval($frame->fd), "name");
            echo "Received from " . $sender . ", message: {$frame->data}" . PHP_EOL;
            foreach ($this->table as $key => $value) {
                if ($key == $frame->fd) {
                    $this->server->push($frame->fd, "Message sent");
                } else {
                    $this->server->push($key, "FROM: {$sender} - MESSAGE: " . $frame->data);
                }
            }
        });

        return $this;
    }

    public function close(): Sockets
    {
        $this->server->on('Close', function (Server $server, int $fd) {
            $this->table->del($fd);
            $this->table->get(strval($fd), "name");

            try {
                User::where('name', $this->table->get(strval($fd), "name"))->first()->delete();
            } catch (\Exception $e) {
                Log::info($e->getMessage());
            }


            echo "Connection close: {$fd}, total connections: " . $this->table->count() . "\n";
        });

        return $this;
    }

    public function disconnect(): Sockets
    {
        $this->server->on('Disconnect', function (Server $server, int $fd) {
            $this->table->del($fd);
            echo "Disconnect: {$fd}, total connections: " . $this->table->count() . "\n";
        });

        return $this;
    }
}