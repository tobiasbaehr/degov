<?php

namespace degov\Scripts\Robo\Model;


class InstallationProfileCollection {

  /**
   * @var InstallationProfile
   */
  private $mainInstallationProfile;

  /**
   * @var InstallationProfile
   */
  private $subInstallationProfile;

  /**
   * @return InstallationProfile
   */
  public function getMainInstallationProfile(): InstallationProfile {
    return $this->mainInstallationProfile;
  }

  /**
   * @return InstallationProfile
   */
  public function getSubInstallationProfile(): InstallationProfile {
    return $this->subInstallationProfile;
  }

  /**
   * @param InstallationProfile $subInstallationProfile
   */
  public function setSubInstallationProfile(InstallationProfile $subInstallationProfile): void {
    $this->subInstallationProfile = $subInstallationProfile;
  }

  /**
   * @param InstallationProfile $mainInstallationProfile
   */
  public function setMainInstallationProfile(InstallationProfile $mainInstallationProfile): void {
    $this->mainInstallationProfile = $mainInstallationProfile;
  }

}