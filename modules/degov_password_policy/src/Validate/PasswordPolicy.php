<?php

declare(strict_types = 1);

namespace Drupal\degov_password_policy\Validate;

use Drupal\Component\Render\FormattableMarkup;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use function count;
use function preg_match;
use function strlen;

/**
 * Class PasswordPolicy.
 *
 * @package Drupal\degov_password_policy\Validate
 */
class PasswordPolicy implements ValidateInterface {

  /**
   * @inheritdoc
   */
  public static function validate(array $form, FormStateInterface $formState): void {
    $value = $formState->getValue('pass');

    // Skip empty field.
    if ($value === '') {
      return;
    }

    $errors = [];
    if (strlen($value) < 12) {
      $errors[] = new TranslatableMarkup('Password should be at least 12 characters long');
    }
    if (!preg_match('/\d/', $value)) {
      $errors[] = new TranslatableMarkup('Password should contain at least one digit');
    }
    if (!preg_match('/[A-Z]/', $value)) {
      $errors[] = new TranslatableMarkup('Password should contain at least one upper-case letter');
    }
    if (!preg_match('/[a-z]/', $value)) {
      $errors[] = new TranslatableMarkup('Password should contain at least one lower-case letter');
    }
    if (!preg_match('/[!@#$%\/.,*()\[\]]/', $value)) {
      $errors[] = new TranslatableMarkup('Password should contain at least one special character');
    }

    if (count($errors)) {
      $errorString = '<ul>';
      foreach ($errors as $error) {
        $errorString .= "<li>$error</li>";
      }
      $errorString .= '</ul>';
      $formState->setError($form['account']['pass'], new FormattableMarkup($errorString, $errors));
    }
  }

}
