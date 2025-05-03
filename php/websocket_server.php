<?php
require dirname(__DIR__) . '/vendor/autoload.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class SignalingServer implements MessageComponentInterface {
    protected $clients;
    protected $userConnections;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->userConnections = [];
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);
        echo "Received message: " . $msg . "\n";
        
        if (isset($data['type']) && $data['type'] === 'register') {
            // Store user connection
            $this->userConnections[$data['userId']] = $from;
            echo "User {$data['userId']} registered\n";
            return;
        }

        if (isset($data['to'])) {
            $toUserId = $data['to'];
            if (isset($this->userConnections[$toUserId])) {
                $toConnection = $this->userConnections[$toUserId];
                echo "Forwarding message to user {$toUserId}\n";
                $toConnection->send($msg);
            } else {
                echo "User {$toUserId} not found\n";
                // Notify sender that recipient is not available
                $from->send(json_encode([
                    'type' => 'error',
                    'message' => 'Recipient not available'
                ]));
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        // Remove user connection
        $userId = array_search($conn, $this->userConnections);
        if ($userId !== false) {
            unset($this->userConnections[$userId]);
            echo "User {$userId} disconnected\n";
        }
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }
}

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new SignalingServer()
        )
    ),
    8080
);

echo "WebSocket server started on port 8080\n";
$server->run(); 