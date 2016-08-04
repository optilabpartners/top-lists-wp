<?php
namespace OptiLab\Mods\Toplist;

use Roots\Sage\DB;

/**
* ToplistModel Class
*/
class TopListModel extends DB\DB_Table_Row
{
	public $id;
	public $name;
	public $description;
	function __construct($data = null)
	{
		if (is_numeric($data)) {
			$this->id = $data;
		} else {
			parent::__construct($data);
		}

	}

	function getID() {
		return $this->id;
	}

	function getName() {
		return $this->name;
	}

	function setName($name) {
		$this->name = $name;
	}

	function getDescription() {
		return $this->description;
	}

	function setDescription($description) {
		$this->description = $description;
	}

}