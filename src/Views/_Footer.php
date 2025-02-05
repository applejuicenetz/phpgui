<script src="<?php echo WEBUI_THEME; ?>vendors/@coreui/coreui/js/coreui.bundle.min.js"></script>
    <script src="<?php echo WEBUI_THEME; ?>vendors/simplebar/js/simplebar.min.js"></script>
    <script>
      const header = document.querySelector('header.header');

      document.addEventListener('scroll', () => {
        if (header) {
          header.classList.toggle('shadow-sm', document.documentElement.scrollTop > 0);
        }
      });
    </script>
   <script>
document.addEventListener('DOMContentLoaded', function () {
    let refreshInterval;

    // Starte den automatischen Refresh
    startRefresh();

    // Funktion, um den Refresh zu starten
    function startRefresh() {
        // Falls bereits ein Intervall läuft, stoppen wir es, um ein mehrfaches Intervall zu verhindern
        if (refreshInterval) {
            clearInterval(refreshInterval);
        }

        refreshInterval = setInterval(function () {
            let urlParams = new URLSearchParams(window.location.search);
            let currentPage = urlParams.get('site'); // currentPage = Wert des Parameters 'site'

            // Überprüfen, ob der Parameter 'site' gesetzt ist, andernfalls eine Standardseite verwenden
            if (!currentPage) {
                currentPage = 'Dashboard'; // Standardseite, wenn kein Parameter übergeben wird
            }

            // Alle URL-Parameter extrahieren und in einen Query-String umwandeln
            const queryString = urlParams.toString();

            // Holen und Einfügen des Inhalts für die angegebene Seite
            fetch('_ContentLoaded.php?' + queryString) // Hole den Inhalt basierend auf allen URL-Parametern
                .then(response => response.text()) // Hole den Text der Antwort
                .then(data => {
                    document.getElementById('pagereload').innerHTML = data; // Setze den Inhalt in das "pagereload"-Element
                })
                .catch(error => {
                    console.error('Fehler beim Laden der Seite:', error);
                });
        }, 1000); // Wiederhole alle 1000 ms (1 Sekunde)
    }

    // Funktion zum Stoppen des Refresh
    function stopRefresh() {
        if (refreshInterval) {
            clearInterval(refreshInterval); // Stoppt das Intervall
            refreshInterval = null; // Setze das Intervall auf null
        }
    }

    // Stoppe das Refresh, wenn der Bildschirm berührt wird
    document.body.addEventListener('touchstart', function () {
        stopRefresh();
        console.log('Refresh gestoppt aufgrund des Touchscreen-Touchs.');
    });

    // Überwache alle Checkboxen mit der Klasse 'form-check-input' und stoppe den Refresh, wenn eine aktiviert oder deaktiviert wird
    const checkboxes = document.querySelectorAll('.form-check-input'); // Alle Checkboxen mit der Klasse 'form-check-input'

    checkboxes.forEach(function (checkbox) {
        // Füge einen 'change'-Listener hinzu, um den Refresh zu stoppen, wenn die Checkbox geändert wird
        checkbox.addEventListener('change', function () {
            stopRefresh();
            console.log('Refresh gestoppt aufgrund der Checkbox-Aktion.');

            // Starte den Refresh, wenn die Checkbox wieder deaktiviert wird
            if (!checkbox.checked) {
                startRefresh();
                console.log('Refresh gestartet, da die Checkbox wieder deaktiviert wurde.');
            }
        });
    });

    // Überwache alle Input-Felder und stoppe den Refresh bei Fokus und starte ihn bei Verlassen
    document.body.addEventListener('focusin', function (event) {
        if (event.target.classList.contains('form-control')) {
            stopRefresh();
            console.log('Refresh gestoppt aufgrund des Fokus auf einem Input-Feld.');
        }
    }, true);

    document.body.addEventListener('focusout', function (event) {
        if (event.target.classList.contains('form-control')) {
            startRefresh();
            console.log('Refresh gestartet nach Verlassen des Input-Feldes.');
        }
    }, true);

    // Funktion zum Aktivieren/Deaktivieren der Checkbox per JavaScript (mit Stopp des Refresh)
    window.change = function(id) {
        var dl_zeile = document.getElementById('zeile_' + id);
        var zelle = dl_zeile.firstChild;

        if (dl_ids[id] == 1) {
            dl_ids[id] = 0;
            document.dl_form.pdl.value = '1';

            // Checkbox deaktivieren
            document.getElementById('dlcheck_' + id).checked = false;
        } else {
            dl_ids[id] = 1;
            document.dl_form.pdl.value = dl_pdl[id];

            // Checkbox aktivieren
            document.getElementById('dlcheck_' + id).checked = true;
        }

        // Stoppe den Refresh, wenn eine Checkbox per JavaScript geändert wird
        stopRefresh();
        console.log('Refresh gestoppt aufgrund der Checkbox-Aktion per JavaScript.');

        // Starte den Refresh, wenn die Checkbox wieder deaktiviert wird
        if (!document.getElementById('dlcheck_' + id).checked) {
            startRefresh();
            console.log('Refresh gestartet, da die Checkbox per JavaScript deaktiviert wurde.');
        }
    };
});


    </script>
  </body>
</html>