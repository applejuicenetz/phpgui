<?php

declare(strict_types=1);

/** HTTP test client with isolated in-memory cookies and explicit checks. */
final class HttpClient
{
    private CurlHandle $curl;
    public int $count = 0;

    public function __construct(public string $base)
    {
        $this->curl = curl_init();
        curl_setopt_array($this->curl, [CURLOPT_COOKIEFILE => '', CURLOPT_RETURNTRANSFER => true, CURLOPT_FOLLOWLOCATION => true, CURLOPT_TIMEOUT => 30]);
    }

    public function request(string $query = '', ?array $fields = null): array
    {
        $headers = [];
        curl_setopt_array($this->curl, [
            CURLOPT_URL => rtrim($this->base, '/') . '/index.php' . ($query !== '' ? '?' . $query : ''),
            CURLOPT_HEADERFUNCTION => static function ($curl, string $line) use (&$headers): int {
                if (str_starts_with($line, 'HTTP/')) {
                    $headers = [];
                } elseif (str_contains($line, ':')) {
                    [$key, $value] = explode(':', $line, 2);
                    $headers[strtolower(trim($key))] = trim($value);
                }
                return strlen($line);
            },
        ]);
        if ($fields !== null) {
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, http_build_query($fields));
        } else {
            curl_setopt($this->curl, CURLOPT_HTTPGET, true);
        }
        $body = curl_exec($this->curl);
        check($body !== false, 'HTTP request failed: ' . curl_error($this->curl));
        ++$this->count;
        check(!str_contains($body, 'Fatal error') && !str_contains($body, '<b>Warning</b>'), 'PHP error in response');
        return ['status' => curl_getinfo($this->curl, CURLINFO_RESPONSE_CODE), 'headers' => $headers, 'body' => $body];
    }

    public function page(string $site): string
    {
        $response = $this->request('site=' . $site);
        check($response['status'] === 200 && str_contains($response['body'], 'data-site=') && !str_contains($response['body'], 'login_form'), 'Page failed: ' . $site);
        return $response['body'];
    }

    public function post(string $site, array $fields): array
    {
        return $this->request('site=' . $site, ['_csrf' => csrfToken($this->page($site))] + $fields);
    }

    public function live(string $type): array
    {
        return decodeJson($this->request('api=live&type=' . $type)['body'])[$type];
    }
}

function check(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

function decodeJson(string $body): array
{
    return json_decode($body, true, flags: JSON_THROW_ON_ERROR);
}

function csrfToken(string $body): string
{
    check(preg_match('/name="csrf-token" content="([^"]+)"/', $body, $matches) === 1, 'Missing CSRF token');
    return $matches[1];
}

function testOptions(): array
{
    $options = getopt('', ['base:', 'core:']);
    return [$options['base'] ?? 'http://127.0.0.1:8088', $options['core'] ?? 'http://127.0.0.1:19861'];
}
