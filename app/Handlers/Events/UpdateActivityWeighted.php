<?php namespace App\Handlers\Events;

use App\Events\ActivityRateWasAdded;
use App\WeightedAverage;
use App\Iterinary;
use App\Rating;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldBeQueued;

class UpdateActivityWeighted
{
    /**
     * Create the event handler.
     *
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ActivityRateWasAdded $event
     */
    public function handle(ActivityRateWasAdded $event)
    {
        $added_ite = WeightedAverage::where("ratingable_type", "Activity")->where("ratingable_id", $event->activity_id)->get();
        if (count($added_ite) == 0) {
            // dd("in")
            $new_weightedaverage = new WeightedAverage;
            $new_weightedaverage->ratingable_id = $event->activity_id;
            $new_weightedaverage->ratingable_type = "Activity";
            $new_weightedaverage->average = 0;
            $new_weightedaverage->save();

        }
        // dd("out");
        $this->updateAllWeightedAverage();
    }

    public function updateAllWeightedAverage()
    {
        $all = WeightedAverage::where("ratingable_type", "Activity")->get();
        // dd($all);
        foreach ($all as $wa) {
            //check if number of votes reaches 5 (minimum)
            if ($this->checkMinVotes($wa->ratingable_id)) {
                // dd("in");
                $numOfVotes = count(Rating::where("ratingable_type", "Activity")->where("ratingable_id", $wa->ratingable_id)->get());
                $avgRate = Rating::where("ratingable_type", "Activity")->where("ratingable_id", $wa->ratingable_id)->selectRaw("avg(value)")->get();
                $avgAll = Rating::where("ratingable_type", "Activity")->selectRaw("avg(value)")->get();
                foreach ($avgRate as $value) {
                    $avgRate = $value['avg(value)'];
                }
                foreach ($avgAll as $value) {
                    $avgAll = $value['avg(value)'];
                }
                //calculate weighted average (base Bayesian rating W = Rv+Cm/v+m )
                $weighted_average = round((($avgRate * $numOfVotes) + ($avgAll * 5)) / ($numOfVotes + 5), 1);
                // dd($avgRate,$numOfVotes, $avgAll, $weighted_average);
                // dd($wa->id);
                $update_weighted_average = WeightedAverage::find($wa->id);
                $update_weighted_average->average = $weighted_average;
                $update_weighted_average->save();
                // return "updated successfully";
            } else {
//                dd("cannot get WeightedAverage");
            }
        }
    }

    public function checkMinVotes($activity_id)
    {
        $numOfVotes = Rating::where("ratingable_type", "Activity")->where("ratingable_id", $activity_id)->get();
        if (count($numOfVotes) >= 5) {
            return true;
        }
        return false;
    }
}