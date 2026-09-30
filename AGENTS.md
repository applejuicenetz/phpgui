# phpGUI: Release-Prozess

Diese Anleitung beschreibt den aktuellen Prozess dieses Repositories. Vor einem Release den Git-Status prüfen und vorhandene, nicht zum Release gehörende Änderungen unangetastet lassen.

## Release vorbereiten

1. Die neue Version in `bootstrap.php` (`PHP_GUI_VERSION`) eintragen. Die bisherigen Tags verwenden Nummern ohne `v`-Präfix, zum Beispiel `0.32.0`.
2. In `CHANGELOG.md` oben einen Abschnitt für dieselbe Version ergänzen und die Änderungen beschreiben.
3. Relevante PHP-Dateien auf Syntaxfehler prüfen und betroffene Funktionen testen. `tests/issue4.php` deckt nur die dort beschriebenen Fälle ab und ersetzt keine Prüfung anderer Änderungen.
4. Die Release-Änderungen über den üblichen Review-Prozess nach `main` bringen. Ein Push auf `main` startet bereits den Container-Workflow; er ist noch kein GitHub-Release.

## Release veröffentlichen

1. Einen Git-Tag mit der Version am freizugebenden Commit erstellen und einen GitHub-Release für diesen Tag veröffentlichen. Der Workflow `.github/workflows/container.yml` reagiert auf das Ereignis `release: released` (sowie auf Pushes nach `main` und manuelle Starts).
2. Der Workflow baut Images für `linux/amd64` und `linux/arm64` auf getrennten Runnern, lädt sie per Digest zu Docker Hub und GHCR hoch und erstellt anschließend die Multi-Plattform-Tags in beiden Registries. Die regulären Image-Tags werden von `docker/metadata-action` aus dem GitHub-Ereignis abgeleitet.
3. Den Erfolg beider Build-Jobs und des Merge-Jobs in GitHub Actions sowie die veröffentlichten Tags unter `docker.io/applejuicenetz/phpgui` und `ghcr.io/applejuicenetz/phpgui` prüfen. Das README verwendet `ghcr.io/applejuicenetz/phpgui:latest` als Beispiel.

Für das Hochladen benötigen die Workflows die Repository-Variablen `DOCKER_HUB_USER` und `GHCR_USER` sowie die Secrets `DOCKER_HUB_TOKEN` und `GHCR_TOKEN`.

## Bestehenden Tag neu bauen

Der manuelle Workflow `.github/workflows/rebuild_container.yml` nimmt einen vorhandenen Git-Tag als Eingabe und baut dessen Quellstand erneut für beide Architekturen. Für ältere Tags ersetzt er im Workflow eine Basisimage-Zeile `FROM php:8-apache` durch `FROM php:8.4-apache`; der Tag selbst wird dadurch nicht geändert. Er veröffentlicht den eingegebenen Image-Tag und setzt zusätzlich `latest`, falls dieser Tag zum aktuellsten GitHub-Release gehört. Vor dem Start prüfen, ob das Überschreiben dieser Registry-Tags beabsichtigt ist.
