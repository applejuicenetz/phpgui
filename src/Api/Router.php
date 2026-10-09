<?php

declare(strict_types=1);

namespace appleJuiceNETZ\Api;

use appleJuiceNETZ\Exception\CoreAuthException;
use appleJuiceNETZ\Exception\CoreUnavailableException;
use appleJuiceNETZ\GUI\Csrf;
use appleJuiceNETZ\GUI\Request;

final class Router
{
    private const ROUTES = [
        'session' => SessionEndpoint::class,
        'status' => StatusEndpoint::class,
        'dashboard' => DashboardEndpoint::class,
        'downloads' => DownloadsEndpoint::class,
        'sources' => SourcesEndpoint::class,
        'uploads' => UploadsEndpoint::class,
        'search' => SearchEndpoint::class,
        'shares' => SharesEndpoint::class,
        'files' => FilesEndpoint::class,
        'statistics' => StatisticsEndpoint::class,
        'servers' => ServersEndpoint::class,
        'settings' => SettingsEndpoint::class,
        'limits' => LimitsEndpoint::class,
        'directories' => DirectoriesEndpoint::class,
        'parts' => PartsEndpoint::class,
        'links' => LinksEndpoint::class,
        'news' => NewsEndpoint::class,
    ];

    public function handle(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
        header('X-Content-Type-Options: nosniff');
        try {
            $class = self::ROUTES[Request::get('endpoint')] ?? null;
            if ($class === null) throw new ApiException(404, 'unknown_endpoint');
            if (!$class::PUBLIC && empty($_SESSION['core_host'])) throw new ApiException(401, 'unauthorized');
            $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
            if (!in_array($method, ['GET', 'POST'], true)) throw new ApiException(405, 'method_not_allowed');
            if ($method === 'POST' && !Csrf::valid()) throw new ApiException(403, 'csrf');
            $endpoint = new $class();
            $result = $method === 'POST' ? $endpoint->post() : $endpoint->get();
            echo json_encode($result, JSON_THROW_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE);
        } catch (ApiException $error) {
            $this->error($error->status, $error->error);
        } catch (CoreAuthException) {
            unset($_SESSION['core_host'], $_SESSION['core_pass'], $_SESSION['cache']);
            $this->error(401, 'unauthorized');
        } catch (CoreUnavailableException) {
            $this->error(503, 'core_unavailable');
        } catch (\Throwable $error) {
            // Do not log exception arguments: Core URLs can contain credentials.
            error_log('API failure: ' . get_class($error));
            $this->error(500, 'internal_error');
        }
    }

    private function error(int $status, string $error): void
    {
        http_response_code($status);
        echo json_encode(['error' => $error]);
    }
}
