<?php
/**
 * author Chupurnov valeriy
 * website http://xdan.ru
 * email chupurnov@gmail.com
 */
 
class uno{
	static private function escapeKeys($array){
		$db = JFactory::getDBO();
		$keys = array_values($array);
		foreach($keys as $id=>$key)
			$keys[$id] = $db->quote($key);
		return $keys;
	}
	
	/**
	 * example uno::insert('tablename',array('name'=>'vasya'));
	 */
	static public  function insert($table,$array){
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		
		$query
			->insert($db->quoteName('#__'.$table))
			->columns($db->quoteName(array_keys($array)))
			->values(implode(',', self::escapeKeys($array)));
		 
		$db->setQuery($query);
		return $db->query()?$db->insertid():false;
	}
	
	/**
	 * example uno::update('tablename',array('name'=>'vasya'),array('id=5'));
	 */
	static public function update($table,$array,$conditions){
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$obj = new stdclass();
		
		$query
			->update($db->quoteName('#__'.$table))
			->where($conditions);
			
		foreach($array as $key=>$value){
			$query->set($db->quoteName($key).'="'.$value.'"');
		}	
		 
		$db->setQuery($query);
		return $db->query();
	}
	
		
	static private function query($table,$conditions=false,$colums=array('*'),$order = false,$limit = false,$offset = 0){
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query
			->select($colums)
			->from($db->quoteName('#__'.$table));
			
		if( $conditions )
			$query->where($conditions);
			
		if( $order )
			$query->order($order);
		
		$db->setQuery($query,$offset,$limit);
		return $db;
	}
	
	/**
	 * example uno::getRow('tablename',array('id=5'));
	 */
	static public function getRow($table,$conditions=false,$colums=array('*'),$order = false,$offset = 0){
		return self::query($table,$conditions,$colums,$order,1,$offset)->loadAssoc();
	}
	
	/**
	 * example uno::getRowBySQL('select * from #__tablename where id=5'));
	 */
	static public function getRowBySQL($sql){
		$db = JFactory::getDBO();
		$db->setQuery($sql);
		return $db->loadAssoc();
	}
	
	/**
	 * example uno::update('tablename',array('name'=>'vasya'),array('name='.uno::_('vasya')));
	 */
	static public function getRows($table,$conditions=false,$colums=array('*'),$order = false,$limit = false,$offset = 0){
		return self::query($table,$conditions,$colums,$order,$limit,$offset)->loadAssocList();
	}
	
	/**
	 * example uno::getRowBySQL('select * from #__tablename where name='.uno::_('vova')));
	 */
	static public function getRowsBySQL($sql){
		$db = JFactory::getDBO();
		$db->setQuery($sql);
		return $db->loadAssocList();
	}
	
	/**
	 * example if(uno::exists('tablename','id=5')){};
	 */
	static public function exists($table,$conditions,$id='id'){
		$cnt = self::getRow($table,$conditions,array($id));
		return isset($cnt[$id])?$cnt[$id]:false;
	}
	
	/**
	 * example uno::delete('tablename','id=5');
	 */
	static public function delete($table,$conditions){
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query
			->delete($db->quoteName('#__'.$table))
			->where($conditions);
		 
		$db->setQuery($query);
		return $db->query();
	}
	
	static public function _($value){
		return JFactory::getDBO()->quote($value);
	}
	static public function __($value){
		return JFactory::getDBO()->quoteName($value);
	}	
	static public function error(){
		return JFactory::getDBO()->getErrorMsg();
	}
}
