<?php

namespace appleJuiceNETZ\UI;

class Language {
    private $languageCode;
    private $translations = [];

    // Statische Variable, um Instanzen zu speichern
    private static $instances = [];

    // Konstruktor, der den Sprachcode übernimmt und die entsprechende JSON-Datei lädt
    private function __construct($languageCode) {
        $this->languageCode = $languageCode;
        $this->loadLanguageFile($languageCode);
    }

    // Verhindert das Kopieren oder die Duplikation der Instanz
    public function __clone() {}
    public function __wakeup() {}

    // Gibt die Instanz der Language-Klasse zurück (Singleton-Pattern)
    public static function getLanguage(): Language {
        // Überprüft, ob bereits eine Instanz existiert
        if (!isset(self::$instances[Language::class])) {
            // Wenn nicht, erstelle eine neue Instanz und speichere sie
            if (isset($_ENV['GUI_LANGUAGE'])) {
                self::$instances[Language::class] = new Language($_ENV['GUI_LANGUAGE']);
            } else {
                // Falls keine Sprache gesetzt ist, kannst du hier einen Standardwert verwenden
                self::$instances[Language::class] = new Language('en');
            }
        }

        return self::$instances[Language::class];
    }

    // Lädt die Sprachdatei im JSON-Format für den angegebenen Sprachcode
    private function loadLanguageFile($languageCode) {
        $filePath = GUI_ROOT . "src/Local/{$languageCode}.json";

        if (file_exists($filePath)) {
            $jsonContent = file_get_contents($filePath);
            $this->translations = json_decode($jsonContent, true);
        } else {
            echo "Warnung: Sprachdatei für '$languageCode' nicht gefunden. Fallback auf Englisch.";
            $this->translations = json_decode(file_get_contents(GUI_ROOT . "src/Local/en.json"), true);
        }
    }

    // Gibt die Übersetzung für einen bestimmten Schlüssel zurück
    public function translate($key) {
        $keys = explode('.', $key);

        $translation = $this->translations;
        foreach ($keys as $subkey) {
            if (isset($translation[$subkey])) {
                $translation = $translation[$subkey];
            } else {
                return $key; // Fallback: Gebe den Originalschlüssel zurück
            }
        }

        return $translation;
    }

    // Gibt den aktuellen Sprachcode zurück
    public function getLanguageCode() {
        return $this->languageCode;
    }
}


