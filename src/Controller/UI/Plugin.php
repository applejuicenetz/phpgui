<?php

namespace appleJuiceNETZ\UI;

use appleJuiceNETZ\UI\Language;

class Plugin
{
    public static function list_plugins_with_info($plugin_dir)
    {
        $language = Language::getLanguage();
        
        // Überprüfen, ob der Ordner existiert
        if (!is_dir($plugin_dir)) {
            echo "Der angegebene Ordner existiert nicht.";
            return;
        }
        
        // Öffnen des Ordners und alle Dateien durchsuchen
        $plugins = scandir($plugin_dir);
        
        // Filtern von Plugins, die eine 'info.json' Datei haben und PHP-Dateien sind
        $plugin_files = array_filter($plugins, function($plugin) use ($plugin_dir) {
            // Überprüfen, ob die 'info.json' Datei existiert
            $info_file = $plugin_dir . '/' . $plugin . '/data/info.json';
            return is_dir($plugin_dir . '/' . $plugin) && file_exists($info_file);
        });
        
        // Überprüfen, ob Plugins gefunden wurden
        if (count($plugin_files) > 0) {
            echo '<div class="row row-cols-1 row-cols-md-3 g-4">';
            // Iteriere durch jedes Plugin und lese die info.json
            foreach ($plugin_files as $plugin) {
                $info_file = $plugin_dir . '/' . $plugin . '/data/info.json';
                
                // info.json-Datei einlesen und dekodieren
                $plugin_info = json_decode(file_get_contents($info_file), true);
                
                // Überprüfen, ob die info.json gültig ist
                if ($plugin_info !== null) {
                    // Ausgabe der Plugin-Informationen
                    $plugin_name = isset($plugin_info['name']) ? $plugin_info['name'] : 'Unbekannt';
                    $plugin_shortname = isset($plugin_info['shortname']) ? $plugin_info['shortname'] : 'Unbekannt';
                    $plugin_version = isset($plugin_info['version']) ? $plugin_info['version'] : 'Nicht verfügbar';
                    $plugin_description = isset($plugin_info['description']) ? $plugin_info['description'] : 'Keine Beschreibung verfügbar';
                    $plugin_image = isset($plugin_info['image']) ? $plugin_info['image'] : '';
                    
                    // Link zum Plugin
                    $plugin_url = "?site=Plugins&plugin={$plugin_name}";  // Beispiel-Link, passe ihn an
                    
                    echo '<div class="col">
                            <div class="card h-100">
                                <img src="plugins/' . $plugin_shortname . '/images/' . $plugin_image . '" class="card-img-top" alt="' . $plugin_name . '">
                                <div class="card-body">
                                    <h5 class="card-title">' . $plugin_name . '<span class="position-absolute badge rounded-pill bg-info">
                                    ' . $plugin_version . '
                                    <span class="visually-hidden">' . $plugin_version . '</span> </h5>
                                    <p class="card-text">' . $plugin_description . '</p>
                                </div>
                                <div class="card-footer text-end">
                                    <button type="button" class="btn btn-primary" onclick="location.href=\'?site=Extras&plugin=' . $plugin_shortname . '\'" style="--cui-btn-padding-y: .25rem; --cui-btn-padding-x: .5rem; --cui-btn-font-size: .75rem;">
                                    ' . $language->translate('plugins.view') . '
                                    </button>
                                </div>
                            </div>
                        </div>';
                }
            }
            echo "</div>";
        } else {
            echo "Keine Plugins gefunden.";
        }
    }
    
    public static function show_plugin($plugin_shortname, $plugin_dir = 'plugins')
    {
        // Prüfen, ob der Plugin-Ordner existiert
        if (!is_dir($plugin_dir)) {
            echo "Der angegebene Ordner existiert nicht.";
            return;
        }
        
        // Der Pfad zur info.json-Datei des Plugins
        $plugin_folder = $plugin_dir . '/' . $plugin_shortname;
        $info_file = $plugin_folder . '/data/info.json';
        
        // Prüfen, ob das Plugin existiert und eine info.json hat
        if (is_dir($plugin_folder) && file_exists($info_file)) {
            // info.json-Datei einlesen und dekodieren
            $plugin_info = json_decode(file_get_contents($info_file), true);
            
            // Überprüfen, ob die info.json gültig ist
            if ($plugin_info !== null) {
                // Plugin-Details anzeigen
                $plugin_name = isset($plugin_info['name']) ? $plugin_info['name'] : 'Unbekannt';
                $plugin_version = isset($plugin_info['version']) ? $plugin_info['version'] : 'Nicht verfügbar';
                $plugin_description = isset($plugin_info['description']) ? $plugin_info['description'] : 'Keine Beschreibung verfügbar';
                $plugin_image = isset($plugin_info['image']) ? $plugin_info['image'] : '';
                
                echo'<div class="card mb-2">
  <div class="card-body">
    <h2>' . $plugin_name . '</h2>
    <p>' . $plugin_description . '</p>
  </div></div>
';
                
                require_once $plugin_folder.'/index.php';
                
                echo '';
            } else {
                echo "Die Plugin-Info ist ungültig.";
            }
        } else {
            echo "Das angeforderte Plugin existiert nicht.";
        }
    }
}
