<?php
ini_set('include_path', ini_get('include_path') . ':libs/PHPExcel/Classes/');

include "PHPExcel.php";

class SheetFilter implements PHPExcel_Reader_IReadFilter {
	public function readCell($collumn, $row, $worksheetName = '') {
		if (in_array($collumn, range('A', 'J'))) {
			return (true);
		}
		return (false);
	}
}

Class FileSheet {
	private $_file = [[]];
	private $_maxRows;
	private $_maxCols;

	public function __construct ($filename) {
		try {
			$fileType = PHPExcel_IOFactory::identify($filename);
			$reader = PHPExcel_IOFactory::createReader($fileType);
			$reader->setReadDataOnly(true);
			$reader->setReadFilter(new SheetFilter());
			$file = $reader->load($filename);
			$workSheet = $file->getSheet(0);
			$this->_maxRows = $workSheet->getHighestRow();
			$this->_maxCols = ord($workSheet->getHighestColumn()) - ord('A');
			for ($row = 1; $row <= $this->_maxRows; ++$row) {
				for ($col = 0; $col <= $this->_maxCols; ++$col) {
					$value = strval($workSheet->getCellByColumnAndRow($col, $row)->getValue());
					$this->_file[$row][$col] = $value;
				}
			}
		} catch (PHPExcel_Reader_Exception $e) {
			throw (new Exception ('Error: loading file: ' . $e->getMessage()));
		}
	}

	public function getRowGroupedByCol($colId) {
		$grouped = [];
		
		if ($colId < 0 || !($colId <= $this->_maxCols)) {
			throw (new Exception ('Bad col id (max: ' . $this->_maxCols . ')'));
		}
		for ($row = 1; $row <= $this->_maxRows; ++$row) {
			$grouped[$this->_file[$row][$colId]][] = $this->_file[$row];
		}
		return ($grouped);
	}

	public function __toString() {
		$out = '';

		for ($row = 1; $row <= $this->_maxRows; ++$row) {
			for ($col = 0; $col <= $this->_maxCols; ++$col) {
				if ($col && $out !== '') {
					$out .= ' ';
				}
				$out .= $this->_file[$row][$col];
			}
			$out .= PHP_EOL;
		}
		return $out;
	}
}
