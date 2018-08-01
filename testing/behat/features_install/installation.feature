Feature: Installation via webbrowser

  Scenario: I want to install nrwGov via webbrowser
    Given I am on "/core/install.php"
    Then I should not see text matching "Error"
    And I should not see text matching "Warning"
    Then task "Sprache auswählen" is done
    And task "Systemvoraussetzungen überprüfen" is done
    Then I should see text matching "Datenbankkonfiguration" after a while
    Then I fill in "edit-mysql-database" with "testing"
    Then I fill in "edit-mysql-username" with "root"
    Then I fill in "edit-mysql-password" with "testing"
    Then I submit the form
    And task "Datenbank einrichten" is done
    And task "Website installieren" is done
    And task "Übersetzungen konfigurieren" is done
    Then I should see text matching "WEBSITE-INFORMATIONEN" after a while
    And I fill in "site_name" with "Some site name"
    And I fill in "site_mail" with "site@example.com"
    And I fill in "edit-account-name" with "admin"
    And I fill in "edit-account-pass-pass1" with "password"
    And I fill in "edit-account-pass-pass2" with "password"
    And I fill in "edit-account-mail" with "admin@example.com"
    And I submit the form
    Then I should not see text matching "Es wurde eine nicht erlaubte Auswahl entdeckt." after a while
    And I should not see text matching "The import failed due for the following reasons:"
    And task "Website konfigurieren" is done
    And I should not see text matching "The import failed due for the following reasons:"
    And task "Install deGov - Base" is done
    And I should not see text matching "The import failed due for the following reasons:"
    And task "Install deGov - Media" is done
    And I should not see text matching "The import failed due for the following reasons:"
    And task "Install deGov - Theme" is done
    And I should not see text matching "The import failed due for the following reasons:"
    And I should not see text matching "Error"
    And I should not see text matching "Warning"
    Then I should see text matching "deGov wurde erfolgreich installiert." after a while
    And I should not see text matching "The import failed due for the following reasons:"
    And I should not see text matching "Error"
    And I should not see text matching "Warning"