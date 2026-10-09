# phpGUI: Release-Prozess

Diese Anleitung beschreibt den aktuellen Prozess dieses Repositories. Vor einem Release den Git-Status prüfen und vorhandene, nicht zum Release gehörende Änderungen unangetastet lassen.

## Sprache im Quellcode

- Quellcode ausschließlich auf Englisch schreiben: Klassen, Methoden, Funktionen, Variablen, technische Fehlermeldungen, Docblocks und Inline-Kommentare. Bestehende deutschsprachige Bezeichner und Kommentare ins Englische übersetzen; bei Umbenennungen alle Aufrufstellen und Tests mit anpassen.
- Benutzertexte bleiben lokalisiert in den Sprachdateien; deutsche Oberflächentexte nicht pauschal ins Englische übersetzen. Die deutschsprachige Projektdokumentation bleibt deutsch. Externe API-Felder und Protokollbezeichner unverändert lassen, wenn deren Umbenennung die Kompatibilität brechen würde.

## Lokalisierung

- Sämtliche benutzersichtbaren Texte ausschließlich in den bestehenden Sprachdateien `language/de.json` und `language/en.json` pflegen. Das gilt auch für JavaScript-Meldungen, Dialoge, Platzhalter, Tooltips, zugängliche Beschriftungen und Fehlertexte in der Oberfläche. Keine zusätzliche `language.json` neben diesen Dateien anlegen.
- Übersehene oder bestehende hartcodierte Oberflächentexte in die Sprachdateien umziehen und sämtliche Verwendungen auf die entsprechenden Übersetzungsschlüssel umstellen. Schlüssel in Deutsch und Englisch vollständig und konsistent halten; JavaScript erhält benötigte Übersetzungen aus derselben Quelle.
- Technische Protokollwerte, Code-Bezeichner und ausschließlich interne Logmeldungen sind keine Oberflächentexte und bleiben unverändert beziehungsweise englisch.

## Modernisierung und Sicherheit

- Modernisierung und Bereinigung der phpGUI sind ausdrücklich erlaubt, einschließlich Architektur, PHP-Code, Templates, CSS, JavaScript und Abhängigkeiten. Bestehende Funktionen und Integrationen erhalten und durch Tests absichern; das Layout soll erkennbar bleiben und mobile-first ausgelegt sein.
- Interne URLs, Routen, API-Strukturen und deren Aufteilung dürfen geändert und vereinheitlicht werden. Alle betroffenen Frontend-Aufrufer, Templates und Tests gemeinsam anpassen; bisherige interne URLs müssen nicht erhalten bleiben. Bestehende Funktionen erhalten. Die Frontend-Integration für `web+ajfsp` und bestehende Permalinks müssen weiterhin funktionieren, einschließlich Link-Übernahme und Anmeldung. Diese Integrationen bei Änderungen an Routing, Authentifizierung oder Link-Verarbeitung durch Regressionstests absichern.
- PHP 8.5 ist das freigegebene Modernisierungsziel. PHP-Dateien und Laufzeit entsprechend anpassen; Versionsanforderungen in Composer, Container und Dokumentation gemeinsam aktualisieren. Kompatibilität durch tatsächlich ausgeführte Tests unter PHP 8.5 belegen, nicht allein durch Syntaxprüfung.
- Sicherheit systematisch verbessern: Eingaben validieren, Ausgaben kontextgerecht maskieren, dynamische Codeausführung vermeiden, zustandsändernde Aktionen gegen CSRF schützen und Sessions sowie Core-Zugriffe absichern. Zugangsdaten und Passwort-Hashes weder protokollieren noch unbeabsichtigt ausgeben. Gewollte Integrationen, insbesondere Permalinks und Browser-Erweiterung, kompatibel halten und Sicherheitsgrenzen dokumentieren.
- Sicherheitsverbesserungen gelten erst nach Prüfung als umgesetzt. Keine pauschale Behauptung, die Anwendung sei „sicher“; getestete Schutzmaßnahmen und verbleibende Risiken konkret benennen.

## Interne API

