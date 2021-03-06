<?php
class users_Setup extends object_InitDataSetup
{
	public function install()
	{
		try
		{
			$scriptReader = import_ScriptReader::getInstance();
       	 	$scriptReader->executeModuleScript('users', 'init.xml');
       	 	$scriptReader->executeModuleScript('users', 'resetpasswordnotif.xml');
       	 	
       	 	$this->getTransactionManager()->beginTransaction();
    
       	 	$wwwadmin = users_UserService::getInstance()->getBackEndUserByLogin('wwwadmin');
       	 	$wwwadmin->setPasswordmd5(null);
       	 	$wwwadmin->setEmail(null);
       	 	$this->getPersistentProvider()->updateDocument($wwwadmin);
       	 	
       	 	$this->getTransactionManager()->commit();
		}
		catch (Exception $e)
		{
			echo "ERROR: " . $e->getMessage() . "\n";
			Framework::exception($e);
		}
	}
}