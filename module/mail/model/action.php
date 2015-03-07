<?php

class Mail_Model_Action extends Linko_Model
{
	public function add($aVals, $iId = null)
	{
		$bUpdate = false;

		if($iId)
		{
			$bUpdate = true;
		}

		if(!$bUpdate)
		{
			if(!Arr::hasKeys($aVals, 'var', 'subject', 'body'))
			{
				return false;
			}
		}

		if($bUpdate)
		{
			$aData = array();

			foreach(array('var', 'subject', 'body', 'title', 'description') as $sField)
			{
				if(isset($aVals[$sField]))
				{
					$aData['mail_' . $sField] = $aVals[$sField];
				}
			}

			if(isset($aVals['param']))
			{
				$aData['param'] = serialize($aVals['param']);
			}

			Linko::Database()->table('mail')
				->update($aData)
				->where('mail_id', '=', $iId)
				->query();

			Linko::Plugin()->call('mail.update_mail', $iId);

			return $iId;
		}
		else
		{
			$aData = array(
				'mail_var' => $aVals['var'],
				'mail_subject' => $aVals['subject'],
				'mail_body' => $aVals['body'],
				'mail_title' => isset($aVals['title']) ? $aVals['title'] : NULL,
				'mail_description' => isset($aVals['description']) ? $aVals['description'] : NULL,
				'param' => isset($aVals['param']) && is_array($aVals['param']) ? serialize($aVals['param']) : null
			);

			$iId = Linko::Database()->table('mail')
				->insert($aData)
				->query()
				->getInsertId();

			Linko::Plugin()->call('mail.update_mail', $iId);

			return $iId;
		}
	}

	public function update($iId, $aVals)
	{
		return $this->add($aVals, $iId);
	}

	public function delete($iId)
	{
		return Linko::Database()->table('mail')
			->delete()
			->where('mail_id', '=', $iId)
			->query()
			->getAffectedRows();
	}

	public function updateEmails($aVals)
	{
		foreach($aVals as $sVar => $aVal)
		{
			if(is_null($aVal['subject']) || is_null($aVal['body']))
			{
				return;
			}

			Linko::Database()->table('mail')
				->update(array(
					'mail_subject' => $aVal['subject'],
					'mail_body' => $aVal['body']
				))
				->where('mail_var', '=', $sVar)
				->query();
		}

		return true;
	}
}