- Daten- und Bildanfragen laufen über `index.php?api=<endpoint>`, Seiten über `index.php?site=<page>`. API-Anfragen benötigen eine angemeldete Session.
- `api=live&type=status,downloads,uploads,dashboard,search` liefert Live-Daten als JSON; `api=limits&action=set_maxdl|set_maxul` speichert Limits per POST mit `value` in Bytes/s und `_csrf`.
- `api=news` liefert die bereinigten Dashboard-News als JSON (`html`); das Dashboard lädt sie nach dem Seitenaufbau per JavaScript, damit ein langsamer News-Server weder Seite noch Polling blockiert. `NewsFeed` gibt dafür die Session-Sperre während des Abrufs frei.
- `api=directories&dir=<path>` liefert Verzeichnisse als JSON; `api=parts&dl_id=<id>` beziehungsweise `usr_id=<id>` liefert Part-Maps als SVG. `PartMapService` lädt die fachlichen Daten unabhängig von HTTP und SVG-Rendering.
- `tests/raw_routes.php`, `tests/raw_errors.php` und `tests/link_integrations.php` prüfen API-Verträge, Core-Fehler und Link-Integrationen gegen einen isolierten Mock-Core. Browserseitige Protokollhandler-Registrierung zusätzlich im HTTPS-Browser prüfen.

## Core-Datenklassen

- Alle Klassen unter `src/appleJuice/` verwenden `declare(strict_types=1)` sowie explizite Parameter-, Rückgabe- und Property-Typen. Core-XML-Felder und die abgeleiteten `phpaj_*`-Schlüssel im Session-Cache bilden den Datenvertrag.
- `Downloads`, `Uploads` und `Search` halten ihre inkrementellen Daten per Referenz im Session-Cache (`$_SESSION['cache']`), der vor dem Binden mit `[]` initialisiert wird. Aggregationen setzen ihre Zähler bei jedem Durchlauf zurück und entfernen veraltete IDs anhand der aktuellen Core-ID-Liste. Suchaktionen verwenden ausschließlich ihre übergebene ID, nie HTTP-Eingaben.
- `Core::command()` streamt die Antwort. `Share` hält keine Dateiliste im Session-Cache: `Share::scan()` übergibt Datensätze einzeln an einen Consumer, `ShareSelection` behält nur die benötigte Seite.
- Share-Verzeichnisse ändert `Share` über `saveDirectories()`: aktuelle Einstellungen laden, die vollständige Liste als `setsettings` mit `countshares`, `sharedirectoryN` und `sharesubN` senden, lokalen Einstellungscache verwerfen. Das Incoming-Verzeichnis ergänzt der Core selbst.
- Der Core nutzt `-1` als Server-ID einer getrennten Verbindung; `Server::ids()` filtert sie explizit. Der Serverlisten-Import verarbeitet höchstens zehn unterschiedliche Links und toleriert leere Antworten.
- `Powerdownload` sendet phpGUI als ganze Zahl, weil der Core den Wert als Integer parst.

## Links hinzufügen

- Der Dialog „Links hinzufügen“ (`templates/partials/modals.php`) nimmt `ajfsp://`-Links als Text und `.ajl`-Dateien über eine Dateiauswahl entgegen. `links.js` wandelt den Dateiinhalt im Browser mit `ajl.js` (`ajlToLinks()`) in `ajfsp://file|name|checksum|size/`-Links um und hängt sie an das Textfeld an; abgesendet wird ausschließlich Text über `ajfsp_link`. Das Feld `ajfsp_target` setzt ein Unterverzeichnis im Incoming-Ordner für Datei-Links (`processlink` mit `subdir`); `LinkProcessor::targetDirectory()` normalisiert den Pfad und lehnt `..`, `:`, Steuerzeichen und mehr als 255 Zeichen ab, weil der Core solche Werte still ignoriert. Ein ungültiger Pfad zeigt eine Warnung, es wird kein Link hinzugefügt. Server-Links ignorieren das Ziel. `tests/link_target.php` prüft die Normalisierung. Das AJL-Format besteht aus einem Kopftext, der Zeile `100` und danach Dreiergruppen aus Name, MD5-Prüfsumme und Größe. Ungültige Dateien führen zu einem Hinweis im Dialog, nicht zu einer Serveranfrage.
- Es gibt kein Plugin-System und keine Addons-Seiten. Neue Funktionen sind Seiten in `Router::PAGES` oder Teile bestehender Seiten.

## Share-Suche

