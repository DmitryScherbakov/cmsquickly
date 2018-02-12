<?php

class Db
{

	protected static $do;

	private static function connect()
	{
		try {
			self::$do = new PDO('mysql:host=' . Config::$aDbConf['host'] . ';dbname=' . Config::$aDbConf['db_name'], Config::$aDbConf['user'], Config::$aDbConf['password'], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		} catch (PDOException $e) {
			die("Error!: " . $e->getMessage());
		}
	}

	public static function __callStatic($name, $args)
	{
		if (self::$do == NULL) {
			$this->connect();
		}
		if(empty($args)) {
			return self::$do->$name();
		}
		return self::$do->$name(implode(', ', $args));
	}

	private static function showError($text)
	{
		if (Config::$aDbConf['show_error']) {
			echo '<div class="">' . $text . '</div>';
			exit;
		}
	}

	public static function getContentDataByUrl($url, $lang)
	{
		$mainContent = self::getRow('SELECT * FROM #__content WHERE link_code=:code LIMIT 1', [':code' => md5($url)]);
		if ($mainContent) {
			$langData = self::getRow('SELECT * FROM #__content_lang WHERE fid=:fid AND lang=:lang LIMIT 1', [':fid' => $mainContent['id'], ':lang' => $lang]);
			if ($langData) {
				unset($langData['id'], $langData['fid'], $langData['lang']);
				return $mainContent + $langData;
			}
			return $mainContent;
		}
		return [];
	}

	public static function getNextListOrdering($tableName, $additionalWhere = '')
	{
		$where = empty($additionalWhere) ? '' : ' WHERE ' . $additionalWhere;
		$sql = "SELECT MAX(`ordering`) FROM #__{$tableName}{$where}";
		$ordering = intval(self::getVal($sql));
		return $ordering ? $ordering + 1 : 1;
	}

	public static function getNextTreeOrdering($tableName, $pid, $additionalWhere = '')
	{
		$sql = "SELECT MAX(`ordering`) FROM #__{$tableName} WHERE `pid`={$pid} {$additionalWhere}";
		$ordering = intval(self::getVal($sql));
		return $ordering ? $ordering + 1 : 1;
	}

	public static function getAsTree($tblName, $lang, $fields, $additionalWhere = '')
	{
		$sql = "SELECT {$fields} FROM #__{$tblName} AS m LEFT JOIN #__{$tblName}_lang AS l ON m.id=l.fid WHERE m.pid=:pid AND l.lang=:lang {$additionalWhere} ORDER BY m.ordering";
		$sth = self::prepare($sql);
		return self::traverseBranch($sth, 0, $lang);
	}

	private static function traverseBranch($sth, $pid, $lang)
	{
		$sth->execute([':pid' => $pid, ':lang' => $lang]);
		$tree = $sth->fetchAll(PDO::FETCH_ASSOC);
		foreach ($tree as $key => $row) {
			$tree[$key]['children'] = self::traverseBranch($sth, $row['id'], $lang);
		}
		return $tree;
	}

	public static function query($sql, $params = [])
	{
		try {
			$stmt = self::prepare($sql);
			if ($params) {
				$stmt->execute($params);
			} else {
				$stmt->execute();
			}
			return true;
		} catch (PDOException $e) {
			self::showError($e->getMessage());
		}
		return false;
	}

	public static function batchQuery($sql, $params)
	{
		try {
			$stmt = self::prepare($sql);
			foreach ($params as $row) {
				$stmt->execute($row);
			}
			return true;
		} catch (PDOException $e) {
			self::showError($e->getMessage());
		}
		return false;
	}

	/**
	 *
	 * @param type $tblName - sql table name without prefix
	 * @param type $fields - sql fields in key=>value format without : prefix in keys
	 * @return boolean
	 */
	public static function insert($tblName, $fields)
	{
		try {
			$sql = "INSERT INTO #__{$tblName} (`" . implode('`, `', array_keys($fields)) . '`) VALUES(' . implode(',', array_fill(0, sizeof($fields), '?')) . ')';
			$stmt = self::prepare($sql);
			if ($stmt) {
				$values = array_values($fields);
				for ($i = 0; $i < sizeof($fields); $i++) {
					$stmt->bindValue($i + 1, $values[$i]);
				}
				$stmt->execute();
				return self::$do->lastInsertId();
			} else {
				return false;
			}
		} catch (PDOException $e) {
			self::showError($e->getMessage());
		}
		return false;
	}

	/**
	 *
	 * @param string $tblName имя таблицы, где будет UPDATE
	 * @param array $set массив с данными, которые нужно обновить в формате ключь => значение
	 * @param string $where строка sql WHERE
	 * @param array/null $params параметры для установки, кроме тех что находятся в $set
	 * @return boolean
	 */
	public static function update($tblName, $set, $where, $params = [])
	{
		try {
			$keys = array_keys($set);
			$setStr = '';
			foreach ($keys as $key) {
				$setStr .= $key . '=:' . $key . ',';
				$params[':' . $key] = $set[$key];
			}

			$sql = "UPDATE #__{$tblName} SET " . trim($setStr, ',') . " WHERE {$where}";
			$stmt = self::prepare($sql);
			if ($stmt) {
				$stmt->execute($params);
				return true;
			} else {
				return false;
			}
		} catch (PDOException $e) {
			self::showError($e->getMessage());
		}
		return false;
	}

	public static function getRow($sql, $params = false)
	{
		try {
			$stmt = self::prepare($sql);
			if ($params) {
				$stmt->execute($params);
			} else {
				$stmt->execute();
			}
			return $stmt->fetch(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			self::showError($e->getMessage());
		}
		return false;
	}

	public static function getAll($sql, $params = false)
	{
		try {
			$stmt = self::prepare($sql);
			if ($params) {
				$stmt->execute($params);
			} else {
				$stmt->execute();
			}
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			self::showError($e->getMessage());
		}
		return false;
	}

	public static function getAllWithKey($key, $sql, $params = false)
	{
		try {
			$stmt = self::prepare($sql);
			if ($params) {
				$stmt->execute($params);
			} else {
				$stmt->execute();
			}
			$tmp = [];
			foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
				$tmp[$row[$key]] = $row;
			}
			return $tmp;
		} catch (PDOException $e) {
			self::showError($e->getMessage());
		}
		return false;
	}

	public static function getVal($sql, $params = false)
	{
		try {
			$stmt = self::prepare($sql);
			if ($params) {
				$stmt->execute($params);
			} else {
				$stmt->execute();
			}
			$tmp = $stmt->fetch(PDO::FETCH_NUM);
			return isset($tmp[0]) ? $tmp[0] : false;
		} catch (PDOException $e) {
			self::showError($e->getMessage());
		}
		return false;
	}

	public static function getColumn($sql, $params) {
		try {
			$stmt = self::prepare($sql);
			if ($params) {
				$stmt->execute($params);
			} else {
				$stmt->execute();
			}
			return $stmt->fetchAll(PDO::FETCH_COLUMN);
		} catch (PDOException $e) {
			self::showError($e->getMessage());
		}
		return false;
	}

	public static function lastId()
	{
		return self::$do->lastInsertId();
	}

	public static function prepare($sql)
	{
		if (self::$do == NULL) {
			self::connect();
		}
		try {
			return self::$do->prepare(str_replace('#__', Config::$aDbConf['prefix'], $sql));
		} catch (PDOException $e) {
			self::showError($e->getMessage());
		}
		return NULL;
	}

	public static function getError()
	{
		return self::$do->errorInfo();
	}

}
