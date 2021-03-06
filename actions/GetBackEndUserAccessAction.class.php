<?php

class users_GetBackEndUserAccessAction extends f_action_BaseJSONAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$result = array();
		$user = users_UserService::getInstance()->getCurrentBackEndUser();
		if ($user)
		{
			$result['userinfos'] = array('fullname' => $user->getFullname(),
										 'login' => $user->getLogin(),
										 'email' => $user->getEmail(),
									     'id' => $user->getId(),
										 'root' => $user->getIsroot());

			$result['userPreferences'] = ($user->hasMeta('userPreferences')) ? JsonService::getInstance()->decode($user->getMeta('userPreferences')) : null;
			
			$fullAccess = $user->getIsroot();
			
			foreach (ModuleService::getInstance()->getModulesObj() as $cModule)
			{
				$packageName = $cModule->getFullName();
				$moduleName = $cModule->getName();
				$version = $cModule->getVersion();
				$enabled = $cModule->isEnabled();
				$rootFolderId = $cModule->getRootFolderId();
				$visible = $cModule->isVisible();
	
				if ($visible)
				{
					$menu = $cModule->getCategory();
					$access = $fullAccess || f_permission_PermissionService::getInstance()->hasPermission($user, $packageName . '.Enabled', $rootFolderId);
					$list = $fullAccess || f_permission_PermissionService::getInstance()->hasPermission($user, $packageName . '.List.rootfolder', $rootFolderId);	
				}
				else
				{
					$access = false;
					$list = $fullAccess || f_permission_PermissionService::getInstance()->hasPermission($user, $packageName . '.List.rootfolder', $rootFolderId);
					$menu = '';
				}

				$result[$moduleName] = array('rootfolderid' => $rootFolderId, 
											 'enabled' => $enabled, 'visible' => $visible, 'menu' => $menu,
											 'access' => $access, 'list' => $list, 'version' => $version);	
			}
		}
		return $this->sendJSON($result);
	}
}