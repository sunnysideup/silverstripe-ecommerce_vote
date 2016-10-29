<?php

class EcommerceVote extends DataObject
{
    public static $db = array(
        "SessionID" => "Varchar(64)"
    );

    public static $has_one = array(
        "Page" => "SiteTree"
    );

    protected static $array_of_classes_used = array();
    public static function set_array_of_classes_used($v)
    {
        self::$array_of_classes_used = $v;
    }
    public static function get_array_of_classes_used()
    {
        return self::$array_of_classes_used;
    }

    protected static $create_defaults = false;
    public static function set_create_defaults($v)
    {
        self::$create_defaults = $v;
    }
    public static function get_create_defaults()
    {
        return self::$create_defaults;
    }

    protected static $default_votes = 100;
    public static function set_default_votes($v)
    {
        self::$default_votes = $v;
    }
    public static function get_default_votes()
    {
        return self::$default_votes;
    }

    protected static $random_size_to_add_to_default = 10;
    public static function set_random_size_to_add_to_default($v)
    {
        self::$random_size_to_add_to_default = $v;
    }
    public static function get_random_size_to_add_to_default()
    {
        return self::$random_size_to_add_to_default;
    }

    public static $has_many = array();

    public static $many_many = array();

    public static $belongs_many_many = array();

    public static $many_many_extraFields = array();

    //database related settings
    public static $indexes = array(
        "SessionID" => true,
    );

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();
        if (!$this->SessionID) {
            $this->SessionID = Session_ID();
        }
    }

    public function onAfterWrite()
    {
        parent::onAfterWrite();
        if (DataObject::get_one("EcommerceVote", "SessionID = '".Session_ID()."' AND PageID =".$this->PageID." AND ID <> ".$this->ID)) {
            $this->delete();
        }
    }


    public function requireDefaultRecords()
    {
        parent::requireDefaultRecords();
        $objects = DataObject::get("EcommerceVote", $filter = "", $sort = "Created DESC", $join = "", $limit = "0, 1000");
        $array = array();
        if ($objects) {
            foreach ($objects as $obj) {
                if (isset($array[$obj->PageID])) {
                    if ($array[$obj->PageID] == $obj->SessionID) {
                        $obj->delete();
                        DB::alteration_message("deleting double vote", "deleted");
                    }
                } else {
                    $array[$obj->PageID] = $obj->SessionID;
                }
            }
        }
        unset($array);
        $array = null;
        if (self::get_create_defaults()) {
            $array = self::get_array_of_classes_used();
            if (!is_array($array)|| !count($array)) {
                $array = array("SiteTree");
            }
            if (count($array)) {
                foreach ($array as $className) {
                    $pages = DataObject::get($className, "EcommerceVote.ID IS NULL", $sort = "", $join = "LEFT JOIN EcommerceVote on EcommerceVote.PageID = SiteTree.ID");
                    if ($pages) {
                        foreach ($pages as $page) {
                            $number = intval(self::get_default_votes() + rand(0, self::get_random_size_to_add_to_default()));
                            $i = 0;
                            while ($i < $number) {
                                $obj = new EcommerceVote();
                                $obj->PageID = $page->ID;
                                $obj->SessionID = "default_votes_".$i;
                                $obj->write();
                                DB::alteration_message("creating vote $i for ".$page->Title, "created");
                                $i++;
                            }
                        }
                    } else {
                        DB::alteration_message("no pages for $className", "deleted");
                    }
                }
            } else {
                DB::alteration_message("classname array is empty", "deleted");
            }
        } else {
            DB::alteration_message("not creating defaults in EcommerceVote", "deleted");
        }
    }

    public static $searchable_fields = array("PageID");

    public static $field_labels = array("PageID" => "Page");

    public static $summary_fields = array("Page.Title");

    public static $singular_name = "Vote";
    public function i18n_single_name()
    {
        return _t("EcommerceVote.ECOMMERCEVOTE", "Vote");
    }

    public static $plural_name = "Votes";
    public function i18n_plural_name()
    {
        return _t("EcommerceVote.ECOMMERCEVOTES", "Votes");
    }
}


class EcommerceVote_ModelAdmin extends ModelAdmin
{
    public static $managed_models = array("EcommerceVote");
    public static $url_segment = 'votes';
    public static $menu_title = 'Votes';
}
