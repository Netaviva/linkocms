<?php

class Activity_Model_Browse extends Linko_Model
{
    /**
     * @var int pagination type
     */
    private $_iPaginationType = 1;

    private $_bFilter = true;

    /**
     * @var int User Id
     */
    private $_iUser = null;

    private $_aResult = array();

    private $_iTotal = 0;

    private $_iLimit = 10;

    /**
     * @var int current page number
     */
    private $_iPage = 0;

    /**
     * @var object
     */
    private $_oQuery;

    public function __construct()
    {
        $this->_iLimit = Linko::Module()->getSetting('activity.page_limit');
    }

    public function setUser($iUser)
    {
        $this->_iUser = $iUser;

        return $this;
    }

    public function setLimit($iLimit)
    {
        $this->_iLimit = $iLimit;

        return $this;
    }

    /**
     * method to set cuurrent page
     * @param int
     * @return $this
     */
    public  function setPage($iPage)
    {
        $this->_iPage = $iPage;

        return $this;
    }

    public function setFilter($bFilter)
    {
        $this->_bFilter = $bFilter;

        return $this;
    }

    /**
     * Process the query, reuturns activities based on object parameters
     *
     * @return $this
     */
    public function process()
    {
        $this->_oQuery = Linko::Database()->table('activity_feed', 'af')
            ->select('af.*', Linko::Model('User')->getTableFields('u', 'ud'))
            ->leftJoin('user', 'u', 'u.user_id = af.user_id')
            ->leftJoin('user_data', 'ud', 'ud.user_id = af.user_id');

        if($this->_bFilter)
        {
            Linko::Plugin()->filter('activity.model_browse_activity_filter', $this->_oQuery);
        }

        if (!empty($this->_iUser))
        {
            $this->_oQuery->where('af.user_id', '=', $this->_iUser);
        }

	    $iTotal = $this->_oQuery->order('af.time_created desc')
	        ->query()
	        ->getCount();

	    $aResult = $this->_oQuery->rebuild()
		    ->offset(($this->_iLimit * (max(1, $this->_iPage) - 1)))
		    ->limit($this->_iLimit)
		    ->query()
	        ->fetchRows();

        foreach($aResult as $iKey => $aActivity)
        {
            $this->_aResult[$iKey] = Linko::Model('Activity')->getForDisplay($aActivity);
        }

        $this->_iTotal = $iTotal;

        Linko::Plugin()->call('activity.browse_process_end', $this->_aResult, $this->_iTotal);

        return $this;

    }

    /**
     * method to get the total activity
     *
     */
    public function getTotal()
    {
        return $this->_iTotal;
    }

    /**
     * Get activities list
     *
     * @return array
     */
    public function getActivities()
    {
        return $this->_aResult;
    }

    /**
     * method to get limit
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->_iLimit;
    }

    public function reset()
    {
        $this->_iUser = null;
        $this->_aResult = array();
        $this->_iTotal = 0;
        $this->_iPage = 0;
        $this->_oQuery = null;
    }
}

?>