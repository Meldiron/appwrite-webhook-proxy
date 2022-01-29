<?

require_once 'vendor/autoload.php';

use Swoole\Coroutine\Http\Server;

Co\run(function()
{
    $server = new Server('0.0.0.0', 80, false);

    $server->handle('/', function(Swoole\Http\Request $swooleRequest, Swoole\Http\Response $swooleResponse)
    {
        $endpoint = \getenv('WEBHOOK_PROXY_APPWRITE_ENDPOINT');
        $apiKey = \getenv('WEBHOOK_PROXY_APPWRITE_API_KEY');
        $projectId = \getenv('WEBHOOK_PROXY_APPWRITE_PROJECT_ID');
        $functionId = \getenv('WEBHOOK_PROXY_APPWRITE_FUNCTION_ID');

        $requestBody = [
            'data' => \json_encode($swooleRequest)
        ];

        $ch = \curl_init();

        $optArray = array(
            CURLOPT_URL => $endpoint . '/functions/' . $functionId . '/executions',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => \json_encode($requestBody),
            CURLOPT_HEADEROPT => \CURLHEADER_UNIFIED,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'X-Appwrite-Project: ' . $projectId,
                'X-Appwrite-Key: ' . $apiKey
            )
        );

        \curl_setopt_array($ch, $optArray);

        $result = curl_exec($ch);
        $response = curl_getinfo($ch, \CURLINFO_HTTP_CODE);

        $responseObject = [
            'code' => $response,
            'error' => \curl_error($ch),
            'body' => $result
        ];

        \curl_close($ch);

        $swooleResponse->end(\json_encode($responseObject));
    });

    $server->start();
});