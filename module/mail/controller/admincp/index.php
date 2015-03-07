<?php

class Mail_Controller_Admincp_Index extends Linko_Controller
{
	public function main()
	{
		$aMails = Linko::Model('Mail/Browse')->getMails();

		if($aEmails = Input::post('email'))
		{
			if(Linko::Model('Mail/Action')->updateEmails($aEmails))
			{
				Linko::Flash()->success('Mails Updated.');
				Linko::Response()->redirect('self');
			}
		}

		Linko::Template()
			->setTitle('Mail Manager')
			->setBreadcrumb(array(

			), 'Mail Manager')
			->setVars(array(
			'aMails' => $aMails
		));
	}
}