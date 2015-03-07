<?php

defined('LINKO') or exit();

/**
 * @author Morrison Laju
 * @package linkocms
 * @subpackage stat : model - stat.php
 * @version 1.0.0
 * @copyright Copyright (c) 2013. All Rights Reserved.
 */
class Stat_Model_Stat extends Linko_Model
{
    private $_aPref = array();

    public function __construct()
    {
        $this->_sRefKey = substr(md5('module.stat'), 6);

        if(($sJson = urldecode(Linko::Cookie()->get($this->_sRefKey))) && $sJson != null)
        {
            $this->_aPref = json_decode($sJson, true);
        }
    }

    public function track()
    {
        if(!$this->_getPref('stat_user_hit'))
        {
            $this->_setPref('stat_user_hit', Date::now());
        }
        
        $oRow = Linko::Database()->table('stat')
            ->select()
            ->where('stat_year', '=', Date::now('Y'))
            ->where('stat_month', '=', Date::now('m'))
            ->where('stat_day', '=', Date::now('d'))
            ->query();
                        
        if($oRow->getCount())
        {
            $aRow = $oRow->fetchRow();
            
            Linko::Database()->table('stat')
                ->update(array(
                    'total_page_hits' => 'inc(1)'
                ))
                ->where('stat_id', '=', $aRow['stat_id'])
                ->query();
               
            if(!$this->_getPref('stat_unique_hit') && $this->_getPref('stat_user_hit'))
            {
                $this->_setPref('stat_unique_hit', Date::now());
                
                Linko::Database()->table('stat')
                    ->update(array(
                        'unique_page_hits' => 'inc(1)',
                    ))
                    ->where('stat_id', '=', $aRow['stat_id'])
                    ->query();
            }
        }
        else
        {
            Linko::Database()->table('stat')
                ->insert(array(
                    'stat_year' => Date::now('Y'),
                    'stat_month' => Date::now('m'),
                    'stat_day' => Date::now('d'),
                    'total_page_hits' => 1,
                    'unique_page_hits' => 1,
                    'total_returning_hits' => 0,
                ))->query();
        }
    }
    
    public function getStatChartData()
    {
        $aRows = Linko::Database()->table('stat')
            ->select()
            ->query()
            ->fetchRows();
         
        $aImpressions = array();
        $aUnique = array();
           
        foreach($aRows as $aRow)
        {
            $aImpressions[] = array(($aRow['stat_year'] . '-' . $aRow['stat_month'] . '-' . $aRow['stat_day']), (int)$aRow['total_page_hits']);
            
            $aUnique[] = array(($aRow['stat_year'] . '-' . $aRow['stat_month'] . '-' . $aRow['stat_day']), (int)$aRow['unique_page_hits']);
        }
        
        return array($aImpressions, $aUnique);
    }
    
    public function getTotalHits()
    {
        return Linko::Database()->table('stat')
            ->select('SUM("total_page_hits") as total_hits')
            ->query()
            ->fetchValue();
    }
    
    public function getUniqueHits()
    {
        return Linko::Database()->table('stat')
            ->select('SUM("unique_page_hits") as unique_hits')
            ->query()
            ->fetchValue();
    }

    private function _setPref($sVar, $sValue)
    {
        $this->_aPref[$sVar] = $sValue;

        Linko::Cookie()->set($this->_sRefKey, urldecode(json_encode($this->_aPref)));
    }

    private function _getPref($sVar)
    {
        return isset($this->_aPref[$sVar]) ? $this->_aPref[$sVar] : null;
    }

    private function _deletePref($sVar)
    {
        unset($this->_aPref[$sVar]);

        Linko::Cookie()->set($this->_sRefKey, urldecode(json_encode($this->_aPref)));
    }
}

?>