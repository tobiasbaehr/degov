<?php

namespace Drupal\degov_search_content_solr;

class InvalidContentBundleMachineNameException extends \Exception {

	public function __construct(string $contentBundleMachineName) {
		parent::__construct($contentBundleMachineName);
		$this->message = 'Got the following content bundle machine name, which led to none entity query result: ' . $contentBundleMachineName;
	}

}