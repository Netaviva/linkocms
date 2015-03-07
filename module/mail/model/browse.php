<?php

class Mail_Model_Browse extends Linko_Model
{
	public function getMails()
	{
		Linko::Cache()->set(array('mail', 'mails'));

		if(!$aMails = Linko::Cache()->read())
		{
			$aMails = Linko::Database()->table('mail')
				->select()
				->query()
				->fetchRows();

			Linko::Cache()->write($aMails);
		}

		return $aMails;
	}
}