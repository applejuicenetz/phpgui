<?php

declare(strict_types=1);

namespace appleJuiceNETZ\GUI\Controller;

use appleJuiceNETZ\appleJuice\Core;
use appleJuiceNETZ\appleJuice\Server;
use appleJuiceNETZ\Exception\RedirectException;
use appleJuiceNETZ\GUI\Csrf;
use appleJuiceNETZ\GUI\Flash;
use appleJuiceNETZ\GUI\Format;
use appleJuiceNETZ\GUI\Page;
use appleJuiceNETZ\GUI\Request;

/** Gemeinsame Hilfen aller Seiten-Controller. */
abstract class Controller
{
    protected Core $core;
    protected object $lang;

    public function __construct(protected string $site = '')
    {
        $this->core = new Core();
        $this->lang = Format::lang();
    }

    abstract public function handle(): Page;

    /** Core-Einstellungen als flaches Array [name => wert]. */
    protected function settings(): array
    {
        static $cache = null;
        if ($cache !== null) {
            return $cache;
        }
        return $cache = \appleJuiceNETZ\GUI\CoreSettings::read($this->core);
    }

    /** Aktuelle Zahlen aus modified.xml?filter=informations. */
    protected function information(): array
    {
        static $cache = null;
        if ($cache !== null) {
            return $cache;
        }
        return $cache = \appleJuiceNETZ\GUI\ViewData::information();
    }

    protected function server(): Server
    {
        static $server = null;

        return $server ??= new Server();
    }

    /** Aktion nur per POST mit gültigem CSRF-Token. */
    protected function guardPost(): bool
    {
        return Request::isPost() && Csrf::valid();
    }

    protected function flash(string $level, string $text, ?string $title = null): void
    {
        Flash::add($level, $text, $title);
    }

    protected function redirect(string $url): never
    {
        throw new RedirectException($url);
    }
}
