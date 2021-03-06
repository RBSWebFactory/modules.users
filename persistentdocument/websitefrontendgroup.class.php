<?php
/**
 * users_persistentdocument_websitefrontendgroup
 * @package modules.users
 */
class users_persistentdocument_websitefrontendgroup extends users_persistentdocument_websitefrontendgroupbase
{
	/**
	 * @return website_persistentdocument_website[]
	 */
	public function getWebsites()
	{
		$websites = $this->getLinkedwebsitesArray();
		$websites[] = website_persistentdocument_website::getInstanceById($this->getWebsiteid());
		return $websites;
	}
}