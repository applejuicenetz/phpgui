<?php

declare(strict_types=1);

/** HTTP client with isolated cookies, JSON methods and explicit checks. */
final class HttpClient
{
    private CurlHandle $curl;
    private string $csrf = '';
    public int $count = 0;

    public function __construct(public string $base)
    {
        $this->curl = curl_init();
        curl_setopt_array($this->curl, [CURLOPT_COOKIEFILE => '', CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 30]);
    }

    public function request(string $query = '', ?array $fields = null, string $path = 'api.php'): array
    {
        $headers = [];
        curl_setopt_array($this->curl, [
            CURLOPT_URL => rtrim($this->base, '/') . '/' . $path . ($query !== '' ? '?' . $query : ''),
            CURLOPT_HEADERFUNCTION => static function ($curl, string $line) use (&$headers): int {
                if (str_starts_with($line, 'HTTP/')) $headers = [];
                elseif (str_contains($line, ':')) {
                    [$key, $value] = explode(':', $line, 2);
                    $headers[strtolower(trim($key))] = trim($value);
                }
                return strlen($line);
            },
        ]);
        if ($fields !== null) curl_setopt($this->curl, CURLOPT_POSTFIELDS, http_build_query($fields));
        else curl_setopt($this->curl, CURLOPT_HTTPGET, true);
        $body = curl_exec($this->curl);
        check($body !== false, 'HTTP request failed: ' . curl_error($this->curl));
        ++$this->count;
        check(!str_contains($body, 'Fatal error') && !str_contains($body, '<b>Warning</b>'), 'PHP error in response');
        return ['status' => curl_getinfo($this->curl, CURLINFO_RESPONSE_CODE), 'headers' => $headers, 'body' => $body];
    }

    public function get(string $endpoint, array $params = []): array
    {
        $response = $this->request(http_build_query(['endpoint' => $endpoint] + $params));
        check($response['status'] === 200, 'GET failed: ' . $endpoint . ': ' . $response['body']);
        return decodeJson($response['body']);
    }

    public function post(string $endpoint, array $fields, array $params = []): array
    {
        $response = $this->request(http_build_query(['endpoint' => $endpoint] + $params), ['_csrf' => $this->csrf] + $fields);
        check($response['status'] === 200, 'POST failed: ' . $endpoint . ': ' . $response['body']);
        return decodeJson($response['body']);
    }

    public function login(string $core, string $password = ''): array
    {
        $this->csrf = $this->get('session')['csrf'];
        $result = $this->post('session', ['action' => 'login', 'host' => $core, 'cpass' => $password]);
        $this->csrf = $result['csrf'];
        check($result['authenticated'], 'Login failed');
        return $result;
    }
}

function check(bool $condition, string $message): void
{
    if (!$condition) throw new RuntimeException($message);
}

function decodeJson(string $body): array
{
    return json_decode($body, true, flags: JSON_THROW_ON_ERROR);
}

function testOptions(): array
{
    $options = getopt('', ['base:', 'core:']);
    return [$options['base'] ?? 'http://127.0.0.1:8089', $options['core'] ?? 'http://127.0.0.1:19871'];
}
