<?php

namespace Drupal\degov\Behat\Context;




class SmokeTestContext extends DrupalContext {

  private $customParameters;

  public function __construct($parameters) {

    $this->customParameters = !empty($parameters) ? $parameters : [];

    parent::__construct();
  }

  public function loginByCustomParameters() {

    xdebug_break();

    // Check if logged in.
    if ($this->loggedIn()) {
      $this->logout();
    }

    $this->getSession()->visit($this->locatePath('/user'));
    $element = $this->getSession()->getPage();
    $element->fillField($this->getDrupalText('username_field'), $user->name);
    $element->fillField($this->getDrupalText('password_field'), $user->pass);
    $submit = $element->findButton($this->getDrupalText('log_in'));
    if (empty($submit)) {
      throw new \Exception(sprintf("No submit button at %s", $this->getSession()->getCurrentUrl()));
    }

    // Log in.
    $submit->click();

    if (!$this->loggedIn()) {
      if (isset($user->role)) {
        throw new \Exception(sprintf("Unable to determine if logged in because 'log_out' link cannot be found for user '%s' with role '%s'", $user->name, $user->role));
      }
      else {
        throw new \Exception(sprintf("Unable to determine if logged in because 'log_out' link cannot be found for user '%s'", $user->name));
      }
    }
  }

}
