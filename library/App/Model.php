<?php
/**
* @file Model.php
* @synopsis 模型封装
* @author Yee, <rlk002@gmail.com>
* @version 1.0
* @date 2016-07-13 17:15:39
*/

abstract class App_Model extends Zend_Db_Table_Abstract
{

	protected $_displayColumn = NULL;
	protected $_returnPaginators = TRUE;

	public function save(array $data)
	{
		$primary = (is_array($this->_primary) ? $this->_primary[1] : $this->_primary);

		if(isset($data[$primary]) && $data[$primary])
		{
			$select = $this->_select();
			$select->where($primary . '= ?', $data[$primary]);
			$select->reset(Zend_Db_Table::COLUMNS);
			$select->columns(array('COUNT(' . $primary . ')'));
			
			if($this->fetchRow($select) == 1)
			{
				$id = $data[$primary];
				$this->update($data, $this->_db->quoteInto($primary . '= ?', $data[$primary]));
				return $id;
			}else
			{
				$data[$primary] = NULL;
				return $this->insert($data);
			}
		}else
		{
			$data[$primary] = NULL;
			return $this->insert($data);
		}
	}

	public function insert(array $data)
	{
		$data = $this->_filter($data);
		return parent::insert($data);
	}

	public function update(array $data, $where)
	{
		$data = $this->_filter($data);
		$where = $this->_normalizeWhere($where);
		return parent::update($data, $where);
	}

	public function delete($where)
	{
		$where = $this->_normalizeWhere($where);
		return parent::delete($where);
	}

	public function findBySlug($slug)
	{
		$select = $this->select();
		$select->where('slug = ?', $slug);
		return parent::fetchRow($select);
	}

	public function deleteById($id)
	{
		if ($this->canBeDeleted($id))
		{
			if(is_array($this->_primary))
			{
				return $this->delete($this->_db->quoteInto($this->_primary[1] . ' = ?', $id));
			}else
			{
				return $this->delete($this->_db->quoteInto($this->_primary . ' = ?', $id));
			}
		}else
		{
			throw new Zend_Exception('This item cannot be deleted. Please check the dependencies first.');
		}
	}


	protected function _filter($data)
	{
		$filteredData = array();
		foreach($this->info(Zend_Db_Table_Abstract::COLS) as $key)
		{
			if(isset($data[$key]))
			{
				$filteredData[$key] = $data[$key];
			}
		}

		return $filteredData;
	}


	protected function _normalizeWhere($where)
	{
		if (is_numeric($where))
		{
			$where = $this->_db->quoteInto($this->_primary . ' = ?', $where);
		}else
		{
			if(is_array($where))
			{
				$parts = array();
				foreach ($where as $key => $value)
				{
					if(is_numeric($key))
					{
						$parts[] = $value;
					}else
					{
						$part = $this->_db->quoteInto($this->_db->quoteIdentifier($key) . ' = ?', $value); 
						$parts[] = $part;
					}
				}
				$where = implode(' AND ', $parts);
			}
		}

		return $where;
	}

	public function findById($id, $force = FALSE)
	{
		if (!is_numeric($id))
		{
			return array();
		}

		$select = $this->_getSelect($force);
		$column = $this->_extractTableAlias($select) . '.' . $this->_primary[1];
		$select->where($column . ' = ?', $id);
		return $this->fetchRow($select);
	}

	public function findAll($page = 1, $paginate = NULL, $force = FALSE)
	{
		$select = $this->_getSelect($force);
		return $this->_paginate($select, $page, $paginate);
	}

	public function search($criteria, $page = 1, $paginate = NULL, $force = FALSE)
	{
		$select = $this->_getSelect($force);

		if(is_array($criteria))
		{
			$queryParts = array();
			foreach($criteria as $colname => $colval)
			{
				if(is_array($colval))
				{
					$parts = array();
					foreach($colval as $val)
					{
						$parts[] = $this->_db->quote($val);
					}
					$queryParts[] = $this->_db->quoteIdentifier($colname) . ' IN (' . implode(',', $parts) . ')';
				}else
				{
					if ($colval instanceof Zend_Db_Expr)
					{
						$queryParts[] = $this->_db->quoteIdentifier($colname) . ' = ' . $colval;
					}else
					{
						$queryParts[] = $this->_db->quoteIdentifier($colname) . ' = ' . $this->_db->quote($value);
					}
				}
			}
			if(count($queryParts) > 1)
			{
				$where = '(' . implode(') AND (', $queryParts) . ')';
			}else
			{
				$where = $queryParts;
			}
		}else
		{
			$where = $criteria;
		}

		$select->where($where);
		return $this->_paginate($select, $page, $paginate);
	}

	public function count($force = FALSE)
	{
		$select = $this->_getSelect($force);
		$select->reset(Zend_Db_Table::COLUMNS);
		$select->columns(array('COUNT(*)'));
		return $this->fetchOne($select);
	}

	public function canBeDeleted($id)
	{
		return TRUE;
	}

	protected function _select()
	{
		$select = $this->select();
		$select->from($this->_name);
		return $select;
	}

	protected final function _getSelect($force = FALSE)
	{
		if($force)
		{
			$select = $this->select();
			$select->from($this->_name);
			return $select;
		}
		return $this->_select();
	}


	protected function _paginate($select, $page, $paginate)
	{
		if(NULL === $paginate)
		{
			$paginate = $this->_returnPaginators;
		}

		if(!$paginate)
		{
			return $this->fetchAll($select);
		}

		$paginator = Zend_Paginator::factory($select);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage(App_DI_Container::get('ConfigObject')->paginator->items_per_page);

		return $paginator;
	}


	protected function _extractTableAlias($select)
	{
		$parts = $select->getPart('from');
		foreach($parts as $alias => $part)
		{
			if($part['tableName'] == $this->_name)
			{
				return $alias;
			}
		}
		return $this->_name;
	}


	protected function _setupDatabaseAdapter()
	{
		if(isset($this->_adapter))
		{
			$this->_db = Zend_Registry::get($this->_adapter);
		}else
		{
			$this->_db = Zend_Db_Table_Abstract::getDefaultAdapter();
		}
	}
}
