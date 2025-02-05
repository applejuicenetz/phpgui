<?php

// app.json einlesen
$AppJson = file_get_contents(__DIR__ . '/data/json/app.json');
$App = json_decode($AppJson, true);

// Überprüfen, ob das Array erfolgreich geladen wurde
if (json_last_error() === JSON_ERROR_NONE) {
    // Iteriere durch das Array und definiere Konstanten für alle Werte
    foreach ($App as $key => $value) {
        // Falls der Wert ein Array ist, dann iteriere weiter durch die inneren Arrays
        if (is_array($value)) {
            foreach ($value as $subKey => $subValue) {
                $constantName = strtoupper($key . '_' . $subKey); // Konstante benennen
                define($constantName, $subValue); // Konstante definieren
            }
        } else {
            // Falls der Wert kein Array ist, einfach die Konstante definieren
            $constantName = strtoupper($key); // Konstante benennen
            define($constantName, $value); // Konstante definieren
        }
    }

    // Beispiel: Zugriff auf die definierten Konstanten
    // echo "WebUI: " . WEBUI;
    // echo "Another constant: " . ANOTHER_CONSTANT;
} else {
    // Fehlerbehandlung, falls das JSON nicht erfolgreich geladen wurde
    echo "Fehler beim Laden der JSON-Datei!";
}

