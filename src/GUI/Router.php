<?php

declare(strict_types=1);

namespace appleJuiceNETZ\GUI;

use appleJuiceNETZ\appleJuice\Core;
use appleJuiceNETZ\Exception\CoreAuthException;
use appleJuiceNETZ\Exception\CoreUnavailableException;
use appleJuiceNETZ\Exception\RedirectException;
use appleJuiceNETZ\GUI\Controller as C;

/**
 * Front-Controller: Anmeldung, Seitentabelle, Rendering und Fehlerseiten.
 * Neue Seiten werden in PAGES eingetragen (Controller + Template).
 */
class Router
{
    /** site => Controller-Klasse. */
    private const PAGES = [
        'start' => C\StartController::class,
        'downloads' => C\DownloadsController::class,
        'dl_users' => C\DownloadSourcesController::class,
        'dl_parts' => C\DownloadPartsController::class,
        'uploads' => C\UploadsController::class,
        'search' => C\SearchController::class,
        'shares' => C\SharesController::class,
        'sharefiles' => C\ShareFilesController::class,
        'server' => C\ServerController::class,
        'settings' => C\SettingsController::class,
        'user_settings' => C\SettingsController::class,
        'extras' => C\ExtrasController::class,
        'help' => C\HelpController::class,
        'kickcore' => C\KickCoreController::class,
    ];

    /** Seiten mit eigener, nicht-HTML-Antwort. */
    private const RAW = ['api', 'showparts', 'directory'];

    public function handle(): void
    {
        $permalink = isset($_GET['l']) && is_string($_GET['l'])
            ? Permalink::parse($_GET['l'])
            : null;

        if ($permalink !== null || isset($_POST['host'])) {
            if ($this->authenticate($permalink)) {
                return;
            }
        }

        if (empty($_SESSION['core_host'])) {
            echo View::render('layout/login', $this->loginData());

            return;
        }

        $site = $this->site();

        if ($site === 'logout') {
            session_unset();
            $this->redirectHeader('index.php');

            return;
        }

        try {
            if (in_array($site, self::RAW, true)) {
                $this->raw($site);

                return;
            }
            $this->dispatch($site);
        } catch (RedirectException $r) {
            $this->redirectHeader($r->url);
        } catch (CoreAuthException) {
            session_unset();
            $_SESSION['login']['wrong_pass'] = true;
            $this->redirectHeader('index.php');
        } catch (CoreUnavailableException) {
            http_response_code(503);
            echo View::render('layout/error', [
                'code' => 503,
                'title' => Format::lang()->UI->core_error_title,
                'text' => Format::lang()->UI->core_error_text,
            ]);
        } catch (\Throwable $t) {
            http_response_code(500);
            error_log((string)$t);
            echo View::render('layout/error', ['code' => 500, 'title' => 'Internal Server Error', 'text' => '']);
        }
    }

    private function redirectHeader(string $url): void
    {
        if (!headers_sent()) {
            header('Location: ' . $url, true, 303);
        }
    }

    private function site(): string
    {
        $site = $_GET['site'] ?? 'start';

        return is_string($site) && $site !== '' ? basename($site) : 'start';
    }

