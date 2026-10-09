# Änderungsprotokoll

Alle wichtigen Änderungen an diesem Projekt werden in dieser Datei dokumentiert.

## 0.33.0 (2026-10-08)

- Oberfläche modernisiert und für Smartphones optimiert, mit besserer Darstellung im Dunkelmodus
- Downloads, Uploads und Freigaben übersichtlicher; Mehrfachauswahl mit Shift-Klick bei geteilten Dateien
- Im Dialog „Links hinzufügen“ ein Zielverzeichnis wählen und AJL-Dateien direkt auswählen und in Downloads umwandeln; Teileanzeige jetzt scharf skalierbar
- PHP 8.5 erforderlich; Sicherheit und Verarbeitung großer Dateilisten verbessert
- Installierte App öffnet `web+ajfsp`-Links und `.ajl`-Dateien direkt
- Statistik der geteilten Dateien als Tab unter „geteilte Ordner“; Addons-Menü und phpinfo entfallen
- Suche in geteilten Ordnern direkt auf der Freigaben-Seite, über alle Ordner und Unterordner, ohne Beachtung der Groß-/Kleinschreibung
- Oberfläche als statische Vue-3-Anwendung ohne Build-Schritt, Backend liefert nur noch JSON; Layout bleibt erhalten. Neuer Login-Hintergrund, Dashboard mit getrennten Karten für Netzwerk und Community, Verbindungen mit konfiguriertem Maximum

## 0.32.2 (2026-10-04)

- Slot-Geschwindigkeit in den Verbindungseinstellungen aus `SPEEDPERSLOT` statt aus dem gesamten Upload-Limit gelesen; Speichern übernimmt damit wieder den richtigen Slot-Wert
- Exakte Upload- und Download-Limits beim Wechsel zwischen KB/s und MB/s erhalten, solange der angezeigte Wert nicht geändert wird
- MB/s-Anzeige in den Verbindungseinstellungen auf zwei Nachkommastellen erweitert

## 0.32.1 (2026-09-30)

- Registrierung des `web+ajfsp`-Protokollhandlers für HTTPS korrigiert und auch in der angemeldeten GUI aktiviert
- Vollständige HTML-Dokumente aus dem News-Feed als Inhalt der Dashboard-Karte eingebunden
- Release-Prozess dokumentiert und Dokumentation sowie Tests aus dem Docker-Build-Kontext ausgeschlossen

## 0.32.0 (2026-09-30)

- Instanz-Permalinks aus der 0.27-Reihe wiederhergestellt; bestehende Links funktionieren weiterhin
- Export von Links zu freigegebenen Dateien mit zuverlässiger Auswahl und kopierbarer Ausgabe wiederhergestellt

## 0.31.0 (2026-09-28)

### Downloads
- Spalten sortierbar gemacht, Auswahl auf Kontrollkästchen beschränkt, Kontrollkästchen für „Alle auswählen“ ergänzt und überflüssige Links unter der Tabelle entfernt
- Filter für die Download-Tabelle ergänzt
- Papierkorb-Symbol durch ein deutlicheres Aufräumsymbol ersetzt
- Geschwindigkeit direkt einstellbar, mit Umschaltung zwischen KB/s und MB/s

### Uploads
- Darstellung an die verbesserte Download-Liste angepasst
- Geschwindigkeit direkt einstellbar

### Dashboard
- Reihenfolge der Karten angepasst, damit Credits auf Mobilgeräten oben sichtbar bleiben
- Serverzeit im Format TT.MM.JJJJ angezeigt
- Download- und Uploadgeschwindigkeiten mit dem Zusatz /s angezeigt
- Zeile für die öffentliche IP-Adresse ergänzt; sie verwendet NETWORKINFO/IP des Cores wie die Java-GUI

### Suche
- Spalten für Name, Größe, Format und Quellen sortierbar gemacht
- Mehrfachauswahl und gleichzeitigen Download mehrerer Treffer ergänzt
- Filter für Dateinamen und Dateiformate ergänzt

### Layout und Navigation
- GUI auf breiten Bildschirmen verbreitert und Fußzeile unten fixiert
- Aktuelle Credits in der oberen Menüleiste angezeigt
- Bei „Links hinzufügen“ das Lupensymbol durch ein Pluszeichen ersetzt und den Dialog entsprechend umbenannt

### Einstellungen
- Umschaltung zwischen KB/s und MB/s für Geschwindigkeitswerte ergänzt

### Core und Infrastruktur
- Laufende Aktualisierung der Seiten per AJAX ohne vollständiges Neuladen ergänzt
- Regulären Ausdruck für die Zuordnung von Dateiteilen korrigiert und 7z-Dateiteile unterstützt
- Build und Einrichtung für lokale Builds korrigiert, Tippfehler behoben und Version auf 0.31.0 erhöht


## 0.30.0 (2025-09-26)

