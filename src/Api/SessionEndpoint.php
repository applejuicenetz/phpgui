<?php

declare(strict_types=1);

namespace appleJuiceNETZ\Api;

use appleJuiceNETZ\GUI\Csrf;
use appleJuiceNETZ\GUI\Permalink;
use appleJuiceNETZ\GUI\Request;
use appleJuiceNETZ\Kernel;

final class SessionEndpoint extends Endpoint
{
    public const PUBLIC = true;

    public function get(): array
    {
        $authenticated = !empty($_SESSION['core_host']);
        return [
            'authenticated' => $authenticated,
            'csrf' => Csrf::token(),
            'language' => $_ENV['GUI_LANGUAGE'],
            'timezone' => $_ENV['TZ'],
            'translations' => Kernel::getLanguage()->translate(),
            'version' => PHP_GUI_VERSION,
            'refresh' => (int)$_ENV['GUI_REFRESH_INTERVAL'],
            'default_host' => ($_ENV['CORE_HOST'] ?: $_ENV['REAL_IP']) . ':' . $_ENV['CORE_PORT'],
            'show_news' => !empty($_ENV['GUI_SHOW_NEWS']),
            'show_share' => !empty($_ENV['GUI_SHOW_SHARE']),
            'faq_url' => $_ENV['FAQ_URL'],
            'rel_info' => CoreData::relInfo(),
            'permalink' => $authenticated && !empty($_ENV['TOP_SHOW_PERMALINK'])
                ? Permalink::create($_SESSION['core_host'], $_SESSION['core_pass']) : null,
        ];
    }

    public function post(): array
    {
        $action = Request::str('action', 'login');
        if ($action === 'logout') {
            session_unset();
            session_regenerate_id(true);
            return $this->get();
        }
        if ($action === 'shutdown') {
            if (empty($_SESSION['core_host'])) throw new ApiException(401, 'unauthorized');
            (new \appleJuiceNETZ\appleJuice\Core())->command('function', 'exitcore');
            session_unset();
            session_regenerate_id(true);
            return $this->get();
        }
        if ($action !== 'login') throw new ApiException(400, 'unknown_action');
        $permalink = Request::str('l');
        if ($permalink !== '') {
            $credentials = Permalink::parse($permalink);
            if ($credentials === null) throw new ApiException(400, 'invalid_host');
            [$host, $secret] = $credentials;
        } else {
            $host = Request::str('host');
            $secret = Request::str('cpass');
        }
        $hash = CoreLogin::verify($host, $secret);
        session_unset();
        CoreLogin::bind($host, $hash);
        $result = $this->get();
        // Explicit opt-in only. Retains the existing "remember login" contract.
        if (Request::str('remember_login') === '1') $result['remember'] = ['url' => rtrim($host, '/'), 'md5' => $hash];
        return $result;
    }
}
