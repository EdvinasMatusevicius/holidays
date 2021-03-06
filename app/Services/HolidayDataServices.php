<?php

namespace App\Services;

use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Http;

class HolidayDataServices {

    protected $holidayDatesArr = [];

    public function getYearHolidays(string $year,string $countryCode,?string $regionCode = null){
        $apiUrl = "https://kayaposoft.com/enrico/json/v2.0/?action=getHolidaysForYear&year=$year&country=$countryCode&holidayType=public_holiday";
        $regionCode ? $apiUrl .= "&region=$regionCode" : '';
        return Http::get($apiUrl)->json();
    }
    public function parseHolidayData(array $data,string $countryCode,?string $region = null){
        $this->fillHolidaysDateArr($data);
        return [
            'holidays'=>$this->groupByMonth($data),
            'numberOfHolidays'=>array_reduce($data, [$this,"countPublicHolidays"], 0),
            'maxFreedaysInRow'=>$this->calcMaxFreeDaysInRow($data,$countryCode,$region)
        ];
        
    }

    private function groupByMonth(array $data){
        $groupedHolidays = [];
        foreach ($data as $holiday) {
            $month = DateTime::createFromFormat('!m', $holiday['date']['month'])->format('F');
            if(isset($groupedHolidays[$month])){
                array_push($groupedHolidays[$month],$holiday);
            }else{
                $groupedHolidays[$month]=[$holiday];
            }
        }
        return $groupedHolidays;
    }
    //for array_reduce
    private function countPublicHolidays($days,$holiday){ 
        if($holiday['holidayType']=="public_holiday"){
            $days +=1;
        }
        return $days;
    }
    private function calcMaxFreeDaysInRow($data, $countryCode, $region){
        $maxInRow = 0;
        for ($i=0; $i < count($data); $i++) { 
            $year = $data[$i]['date']['year'];
            $month = $data[$i]['date']['month'];
            $day = $data[$i]['date']['day'];
            $holidayDate =  Carbon::createFromFormat('d-m-Y', "$day-$month-$year");
            $holidaysInRowAhead = $this->inRowHolidaysAhead($holidayDate,'add',$countryCode,$region);
            $i +=$holidaysInRowAhead;
            $freedaysInRow = 1+$holidaysInRowAhead + $this->freedaysAroundHolidays($holidayDate,$holidaysInRowAhead,$countryCode,$region);
            if($maxInRow < $freedaysInRow){
                $maxInRow = $freedaysInRow;
            };
        };
        return $maxInRow;
    }
    public function getCurrentDayStatus(string $countryCode,?string $region = null){
        $date = date('d-m-Y');
        $status = ['date'=>$date];
        $this->isDayWorkDay($date,$countryCode,$region) ? $status['status']='workday' : 
            ($this->isDayHoliday($date,$countryCode,$region) ? $status['status']='holiday' : $status['status']='freeday');
        return $status;
    }
    private function inRowHolidaysAhead($carbonHolidayDate,$operator,$countryCode,$region){
        $holidaysInRow = 1;
        $yearEndDate = $carbonHolidayDate->copy()->endOfYear()->format('d-m-Y');
        while($this->isDayHolidayNoApi($dayToCheck = $this->carbonDayHelper($carbonHolidayDate,'add',$holidaysInRow,true), $countryCode, $region)){//if want to use api change to isDayHoliday
            ++$holidaysInRow;
            if($dayToCheck === $yearEndDate){
                break;
            }
        }
        return $holidaysInRow -1;
    }
    private function freedaysAroundHolidays($carbonHolidayDate,$holidaysAhead, $countryCode, $region){
        $freeDays = 0;
        $yearStart = $carbonHolidayDate->copy()->startOfYear()->format('d-m-Y');
        $yearEnd = $carbonHolidayDate->copy()->endOfYear()->format('d-m-Y');
        if($yearStart !== $carbonHolidayDate->copy()->format('d-m-Y')){
            $a=1;
            while ($this->carbonDayHelper($carbonHolidayDate,'sub',$a,false)->isWeekend() || //asumes that all countrys have same weekends free
                $this->isDayHolidayNoApi($this->carbonDayHelper($carbonHolidayDate,'sub',$a,true), $countryCode, $region)) {//if want to use api change to !$this->isDayWorkDay. -Api bottlenecks-
                    $dayToCheck = $this->carbonDayHelper($carbonHolidayDate,'sub',$a,true);
                ++$a;
                ++$freeDays;
                if($yearStart === $dayToCheck){
                    break;
                }
            }
        };
        if($holidaysAhead > 0){
            $carbonHolidayDate = $this->carbonDayHelper($carbonHolidayDate,'add',$holidaysAhead,false);
        }
        if($yearEnd !== $carbonHolidayDate->copy()->format('d-m-Y')){
            $b=1;
            while ($this->carbonDayHelper($carbonHolidayDate,'add',$b,false)->isWeekend() ||
                $this->isDayHolidayNoApi($this->carbonDayHelper($carbonHolidayDate,'add',$b,true), $countryCode, $region)) { //if want to use api change to !$this->isDayWorkDay 
                $dayToCheck = $this->carbonDayHelper($carbonHolidayDate,'add',$b,true);
                ++$b;
                ++$freeDays;
                if($yearEnd === $dayToCheck){
                    break;
                }
            }
        }
        return $freeDays;
    }
    private function carbonDayHelper($carbon,$operator,$count,$needString){
        if($operator === 'add'){
            $carbonDate = $carbon->copy()->addDays($count);
        }elseif ($operator === 'sub') {
            $carbonDate = $carbon->copy()->subDays($count);
        }
        if($needString){
            return $carbonDate->format('d-m-Y');
        }
        return $carbonDate;
    }
    private function fillHolidaysDateArr($data){
        foreach ($data as $holiday) {
            $year = $holiday['date']['year'];
            $month = $holiday['date']['month'];
            $day = $holiday['date']['day'];
            $holidayDate =  Carbon::parse("$day-$month-$year")->format('d-m-Y');
            array_push($this->holidayDatesArr,$holidayDate);
        }
    }
    private function isDayHolidayNoApi($date){
        return in_array($date,$this->holidayDatesArr);
    }
    private function isDayWorkDay($date,$countryCode,$region){
        $apiUrl = "https://kayaposoft.com/enrico/json/v2.0/?action=isWorkDay&date=$date&country=$countryCode";
        $region ? $apiUrl .= "&region=$region" : '';
        return ((Http::get($apiUrl)->json())['isWorkDay']);
    }
    private function isDayHoliday($date,$countryCode,$region){
        $apiUrl = "https://kayaposoft.com/enrico/json/v2.0?action=isPublicHoliday&date=$date&country=$countryCode";
        $region ? $apiUrl .= "&region=$region" : '';
        return (Http::get($apiUrl)->json())['isPublicHoliday'];  
    }
}