- Protokollhandler für web+ajfsp registriert, wenn die GUI über HTTPS läuft
- AJL-Import korrigiert
- Upload-Seite bei null verfügbaren Upload-Slots korrigiert
- Serverfehler 500 beim Beenden des Cores behoben

## 0.29.8 (2024-10-09)

- Abwärtskompatibilität mit der Browser-Erweiterung korrigiert

## 0.29.7 (2024-09-21)

- Fehler im Dashboard behoben
- Ersten Fortschrittsbalken bei Downloads entfernt

## 0.29.6 (2024-07-15)

- Darstellung korrigiert

## 0.29.5 (2024-05-15)

- Englische Übersetzung ergänzt
- Fehler auf allen Seiten behoben
- Seite für die Serververbindung ergänzt
- Mobile Navigationsleiste am unteren Bildschirmrand ergänzt

## 0.29.4 (2024-05-07)

- Dashboard korrigiert
- Suche korrigiert
- Download-Schaltflächen wieder funktionsfähig gemacht
- Design-Umschalter entfernt
- Share-Ansicht korrigiert

## 0.29.3 (2024-04-30)

- Fehler im einfachen Design behoben

## 0.29.2 (2024-04-25)

- Codestruktur verbessert, unter anderem durch PSR-4

## 0.29.1 (2024-04-24)

- Login korrigiert
- Design-Umschalter korrigiert

## 0.29.0 (2024-04-23)

- Neues, modernes Design eingeführt
- Community-Version veröffentlicht
- Design-Umschalter und Versionsprüfung ergänzt
- Suche, Uploads, Downloads, Dashboard und Ansicht freigegebener Dateien korrigiert

## 0.28.1 (2023-12-07)

- Design-Umschalter korrigiert

## 0.28.0 (2023-08-21)

- Anpassungen für PHP 8 vorgenommen
- Speicherüberlauf bei großen Shares behoben

## 0.27.10 (2023-08-21)

- Docker-Image wieder auf PHP 7 umgestellt

## 0.27.9 (2023-07-18)

- PHP-Wert memory_limit im Docker-Container auf -1 gesetzt
- phpinfo-Plugin ergänzt
- PHP 8 als Basis des Docker-Images verwendet
- PHP-Erweiterung opcache für bessere Leistung installiert

## 0.27.8 (2021-12-21)

- Dateien in der Dateiansicht alphabetisch sortiert

## 0.27.7 (2021-09-17)

- GD-Funktionen für die Teilliste wiederhergestellt

## 0.27.6 (2021-01-04)

- RelInfo-URL korrigiert

## 0.27.5 (2020-11-16)

- NEWS_URL und SERVERLIST_URL konfigurierbar gemacht
- GUI-Nachrichten von GitHub bezogen

## 0.27.4 (2020-10-01)

- Standardwert für error_reporting auf 0 gesetzt; über PHP_INI_ERROR_REPORTING änderbar
- Standardwert für display_errors auf Off gesetzt; über PHP_INI_DISPLAY_ERRORS änderbar

## 0.27.3 (2020-10-01)

- Auswahl eines Tabs im Permalink ermöglicht

## 0.27.2 (2020-09-23)

- Verwendung von Permalinks korrigiert

## 0.27.1 (2020-09-12)

- Erstellung von RelInfo-Links korrigiert

## 0.27.0 (2020-09-11)

- Vereinfachtes RelInfo-Symbol in den Ansichten für Downloads, Uploads, Shares und Suche wieder ergänzt
- Permalink in der oberen Leiste ergänzt
- HTTP-Dateien mit file_get_contents statt fsockopen geladen
- Code und Gestaltung von /index.php für bessere Lesbarkeit überarbeitet
- minigui entfernt

## 0.26.0 (2020-02-26)

- Umgebungsvariablen für Docker dokumentiert
- phpaj-Option savebw entfernt
- phpaj-Option autoclean für Downloads entfernt
- Nicht mehr benötigte phpaj-Optionen aus den Einstellungen entfernt
- Konfiguration der Fortschrittsbalken über Umgebungsvariablen ermöglicht
- Automatische Anmeldung im oberen Frame ermöglicht

## 0.25.5 (2020-01-27)

- Verarbeitung mehrerer Links ermöglicht

## 0.25.4 (2020-01-24)

- Linkexport für AJL und BB-Code korrigiert

## 0.25.3 (2019-12-16)

- Für alle Core-Anfragen von fsockopen auf das schnellere curl umgestellt

## 0.25.2 (2019-12-09)

- Pop-ups beim Linkexport entfernt

## 0.25.1 (2019-12-06)

- minigui für PHP 7.x korrigiert

## 0.25.0 (2019-07-30)

- Projekt in die Versionsverwaltung importiert
- Code mit PHP 7.x kompatibel gemacht
- Docker-Unterstützung ergänzt
- Veraltete Implementierung von appledocs entfernt