- Die Suche liegt direkt auf `index.php?site=shares&q=<text>` und durchsucht alle freigegebenen Verzeichnisse. In einer Ordneransicht (`site=sharefiles&dir=<path>&q=<text>`) ist sie auf den Unterbaum des Ordners begrenzt. Ohne `q` zeigt `shares` die Verwaltung der Verzeichnisse, `sharefiles` die direkten Dateien und Unterordner.
- Der Filter ist ein Teilstring-Vergleich ohne Beachtung der Groß-/Kleinschreibung auf dem vollständigen Pfad (`Share::matchesFilter()`), wie in der Java-GUI. Treffer erscheinen flach mit Pfad, ohne Ordnerliste.
- `Share::page(?string $directory, int $page, int $pageSize, string $filter)` liefert eine Seite mit 200 Einträgen; `null` bedeutet alle Shares. Pagination, Link-Export ohne Auswahl und Prioritäten wirken auf die gefilterte Menge.
- `public/manifest.json` deklariert für die installierte PWA den Protokoll-Handler `web+ajfsp` (`index.php?ajfsp_link=%s`) und den Datei-Handler für `.ajl` (`index.php?site=downloads`); `links.js` übernimmt die geöffnete Datei über `launchQueue` in den Dialog „Links hinzufügen“. `tests/manifest.php` prüft beide Einträge.
- Die Seiten `shares`, `sharefiles` und `sharestats` teilen die Tab-Leiste `share-tabs` („geteilte Ordner“ und „Statistik“); `sharefiles` markiert den Tab „geteilte Ordner“. `index.php?site=sharestats&stats=<modus>` zeigt die 50 am häufigsten oder zuletzt angefragten beziehungsweise gesuchten Dateien (`most`, `-most`, `last`, `-last`, `search`, `-search`; unbekannte Werte ergeben `most`). Das Dashboard verlinkt die Credits-Karte dorthin.
- Liste und Aktionen teilen `ShareFilesBase` (Basis von `SharesController` und `ShareFilesController`) und die Partials `share-search` und `share-files`. Das Suchfeld sucht nach 400 ms Pause, Esc leert es (`sharefiles.js`).

## Release vorbereiten

1. Die neue Version in `bootstrap.php` (`PHP_GUI_VERSION`) eintragen. Die bisherigen Tags verwenden Nummern ohne `v`-Präfix, zum Beispiel `0.32.0`.
2. In `CHANGELOG.md` oben einen Abschnitt für dieselbe Version ergänzen und die Änderungen beschreiben.
3. Relevante PHP-Dateien auf Syntaxfehler prüfen und betroffene Funktionen testen.
4. Die Release-Änderungen über den üblichen Review-Prozess nach `main` bringen. Ein Push auf `main` startet bereits den Container-Workflow; er ist noch kein GitHub-Release.

## Release veröffentlichen

1. Einen Git-Tag mit der Version am freizugebenden Commit erstellen und einen GitHub-Release für diesen Tag veröffentlichen. Der Workflow `.github/workflows/container.yml` reagiert auf das Ereignis `release: released` (sowie auf Pushes nach `main` und manuelle Starts).
2. Der Workflow baut in einem einzigen Job per QEMU und Buildx ein Multi-Plattform-Image für `linux/amd64` und `linux/arm64` und lädt es mit allen Tags zu Docker Hub und GHCR hoch. Pushes nach `main` (und manuelle Starts auf `main`) veröffentlichen den Tag `beta`; einen Tag `main` gibt es nicht. Release-Tags (und `latest`) leitet `docker/metadata-action` aus dem Release-Ereignis ab.
3. Den Erfolg beider Build-Jobs und des Merge-Jobs in GitHub Actions sowie die veröffentlichten Tags unter `docker.io/applejuicenetz/phpgui` und `ghcr.io/applejuicenetz/phpgui` prüfen. Das README verwendet `ghcr.io/applejuicenetz/phpgui:latest` als Beispiel.

Für das Hochladen benötigen die Workflows die Repository-Variablen `DOCKER_HUB_USER` und `GHCR_USER` sowie die Secrets `DOCKER_HUB_TOKEN` und `GHCR_TOKEN`.

## Bestehenden Tag neu bauen

