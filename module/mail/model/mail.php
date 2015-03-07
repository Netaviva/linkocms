<?php

class Mail_Model_Mail extends Linko_Model
{
	const OPEN_DELIM = '{';

	const CLOSE_DELIM = '}';

	private $_aTo = array();

	private $_aAttachment = array();

	private $_aParam = array();

	private $_sSubject;

	private $_sMessage;

	public function __construct()
	{
		$sFromName = Linko::Module()->getSetting('mail.global_from_name');
		$sFromAddress = Linko::Module()->getSetting('mail.global_from_email');

		$this->_aFrom = array($sFromAddress => $sFromName);
	}

	public function load($sVar)
	{
		Linko::Cache()->set(array('mail', 'mail_' . $sVar));

		if(!$aRow = Linko::Cache()->read())
		{
			$aRow = Linko::Database()->table('mail')
				->select()
				->where('mail_var', '=', $sVar)
				->query()
				->fetchRow();

			Linko::Cache()->write($aRow);
		}

		if(isset($aRow['mail_id']))
		{
			$this->setSubject($aRow['mail_subject']);

			$this->setMessage($aRow['mail_body']);
		}

		return $this;
	}

	public function setTo($mAddress, $mName = null)
	{
		if(!is_array($mAddress))
		{
			if($mName)
			{
				$mAddress = array($mAddress => $mName);
			}
			else
			{
				$mAddress = array($mAddress);
			}
		}

		$this->_aTo = array_merge($this->_aTo, array_map('trim', $mAddress));

		return $this;
	}

	public function setSubject($sSubject)
	{
		$this->_sSubject = $sSubject;

		return $this;
	}

	public function setMessage($sMessage)
	{
		$this->_sMessage = $sMessage;

		return $this;
	}

	public function addAttachment($mFile)
	{

	}

	public function setParam($mVar, $sValue = null)
	{
		if(!is_array($mVar))
		{
			$mVar = array($mVar => $sValue);
		}

		foreach($mVar as $sVar => $sValue)
		{
			$this->_aParam[$sVar] = $sValue;
		}

		return $this;
	}

	public function getSubject()
	{
		return $this->decorate($this->_sSubject);
	}

	public function getMessage()
	{
		return $this->decorate($this->_sMessage);
	}

	public function send()
	{
		$sPath = Linko::Config()->get('dir.module') . 'mail' . DS . 'library' . DS;
		require_once $sPath . 'Swift' . DS . 'lib' . DS . 'swift_required.php';

		try
		{
			$aReplace = array(
				'<br />' => "\n",
				'<br>' => "\n"
			);

			// create message
			$oMessage = Swift_Message::newInstance()
				->setSubject($this->getSubject())
				->setFrom($this->_aFrom)
				->setTo($this->_aTo)
				->setBody(nl2br($this->getMessage()), 'text/html')
				->addPart(strip_tags(str_replace(array_keys($aReplace), array_values($aReplace), $this->getMessage())), 'text/plain');

			switch(Linko::Module()->getSetting('mail.mail_transport'))
			{
				case 'smtp':
					$sSMTPHost = Linko::Module()->getSetting('mail.smtp_host');
					$sSMTPPort = Linko::Module()->getSetting('mail.smtp_port');
					$sSMTPUsername = Linko::Module()->getSetting('mail.smtp_username');
					$sSMTPPassword = Linko::Module()->getSetting('mail.smtp_password');

					$oTransport = Swift_SmtpTransport::newInstance($sSMTPHost, $sSMTPPort)
						->setUsername($sSMTPUsername)
						->setPassword($sSMTPPassword);
					break;
				case 'mail':
				default:
					$oTransport = Swift_MailTransport::newInstance();
					break;
			}

			$oMailer = Swift_Mailer::newInstance($oTransport);

			// enable logger plugin
			// $oLogger = new Swift_Plugins_Loggers_ArrayLogger();
			// $oMailer->registerPlugin(new Swift_Plugins_LoggerPlugin($oLogger));

			$iSent = $oMailer->send($oMessage, $aFailed);
		}
		catch(Exception $e)
		{
			$iSent = 0;
		}

		// Arr::dump($oLogger->dump());
		// Arr::dump($aFailed);

		return $iSent;
	}

	public function reset()
	{
		$oMail = clone $this;

		$this->_aTo = $this->_aAttachment = $this->_aParam = array();

		$this->_sSubject = $this->_sMessage = null;

		return $oMail;
	}

	private function decorate($sData)
	{
		$aCurrentUser = Linko::Model('User/Auth')->getUser();

		$this->_aParam = array_merge(array(
			'site_title' => Linko::Module()->getSetting('page.site_title'),
			'site_url' => Linko::Url()->make(),
			'current_username' => $aCurrentUser['username'],
			'current_useremail' => $aCurrentUser['email'],
			'current_fullname' => Linko::Model('User')->getFullname($aCurrentUser),
			'signature' => Linko::Module()->getSetting('mail.signature')
		), $this->_aParam);

		$aParam = array();

		foreach($this->_aParam as $sVar => $sValue)
		{
			$aParam[self::OPEN_DELIM . $sVar . self::CLOSE_DELIM] = $sValue;
		}

		return str_replace(array_keys($aParam), array_values($aParam), $sData);
	}
}