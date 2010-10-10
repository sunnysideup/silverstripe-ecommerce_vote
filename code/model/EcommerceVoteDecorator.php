<?php

/**
 *@author nicolaas[at]sunnysideup.co.nz
 *
 *
 *
 **/


class EcommerceVoteDecorator extends Extension {


	static $allowed_actions = array(
		"addecommercevote" => true,
		"removeallecommercevotes" => true
	);



	function addecommercevote() {
		$id = intval(Director::URLParam("ID"));
		if($id) {
			if($page = DataObject::get_by_id("SiteTree", $id)) {
				$ecommerceVote = new EcommerceVote();
				$ecommerceVote->PageID = $id;
				$ecommerceVote->write();
				if(Director::is_ajax()) {
					return "voted";
				}
				else {
					Director::redirectBack();
					return;
				}
			}
		}
		if(Director::is_ajax()) {
			return "vote ERROR";
		}
		else {
			Director::redirectBack();
			return;
		}
	}


	function TopEcommerceVotes($numberOfEntries = 5) {
		$sqlQuery = new SQLQuery(
			$select = "SiteTree.ID MyPageID, COUNT(EcommerceVote.ID) c",
			$from = array('SiteTree INNER JOIN EcommerceVote ON SiteTree.ID = EcommerceVote.PageID'),
			$where = "",
			$orderby = "c DESC",
			$groupby = "SiteTree.ID",
			$having = "",
			$limit = "0, $numberOfEntries"
		);
		$results = $sqlQuery->execute();
		if($results) {
			$stage = Versioned::current_stage();
			$baseClass = "SiteTree";
			$stageTable = ($stage == 'Stage') ? $baseClass : "{$baseClass}_{$stage}";
			$dataObjectSet = new DataObjectSet();
			foreach($results as $result) {
				$page = DataObject::get_by_id("SiteTree", $result["MyPageID"]);
				$page->EcommerceVoteCounter =  $result["c"];
				$dataObjectSet->push($page);
			}
			return $dataObjectSet;
		}
	}

	function EcommerceVoteTopFive() {
		return $this->TopEcommerceVotes(5);
	}

	function removeallecommercevotes() {
		if($m = Member::currentUser()) {
			if($m->isAdmin()) {
				DB::query("DELETE FROM EcommerceVote");
				die("all votes have been deleted");
			}
		}
		die("you have to be logged in as an administrator");
	}

}