<?php
namespace Optilab\Mods\TopList;

use Roots\Sage\DB;
use Optilab\Mods\Toplist\TopListModel;

/**
* Toplist Manager
*/
class TopListController
{

	public function __construct() {

	}

	public static function bootstrap() {
		global $wpdb;
		Db\DB_Manager::execute(call_user_func(function() use ($wpdb) {
			$rows = array(
				new DB\DB_Table_Column('id', 'int', null, false, false, true, true ),
				new DB\DB_Table_Column('name', 'varchar(100)', null, true, true ),
				new DB\DB_Table_Column('description', 'varchar(255)', null, true ),
			);
			$table = new DB\DB_Table('toplist', $rows, $wpdb);

			return $table->create();
		}));
	}
	
	public static function create(TopListModel $toplist) {

		global $wpdb;
		$table = new DB\DB_Table('toplist');
		$result = DB\DB_Manager::insert(call_user_func(function() use ($toplist, $table) {
			return $table->addRow($toplist);
		}));
		if (is_int($result)) {
			$toplist->id = (int)$result;
			return $toplist;
		} else {
			return false;
		}
	}

	public static function fetchOne(TopListModel $toplist) {
		global $wpdb;
		$table = new DB\DB_Table('toplist');
		$row = DB\DB_Manager::fetchOne(call_user_func(function() use ($table, $toplist) {
			return $table->getRow($toplist);
		}));
		$toplist = new TopListModel($row);
		return $toplist;
	}

	public static function fetchMany($criteria = null, $condition_AND = true ) {
		$table = new DB\DB_Table('toplist');
		$rows = DB\DB_Manager::fetchMany(call_user_func(function() use ($table, $criteria, $condition_AND) {
			return $table->getRows($criteria, $condition_AND);
		}));
		// TODO: Put things back
		$toplists = array();

		foreach ($rows as $row) {
			$toplists[] =  new TopListModel((array) $row);
		}
		return $toplists;
	}

	public static function updateOne(TopListModel $toplist) {
		global $wpdb;
		$table = new DB\DB_Table('toplist');
		$row = DB\DB_Manager::update(call_user_func(function() use ($table, $toplist) {
			return $table->updateRow($toplist);
		}));
	}

	public static function deleteOne(TopListModel $toplist) {
		global $wpdb;
		$table = new DB\DB_Table('toplist');
		$row = DB\DB_Manager::deleteOne(call_user_func(function() use ($table, $toplist) {
			return $table->deleteRow($toplist);
		}));
	}
}
