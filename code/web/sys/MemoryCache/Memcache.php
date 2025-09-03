<?php

require_once ROOT_DIR . '/sys/MemoryCache/CachedValue.php';

class Memcache {
	private bool $enableDbCache = true;
	private array $vars = [];

	public function __destruct() {
		//Clear old expired memory cache entries.  This can be done VERY sporadically.
		$random = rand(0, 1000);
		if ($random == 1) {
			try {
				$cachedValue = new CachedValue();
				$cachedValue->whereAdd();
				$cachedValue->whereAdd("expirationTime <= " . time());
				$cachedValue->delete(true);
			} catch (Exception) {
				global $logger;
				$logger->log("Could not clear cache of old values", Logger::LOG_DEBUG);
			}
		}
	}

	public function get($name) {
		if (!array_key_exists($name, $this->vars)) {
			if ($this->enableDbCache && !isset($_REQUEST['reload'])) {
				try {
					$cachedValue = new CachedValue();
					$cachedValue->cacheKey = $name;
					if ($cachedValue->find(true)) {
						if ($cachedValue->expirationTime != 0 && $cachedValue->expirationTime < time()) {
							$this->vars[$name] = false;
						} else {
							$unSerializedValue = unserialize($cachedValue->value);
							$this->vars[$name] = $unSerializedValue;
						}
					} else {
						$this->vars[$name] = false;
					}
				} catch (Exception) {
					//Table has not been created ignore
					$this->vars[$name] = false;
				}
			} else {
				$this->vars[$name] = false;
			}
		}
		return $this->vars[$name];
	}

	public function set($name, $value, $timeout) : bool {
		$this->vars[$name] = $value;
		if ($this->enableDbCache) {
			$valueToCache = serialize($value);
			if (strlen($valueToCache) <= 16384 && strlen($name) < 200) {
				try {
					$cachedValue = new CachedValue();
					$cachedValue->cacheKey = $name;
					$isNew = true;
					if ($cachedValue->find(true)) {
						$isNew = false;
					}
					$cachedValue->value = $valueToCache;
					if ($timeout == 0) {
						$cachedValue->expirationTime = 0;
					} else {
						$cachedValue->expirationTime = time() + $timeout;
					}

					if ($isNew) {
						/** @noinspection PhpUnusedLocalVariableInspection */
						$result = $cachedValue->insert();
					} else {
						/** @noinspection PhpUnusedLocalVariableInspection */
						$result = $cachedValue->update();
					}
				} catch (Exception) {
					//Table has not been created ignore
					global $logger;
					$logger->log("error caching data", Logger::LOG_DEBUG);
				}
			} else {
				if (IPAddress::showDebuggingInformation()) {
					global $logger;
					$logger->log("data for $name was too large to be cached, fields was " . strlen($valueToCache) . " characters long", Logger::LOG_WARNING);
				}
				return false;
			}
		}
		return true;
	}

	static ?CachedValue $cachedValueCleaner = null;

	public function delete($name) : void {
		unset($this->vars[$name]);
		if ($this->enableDbCache) {
			try {
				if (Memcache::$cachedValueCleaner == null) {
					Memcache::$cachedValueCleaner = new CachedValue();
				}

				Memcache::$cachedValueCleaner->whereAdd();
				Memcache::$cachedValueCleaner->cacheKey = $name;
				Memcache::$cachedValueCleaner->delete(true);
			} catch (Exception) {
				//Table has not been created ignore
			}
		}
	}

	public function deleteStartingWith($name) : void {
		foreach ($this->vars as $key => $value) {
			if (str_starts_with($key, $name)) {
				unset($this->vars[$key]);
			}
		}
		if ($this->enableDbCache) {
			try {
				if (Memcache::$cachedValueCleaner == null) {
					Memcache::$cachedValueCleaner = new CachedValue();
				}

				Memcache::$cachedValueCleaner->whereAdd();
				Memcache::$cachedValueCleaner->whereAdd("cacheKey like '$name%'");
				Memcache::$cachedValueCleaner->delete(true);
			} catch (Exception) {
				//Table has not been created ignore
			}
		}
	}
}