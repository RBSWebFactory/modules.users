<?php
/**
 * users_BlockEditFrontendUserProfileAction
 * @package modules.users.lib.blocks
 */
class users_BlockEditFrontendUserProfileAction extends website_BlockAction
{
	/**
	 * @see website_BlockAction::execute()
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @return String
	 */
	public function execute($request, $response)
	{
		if ($this->isInBackoffice())
		{
			return website_BlockView::INPUT;
		}

		$user = users_UserService::getInstance()->getCurrentFrontEndUser();
		$request->setAttribute('user', $user);

		return website_BlockView::INPUT;
	}

	/**
	 * @return boolean
	 */
	public function saveNeedTransaction()
	{
		return true;
	}
	
	/**
	 * @return string[]
	 */
	public function getUserBeanExclude()
	{
		return array('title');
	}

	/**
	 * @param f_mvc_Request $request
	 * @param f_mvc_Response $response
	 * @param users_persistentdocument_websitefrontenduser $user
	 * @return String
	 */
	public function executeSave($request, $response, users_persistentdocument_websitefrontenduser $user)
	{
		if ($user->getLogin() === null)
		{
			$user->setLogin($user->getEmail());
		}
		$user->save();
		$request->setAttribute('user', $user);
	
		//TODO: Email confirmation.
		//$user->getDocumentService()->sendEmailConfirmationMessage($user, false);

		$this->addMessage(LocaleService::getInstance()->transFO('m.users.frontoffice.informations-updated', array('ucf', 'html')));

		return website_BlockView::INPUT;
	}

	/**
	 * @param f_mvc_Request $request
	 * @param users_persistentdocument_websitefrontenduser $user
	 */
	public function validateSaveInput($request, $user)
	{
		$includedFields = array('email', 'titleid', 'firstname', 'lastname');
		$validationRules = BeanUtils::getBeanValidationRules('users_persistentdocument_websitefrontenduser', $includedFields);
		$isOk = $this->processValidationRules($validationRules, $request, $user);

		// Login validation.
		if ($user->isPropertyModified('login'))
		{
			$website = website_WebsiteModuleService::getInstance()->getCurrentWebsite();
			$login = ($request->hasParameter('login')) ? $request->getParameter('login') : $request->getParameter('email');
			if (in_array($login, users_ModuleService::getInstance()->getDisallowedLogins()))
			{
				$this->addError(LocaleService::getInstance()->transFO('m.users.frontoffice.login-disallowed', array('ucf', 'html')));
				$isOk = false;
			}
			else if (users_UserService::getInstance()->getFrontendUserByLogin($login, $website->getId()))
			{
				$this->addError(LocaleService::getInstance()->transFO('m.users.frontoffice.login-used', array('ucf', 'html')));
				$isOk = false;
			}
		}
		return $isOk;
	}
}