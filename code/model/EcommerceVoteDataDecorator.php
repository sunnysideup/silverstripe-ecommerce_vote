<?php

/**
 *@author nicolaas[at]sunnysideup.co.nz
 *
 *
 *
 **/

class EcommerceVoteDataDecorator extends DataObjectDecorator
{
    public function HasEcommerceVote()
    {
        return DataObject::get_one("EcommerceVote", "SessionID = '".Session_ID()."' AND PageID = ".$this->owner->ID);
    }

    public function EcommerceVotes()
    {
        $count = DB::query("Select COUNT(ID) c FROM EcommerceVote WHERE PageID = ".$this->owner->ID);
        foreach ($count as $item) {
            return $item["c"];
        }
        return 0;
    }

    public function updateCMSFields(FieldSet &$fields)
    {
        $array = EcommerceVote::get_array_of_classes_used();
        $show = false;
        if (!is_array($array) || !count($array)) {
            $show = true;
        } else {
            foreach ($array as $className) {
                if ($this->owner instanceof $className) {
                    $show = true;
                }
            }
        }
        if ($show) {
            $fields->addFieldsToTab(
                "Root.Content.Votes",
                new LiteralField("Votes", "Number of votes: ".$this->EcommerceVotes())
            );
        }
        return $fields;
    }
}
