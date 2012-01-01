<?php

App::uses('View', 'View');

class PhaseView extends View {

/**
 * Overridden to not force inflecting the filename
 *
 * @param string $name Controller action to find template filename for
 * @return string Template filename
 * @throws MissingViewException when a view file could not be found.
 */
	protected function _getViewFileName($name = null) {
		$subDir = null;

		if (!is_null($this->subDir)) {
			$subDir = $this->subDir . DS;
		}

		if ($name === null) {
			$name = $this->view;
		}
		$name = str_replace('/', DS, $name);

		if (strpos($name, DS) === false && $name[0] !== '.') {
			$name = $this->viewPath . DS . $subDir . $name;
		} elseif (strpos($name, DS) !== false) {
			if ($name[0] === DS || $name[1] === ':') {
				if (is_file($name)) {
					return $name;
				}
				$name = trim($name, DS);
			} else if ($name[0] === '.') {
				$name = substr($name, 3);
			} else {
				$name = $this->viewPath . DS . $subDir . $name;
			}
		}
		$paths = $this->_paths($this->plugin);

		$exts = $this->_getExtensions();
		foreach ($exts as $ext) {
			foreach ($paths as $path) {
				if (file_exists($path . $name . $ext)) {
					return $path . $name . $ext;
				}
			}
		}
		$defaultPath = $paths[0];

		if ($this->plugin) {
			$pluginPaths = App::path('plugins');
			foreach ($paths as $path) {
				if (strpos($path, $pluginPaths[0]) === 0) {
					$defaultPath = $path;
					break;
				}
			}
		}
		throw new MissingViewException(array('file' => $defaultPath . $name . $this->ext));
	}
}