    private function raw(string $site): void
    {
        $file = GUI_ROOT . '/endpoints/' . $site . '.php';
        try {
            require $file;
        } catch (CoreAuthException) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'unauthorized']);
        } catch (CoreUnavailableException) {
            http_response_code(503);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'core_unavailable']);
        }
    }

    /**
     * Anmeldung per Formular oder Permalink.
     *
     * @return bool true, wenn die Antwort bereits gesendet wurde (Weiterleitung)
     */
    private function authenticate(?array $permalink): bool
    {
        $host = $permalink[0] ?? ($_POST['host'] ?? '');
        $password = $permalink[1] ?? ($_POST['cpass'] ?? '');
        if (!is_string($host) || !is_string($password) || !$this->validHost($host)) {
            $_SESSION['login']['host'] = true;

            return false;
        }
        $hash = strlen($password) === 32 ? $password : md5($password);
        $url = rtrim($host, '/') . '/xml/settings.xml?' . http_build_query(['password' => $hash]);
        $body = @file_get_contents($url, false, stream_context_create(['http' => ['timeout' => 8, 'ignore_errors' => true]]));

        if (empty($body)) {
            $_SESSION['login']['host'] = true;

            return false;
        }
        if (str_contains($body, 'wrong password.')) {
            $_SESSION['login']['wrong_pass'] = true;

            return false;
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            @session_regenerate_id(true);
        }
        $_SESSION['core_pass'] = $hash;
        $_SESSION['core_host'] = rtrim($host, '/');
        unset($_SESSION['login']);

        $link = $_POST['ajfsp_link'] ?? '';
        if (is_string($link) && $link !== '') {
            $_SESSION['ajfsp_link'] = $link;
        }
        if ($permalink !== null) {
            $this->redirectHeader('index.php');

            return true;
        }

        return false;
    }

    private function validHost(string $host): bool
    {
        $scheme = parse_url($host, PHP_URL_SCHEME);

        return in_array($scheme, ['http', 'https'], true) && filter_var($host, FILTER_VALIDATE_URL) !== false;
    }

    private function loginData(): array
    {
        $login = $_SESSION['login'] ?? [];
        unset($_SESSION['login']);
        $default = ($_ENV['CORE_HOST'] ?: $_ENV['REAL_IP']) . ':' . $_ENV['CORE_PORT'];

        return [
            'error_host' => !empty($login['host']),
            'error_pass' => !empty($login['wrong_pass']),
            'default_host' => $default,
            'ajfsp_link' => Request::str('ajfsp_link'),
            'title' => Format::lang()->Login->login,
        ];
    }

    private function dispatch(string $site): void
    {
        $class = self::PAGES[$site] ?? null;

        if ($class === null) {
            $this->renderPage(new Page('pages/error404', [], Format::lang()->System->pagetitle->{'404'}, status: 404), '404');

            return;
        }

        /** @var C\Controller $controller */
        $controller = new $class($site);
        $this->handleLinks();
        $this->renderPage($controller->handle(), $site);
    }

    /** ajfsp_link aus Formular, Session (nach Login) oder URL entgegennehmen und an den Core geben. */
    private function handleLinks(): void
    {
        $input = Request::str('ajfsp_link');
        if ($input === '' && !empty($_SESSION['ajfsp_link'])) {
            $input = (string)$_SESSION['ajfsp_link'];
        }
        unset($_SESSION['ajfsp_link']);
        if ($input === '') {
            return;
        }

        $lang = Format::lang();
        $results = LinkProcessor::submit($input, new Core());
        $hasFile = false;
        $hasServer = false;
        foreach ($results as $r) {
            $l = $r['link'];
            if ($l['type'] === 'file') {
                $hasFile = true;
                if ($r['ok']) {
                    Flash::add('success', $l['name'] . ' (' . Format::bytes($l['size']) . ')', $lang->Downloads->get_start);
                    $_SESSION['link_marker'][] = $l['raw'];
                } else {
                    Flash::add('warning', $l['name'] . ': ' . $r['reply']);
                }
            } else {
                $hasServer = true;
                Flash::add($r['ok'] ? 'success' : 'warning', $l['host'] . ':' . $l['port'] . ' ⇒ ' . $r['reply']);
            }
        }
        if ($hasFile) {
            throw new RedirectException('index.php?site=downloads');
        }
        if ($hasServer) {
            throw new RedirectException('index.php?site=server');
        }
    }

    private function renderPage(Page $page, string $site): void
    {
        http_response_code($page->status);
        $shell = $this->shellData($site);
        $flash = Flash::pull();
        $marker = $_SESSION['link_marker'] ?? [];
        unset($_SESSION['link_marker']);
        $lang = Format::lang();
        $content = View::render($page->template, $page->data + ['lang' => $lang]);

        echo View::render('layout/' . $page->layout, [
            'content' => $content,
            'site' => $site,
            'nav_site' => Navigation::parent($site),
            'title' => $page->title ?: ($lang->System->pagetitle->$site ?? ''),
            'scripts' => $page->scripts,
            'poll' => $page->poll,
            'flash' => $flash,
            'link_marker' => $marker,
            'lang' => $lang,
        ] + $shell);
    }

    /** Daten, die jede Seite im Rahmen braucht (Header, Navigation, Warnungen). */
    private function shellData(string $site): array
    {
        $core = new Core();
        $settings = $core->command('xml', 'settings.xml');
        $servers = new \appleJuiceNETZ\appleJuice\Server();
        $info = $servers->info();
        $uploads = new \appleJuiceNETZ\appleJuice\Uploads();
        $uploads->refresh_cache();
        $plugins = new Plugins();
        $plugins->Find_Plugins();

        return [
            'nick' => (string)($settings['NICK']['VALUES']['CDATA'] ?? ''),
            'credits' => Format::bytes($info['CREDITS']),
            'credits_negative' => (float)$info['CREDITS'] < 0,
            'uploads_active' => (int)($uploads->cache['phpaj_ul'] ?? 0),
            'firewalled' => $servers->netstats['firewalled'] === 'true',
            'connecting' => (int)$servers->netstats['connectedwith'] < 0,
            'plugins' => $plugins->liste,
            'nav' => Navigation::items(),
            'new_version' => VersionCheck::newer(),
            'permalink' => !empty($_ENV['TOP_SHOW_PERMALINK'])
                ? Permalink::create($_SESSION['core_host'], $_SESSION['core_pass'])
                : null,
            'csrf' => Csrf::token(),
            'faq_url' => $_ENV['FAQ_URL'],
            'version' => PHP_GUI_VERSION,
        ];
    }
}
