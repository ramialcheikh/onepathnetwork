<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 11/07/15
 * Time: 12:36 PM
 */

class Stats {

    private $stats;

    /*
     * The models of which the stats need to be determined
     */
    private $statSubjects;

    public function __construct(){
        $this->statSubjects[] = array(
            'model' => 'User',
            'alias' =>  'User'
        );
    }

    public function addStatSubject($subjectClass, $alias = null) {
        $this->statSubjects[] = array(
            'model' => $subjectClass,
            'alias' =>  $alias ? $alias : class_basename($subjectClass)
        );
    }
    public function getOverallStats() {
        $overallStats = array();
        foreach($this->statSubjects as $statSubject) {
            $overallStats[$statSubject['alias']] = $statSubject['model']::count();
        }
        //Filling stats vars that are not yet set
        self::fillNullStats($overallStats);
        return $overallStats;
    }

    public function getTodayStats() {
        $todayStats = array();
        foreach($this->statSubjects as $statSubject) {
            $todayStats[$statSubject['alias']] = $statSubject['model']::whereRaw('DATE(created_at) = DATE(\'' . date('Y-m-d H:i:s') . '\')')->count();
        }
        //Filling stats vars that are not yet set
        self::fillNullStats($todayStats);
        return $todayStats;
    }

    /*
     * Get daily stats(new items) for last N days for a particular model
     */
    public static function getDailyStatsFor($model, $daysCount, $whereCondition = null) {

        $activityHistoryQuery = $model::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as todayCount'))->where('created_at', '>', DB::raw('DATE_SUB(NOW(), INTERVAL '. $daysCount .' DAY)'));
        if($whereCondition){
            $activityHistoryQuery->where($whereCondition);
        }
        $activityHistory = $activityHistoryQuery->groupBy('date')->get()->toArray();

        $lastNDays = self::lastNDays($daysCount);
        $dailyStats = array();
        foreach($lastNDays as $key => $day) {
            $dailyStats[$key] = array('date' => $day, 'stats' => 0);
            foreach($activityHistory as $activity){
                if ($activity['date'] === $day) {
                    $dailyStats[$key]['stats'] = $activity['todayCount'];
                }
            }
        }
        return ($dailyStats);

    }

    /*
     * Fill the stats vars that are not yet set in the stats array
     * Set them to null
     * @param $stats The stats array
     */
    public static function fillNullStats(&$stats){
        foreach ($stats as $var => $val) {
            if(!isset($stats[$var]))
                $stats[$var] = 0;
        }

    }

    public static function lastNDays($n){
        $timestamp = time();
        $days = array();
        for ($i = 0 ; $i < $n ; $i++) {
            $days[] = date('Y-m-d', $timestamp);
            $timestamp -= 24 * 3600;
        }
        return $days;
    }

}