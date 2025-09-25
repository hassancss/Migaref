<?php
class Migareference_Model_Referrerstats extends Core_Model_Default {  
    public function __construct($datas = []) {
	  parent::__construct($datas);
        $this->_db_table = 'Migareference_Model_Db_Table_Referrerstats';
    }  
    public function setIntervals($app_id = 0)
    {
      return $this->getTable()->setIntervals($app_id);
    }
    // Referrer
    public function totalCountReports($app_id = 0,$user_id=0,$from_date='',$to_date='')
    {
      return $this->getTable()->totalCountReports($app_id,$user_id,$from_date,$to_date);
    }
    public function totalCountLandPrpct($app_id = 0,$user_id=0,$from_date='',$to_date='')
    {
      return $this->getTable()->totalCountLandPrpct($app_id,$user_id,$from_date,$to_date);
    }
    public function totalGraphReports($app_id = 0,$user_id=0,$from_date='',$to_date='',$label_formate='',$group_by_foramt='')
    {
      return $this->getTable()->totalGraphReports($app_id,$user_id,$from_date,$to_date,$label_formate,$group_by_foramt);
    }
    // General
    public function totalCountReportsGnl($app_id = 0,$from_date='',$to_date='')
    {
      return $this->getTable()->totalCountReportsGnl($app_id,$from_date,$to_date);
    }    
    public function countReferrerRatGnl($app_id = 0,$from_date='',$to_date='')
    {
      return $this->getTable()->countReferrerRatGnl($app_id,$from_date,$to_date);
    }
    public function countReferrerJobsGnl($app_id = 0,$from_date='',$to_date='')
    {
      return $this->getTable()->countReferrerJobsGnl($app_id,$from_date,$to_date);
    }
	public function countReferrerSectorsGnl($app_id = 0, $from_date = '', $to_date = '')
    {
      return $this->getTable()->countReferrerSectorsGnl($app_id, $from_date, $to_date);
    }
	
    public function countReferrerRegionsGnl($app_id = 0,$from_date='',$to_date='')
    {
      return $this->getTable()->countReferrerRegionsGnl($app_id,$from_date,$to_date);
    }
    public function countReferrerProvincesGnl($app_id = 0,$from_date='',$to_date='')
    {
      return $this->getTable()->countReferrerProvincesGnl($app_id,$from_date,$to_date);
    }
    public function totalCountRemidersGnl($app_id = 0,$from_date='',$to_date='')
    {
      return $this->getTable()->totalCountRemidersGnl($app_id,$from_date,$to_date);
    }
    public function countReferrerAgeGnl($app_id = 0,$from_date='',$to_date='')
    {
      return $this->getTable()->countReferrerAgeGnl($app_id,$from_date,$to_date);
    }
    public function totalGraphReportsGnl($app_id = 0,$from_date='',$to_date='',$label_formate='',$group_by_foramt='')
    {
      return $this->getTable()->totalGraphReportsGnl($app_id,$from_date,$to_date,$label_formate,$group_by_foramt);
    }
    public function totalGraphRemindersGnl($app_id = 0,$from_date='',$to_date='',$label_formate='',$group_by_foramt='')
    {
      return $this->getTable()->totalGraphRemindersGnl($app_id,$from_date,$to_date,$label_formate,$group_by_foramt);
    }
    public function totalGraphRefGnl($app_id = 0,$from_date='',$to_date='',$label_formate='',$group_by_foramt='')
    {
      return $this->getTable()->totalGraphRefGnl($app_id,$from_date,$to_date,$label_formate,$group_by_foramt);
    }
    public function totalTblAgentsGnl($app_id = 0,$from_date='',$to_date='')
    {
      return $this->getTable()->totalTblAgentsGnl($app_id,$from_date,$to_date);
    }

    //Admin or Agent stats
    public function agentReferrersTblAgent($app_id = 0,$agent_id=0,$from_date='',$to_date='')
    {
      return $this->getTable()->agentReferrersTblAgent($app_id,$agent_id,$from_date,$to_date);
    }
    public function totalCountRemidersAgent($app_id = 0,$agent_id=0,$from_date='',$to_date='')
    {
      return $this->getTable()->totalCountRemidersAgent($app_id,$agent_id,$from_date,$to_date);
    }
    public function countReferrerRatAgent($app_id = 0,$agent_id=0,$from_date='',$to_date='')
    {
      return $this->getTable()->countReferrerRatAgent($app_id,$agent_id,$from_date,$to_date);
    }
    public function totalGraphRefAgent($app_id = 0,$agent_id=0,$from_date='',$to_date='',$label_formate='',$group_by_foramt='')
    {
      return $this->getTable()->totalGraphRefAgent($app_id,$agent_id,$from_date,$to_date,$label_formate,$group_by_foramt);
    }
    public function totalGraphRemindersAgent($app_id = 0,$agent_id=0,$from_date='',$to_date='',$label_formate='',$group_by_foramt='')
    {
      return $this->getTable()->totalGraphRemindersAgent($app_id,$agent_id,$from_date,$to_date,$label_formate,$group_by_foramt);
    }
    public function totalGraphReportsAgent($app_id = 0,$agent_id=0,$from_date='',$to_date='',$label_formate='',$group_by_foramt='')
    {
      return $this->getTable()->totalGraphReportsAgent($app_id,$agent_id,$from_date,$to_date,$label_formate,$group_by_foramt);
    }
    public function totalCountReportsAgent($app_id = 0,$agent_id=0,$from_date='',$to_date='',$label_formate='',$group_by_foramt='')
    {
      return $this->getTable()->totalCountReportsAgent($app_id,$agent_id,$from_date,$to_date,$label_formate,$group_by_foramt);
    }
    public function countReferrerJobsAgent($app_id = 0,$agent_id=0,$from_date='',$to_date='')
    {
      return $this->getTable()->countReferrerJobsAgent($app_id,$agent_id,$from_date,$to_date);
    }
    public function countReferrerAgeAgent($app_id = 0,$agent_id=0,$from_date='',$to_date='')
    {
      return $this->getTable()->countReferrerAgeAgent($app_id,$agent_id,$from_date,$to_date);
    }
    public function countReferrerRegionsAgent($app_id = 0,$agent_id=0,$from_date='',$to_date='')
    {
      return $this->getTable()->countReferrerRegionsAgent($app_id,$agent_id,$from_date,$to_date);
    }
    public function countReferrerProvincesAgent($app_id = 0,$agent_id=0,$from_date='',$to_date='')
    {
      return $this->getTable()->countReferrerProvincesAgent($app_id,$agent_id,$from_date,$to_date);
    }
    public function countReferrerProvinceAgent($app_id = 0,$agent_id=0,$from_date='',$to_date='')
    {
      return $this->getTable()->countReferrerProvinceAgent($app_id,$agent_id,$from_date,$to_date);
    }
	public function totalCountReportsGnlByRating($app_id = 0, $agent_id = 0, $from_date = '', $to_date = '')
    {
      return $this->getTable()->totalCountReportsGnlByRating($app_id, $agent_id, $from_date, $to_date);
    }
  }
