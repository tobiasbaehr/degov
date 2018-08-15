Behat Testing
-------------

### Setup

Folgende Binärdateien werden benötigt:
* Chromedriver für das jeweilige Betriebssystem [herunterladen](https://sites.google.com/a/chromium.org/chromedriver/downloads).
* Behat: https://github.com/Behat/Behat (wird per Composer geladen)

Die *composer.json* Datei anpassen, sodass die *composer.json* Datei mit den Behat Abhängigkeiten geladen wird.

#### Auf Host Maschine Bash Alias einrichten
```
alias chromedriver-start='~/Dev/chromedriver --url-base=wd/hub --port=4444 --whitelisted-ips=""'
```

#### Composer Abhängigkeiten und Autoloading anpassen
In `composer.json` Datei des Projektes die `composer.json` Datei für die Abhängigkeiten der Behat Tests inkludieren. 
Nur die eine Zeile
innerhalb des `include` Blocks eintragen.

```
    "merge-plugin": {
        "merge-extra": true,
        "merge-extra-deep": true,
        "include": [
            
            ...
            
            "testing/behat/composer.json"
        ]
    }
```

In der `composer.json` Datei das Autoloading der `Context` Klassen einstellen:
```
  "autoload-dev": {
    "psr-4": {
      "Drupal\\Tests\\Behat\\Context\\": "testing/behat/context"
    }
  }
```
### Netzwerk IP der Host Maschine in behat.yml Datei eintragen
```
  ifconfig
```
Zeigt die IPs an. Es ist für gewöhnlich die erste IP in der Aufzählung.


### Ausführen der Tests
Tests direkt in Gast-System über Behat Kommando ausführen (aus dem *project* Verzeichnis):
```
bin/behat
```

_Tipp:_ Mit dem `--strict` Parameter werden sämtliche Errors ausgegeben. Optional kann der Pfad
zu einer Config Datei übergeben werden.
```
./behat -vvv --strict --config /var/www/project/docroot/behat.yml
```

## Troubleshooting

### Chrome DevTools während der Testausführung benutzen
Dazu muss man in der Host-Maschine während des Tests folgendes Kommando ausführen:
```
killall php
```
Damit stoppt der Test und man sieht den aktuellen Stand im Browser und kann die Chrome DevTools benutzen. Ansonsten
stürzt der Browser ab.

### Chrome Browser startet nicht
* Kontrollieren ob die IP des Host-Systems in der `behat.yml` Datei stimmt (Schlüssel wd_host`). Wenn man in ein anderes Netzwerk wechselt (z.B. im Homeoffice),
  dann ändert sich die IP der Host-Maschine und muss entsprechend angepasst werden.

### Fehlermeldung
```
  [Behat\Behat\Context\Exception\ContextNotFoundException]       
  `FeatureContext` context class not found and can not be used.  
                                                                
```
Hierbei prüfen ob die `behat.yml` Datei im `project` und nicht im `docroot` Verzeichnis liegt. Den Befehl `bin/behat` aus dem
`project` Verzeichnis heraus ausführen. Die Pfade innerhalb der `behat.yml` bei folgenden Zeilen prüfen: 
* features: '%paths.base%/docroot/modules/custom/im_testing/tests/src/Behat/Features'
* drupal_root: '%paths.base%/docroot'

### Quellen für Hintergrundinfos
* Drupal Extension für Behat und Mink: http://behat-drupal-extension.readthedocs.io/en/3.1/index.html
* Behat Projekt: http://behat.org/en/latest/
* Chromedriver: https://sites.google.com/a/chromium.org/chromedriver/downloads
