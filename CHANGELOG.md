# Änderungsprotokoll

Alle wichtigen Änderungen an diesem Projekt werden in dieser Datei dokumentiert.

## 0.33.2 (2026-10-18)

### Downloads und Uploads
- Sortierung nach Geschwindigkeit
- Dateiname in Downloads öffnet die Quellenliste
- Quellenliste zeigt die Chunk-Größe
- Quellenliste zeigt, wie viel jede Quelle hat
- Uploads zeigen Wasserstand sowie OS-Symbol und Version
- Wasserstand im Upload zeigt den korrekten Prozentwert
- Upload-Karten: Symbole statt Textlabels, Dateiname ohne Umbruch
- Download-Sortierung mobil einzeilig mit Kurzlabels
- Fortschritt immer mit zwei Nachkommastellen, kein Flackern bei 10.3 %
- Upload-Karten: Dateiname nutzt die volle Kartenbreite

### Oberfläche
- Versionsnummer im Footer verlinkt auf die Releases
- Login zeigt die Version als Tag unter dem Fenster
- Firewall-Hinweis mit Warnsymbol, mobil einzeilig
- Schmalerer Footer
- Serverliste: „Zuletzt“ statt „letzte Verbindung“

### Betrieb
- Neue Variable `REPOSITORY_URL`; leer blendet die Links aus
- Leere optionale URLs deaktivieren nur ihre Funktion
- Umgebungsvariablen werden geprüft, ungültige Werte fallen auf Standard
- Assets mit ETag; Entwicklung prüft immer, Produktion cacht eine Stunde
- HTTP-Header ohne PHP-Version, Apache-Version und Betriebssystem

## 0.33.1 (2026-10-16)

### Oberfläche
- Dezenter Aktivitätspunkt bei API-Anfragen
- Bis 520 px: Seitensymbol statt Titel, Statuswerte bleiben sichtbar
- Stabile Breite der Down- und Up-Anzeige
- Rückmeldungen nennen die ausgeführte Aktion
- Aktualisierung standardmäßig alle 2 statt 5 Sekunden
- Icons an allen Buttons

### Downloads und Uploads
- Geschwindigkeitsbalken mit Kopfzeile synchronisiert
- Downloadkarten: Quellen, Status und PDL als Badges
- Geschwindigkeit und Restzeit daneben in derselben Zeile
- Uploadstatus rechts auf Höhe des Benutzernamens
- Enter im PDL-Feld übernimmt den Wert
- PDL-Zeile mobil einzeilig, kompakte Buttons

### Share
- „Share“ als Name in Navigation und Seitentitel
- Mobiler Schnellzugriff in unterer Leiste
- Shareprüfung abbrechbar ab Core 0.35.186.1
- Kompakte Prüfbuttons, mobil gleich breit
- Verzeichnisauswahl zeigt wieder Core-Ordner
- Incoming mit Badge; Entfernen in Oberfläche und API gesperrt
- Unterverzeichnis-Auswahl mobil in eigener Zeile
- Prioritätsbudget kompakt rechts in Tab-Leiste
- Link-Export: Kopieren in Zwischenablage, ohne Zeilenumbruch
- Export, Reload und Prio mobil in einer Zeile
- Statistik: Dateiname springt in den Share-Ordner

### Serverliste
- Einheitliche Kartenabstände und kürzere Beschriftungen
- Servername, Host und Verbindungszeit ohne Umbruch
- Status unter Servername, Aktionsbuttons gleich breit
- Hinweis zur freien Serverwahl ergänzt

### Smartphone-Eingaben
- Passende Enter-Tasten für Suche, Anmeldung und Eingaben
- Keine Autokorrektur oder Großschreibung bei Links und Pfaden
- Core-URL ebenfalls ohne Eingabekorrektur
- Markieren schreibgeschützter Links öffnet keine Tastatur

## 0.33.0 (2026-10-09)

### Oberfläche
- Vue.js-Oberfläche mit dynamischen Seitenwechseln
- Smartphone-Ansicht und Dunkelmodus verbessert
- Neuer Login-Hintergrund
- Kopfzeile mit Download, Upload, Credits und Verbindungen
- Schnellzugriff auf „Links hinzufügen“
- Sprache pro Browser umschaltbar: Deutsch oder Englisch
- `GUI_LANGUAGE` bleibt Standardsprache
- Addons-Menü und phpinfo entfernt

### Dashboard
- Getrennte Karten für Netzwerk und Community
- Community vor Netzwerk und Core-Informationen
- Vier Kacheln nebeneinander auf Smartphones
- Verbindungen mit konfiguriertem Maximum
- Verbindungsdauer oben rechts in Serverkarte
- Öffentliche IP maskiert, vollständig beim Darüberfahren

### Downloads, Uploads und Share
- Übersichtlichere Transfer- und Freigabenansichten
- Mehrfachauswahl mit Shift-Klick bei geteilten Dateien
- Upload-Dateiname öffnet Share und markiert Datei
- Share-Suche über alle Ordner und Unterordner
- Suche unabhängig von Groß- und Kleinschreibung
- Dateistatistik als eigener Tab
- Kopierbare ajfsp-Links, auch mit Quelle
- Scharf skalierbare Teileanzeige

### Suche und Links
- Geteilte und bereits ladende Suchtreffer markiert
- ajfsp-Link und Link mit Quelle pro Suchtreffer
- Zielverzeichnis und AJL-Import im Link-Dialog
- Login zeigt Anzahl der zu übernehmenden Links

### Installierte App und Laufzeit
- Roter Apfel als App- und Touch-Symbol
- `web+ajfsp`-Links und `.ajl`-Dateien direkt öffnen, außer unter iOS
- PHP 8.5 erforderlich
- Sicherheit und Verarbeitung großer Dateilisten verbessert

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