Der manuelle Workflow `.github/workflows/rebuild_container.yml` nimmt einen vorhandenen Git-Tag als Eingabe und baut dessen Quellstand erneut für beide Architekturen. Für ältere Tags ersetzt er im Workflow eine Basisimage-Zeile `FROM php:8-apache` durch `FROM php:8.4-apache`; der Tag selbst wird dadurch nicht geändert. Er veröffentlicht den eingegebenen Image-Tag und setzt zusätzlich `latest`, falls dieser Tag zum aktuellsten GitHub-Release gehört. Vor dem Start prüfen, ob das Überschreiben dieser Registry-Tags beabsichtigt ist.

## Lokale Tests mit Mock-Core

Der eigenständige Dienst [`ajcore-mock`](https://github.com/applejuicenetz/ajcore-mock) (lokal `../ajcore-mock/mock_core.py`) ist ein zustandsbehafteter Mock der Core-API. Er ist nicht Bestandteil dieses Repositories. Er verwendet nur die Python-Standardbibliothek. Er bildet alle von phpGUI genutzten Endpunkte nach. Aktionen (`pausedownload`, `resumedownload`, `canceldownload`, `cleandownloadlist`, `renamedownload`, `settargetdir`, `setpowerdownload`, `processlink`, `search`, `serverlogin`, `removeserver`, `setsettings`) ändern den Zustand, aktive Downloads schreiten zeitbasiert voran. Passwort-Standard ist leer (MD5 `d41d8cd98f00b204e9800998ecf8427e`).

```shell
python3 ../ajcore-mock/mock_core.py --scenario busy --port 19851 --shareidx-bytes 0
# Omit --shareidx-bytes 0 to model a 3.5 MB share index.
```

Szenarien: `empty`, `busy` (Downloads in allen Status, Uploads, Shares, Suchergebnisse, Sonderzeichen in Namen, etwa 300 ISO-Dateien in Ordnern unter `/mock/isos/<Distribution>/<Release>/`), `firewalled`, `disconnected` (negative Credits). Der Mock ersetzt keinen Test gegen den echten Core; Formate und Antworten folgen `core-src/docs/openapi.yaml` und `XmlServer.java`. Bei Abweichungen gilt der Core-Code, und der Mock wird korrigiert.

phpGUI lokal gegen den Mock: PHP 8.5 und die in `composer.json` deklarierten Laufzeit-Erweiterungen `ctype`, `dom`, `json`, `libxml`, `mbstring`, `openssl`, `session` und `xml` verwenden. `curl` wird ausschließlich für HTTP-Tests benötigt und ist unter `require-dev` deklariert. `gd` und `zip` sind keine Anforderungen der Anwendung; Part-Maps werden als SVG erzeugt. Web-Dateien liegen unter `public/`. Der Dockerfile installiert keine zusätzlichen PHP-Erweiterungen; vor einem Container-Build die Composer-Plattformanforderungen gegen das Basisimage prüfen.

```shell
php -S 127.0.0.1:8088 -t public
```

Core-URL im Login: `http://127.0.0.1:19851`, Passwort leer.

## Tests

- Tests in diesem Repository dürfen ausschließlich in PHP geschrieben werden, einschließlich Test-Hilfen und Regressionstests. Keine Python-, JavaScript- oder Shell-Tests hinzufügen. Der externe Mock-Core ist ein separater Dienst und fällt nicht unter diese Quellcode-Regel.
- Tests unter PHP 8.5 ausführen. HTTP-Tests nutzen `tests/HttpClient.php` und benötigen `ext-curl`; Prüfungen werfen bei Fehlern Exceptions und sind unabhängig von der PHP-Einstellung `zend.assertions`.
- Schreibende Tests ausschließlich gegen einen isolierten Mock-Core ausführen, nicht gegen einen echten Core.

```shell
php tests/smoke.php --base=http://127.0.0.1:8088 --core=http://127.0.0.1:19851
php tests/raw_routes.php --base=http://127.0.0.1:8088 --core=http://127.0.0.1:19851
php tests/link_integrations.php --base=http://127.0.0.1:8088 --core=http://127.0.0.1:19851
php tests/raw_errors.php http://127.0.0.1:19851
php tests/core_services.php http://127.0.0.1:19851
php tests/core_models.php
php tests/share_filter.php
php tests/link_target.php
```

`core_services.php` führt Core-Aktionen gegen den Mock aus und stellt die Share-Einstellungen danach wieder her. `core_models.php`, `share_filter.php` und `link_target.php` benötigen keinen Dienst. `login_redirect.php`, `manifest.php` und `format.php` laufen ebenfalls ohne Dienst.
