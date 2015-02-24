<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements. See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 * 
 *		http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * 
 * @package log4php
 */

use /** @noinspection PhpUndefinedClassInspection */
    Psr\Log\LoggerInterface;

require dirname(__FILE__) . '/LoggerAutoloader.php';

/**
 * This is the central class in the log4php package. All logging operations 
 * are done through this class.
 * 
 * The main logging methods are:
 * 	<ul>
 * 		<li>{@link trace()}</li>
 * 		<li>{@link debug()}</li>
 * 		<li>{@link info()}</li>
 * 		<li>{@link notice()}</li>
 * 		<li>{@link warning()}</li>
 * 		<li>{@link error()}</li>
 * 		<li>{@link critical()}</li>
 * 		<li>{@link alert()}</li>
 * 		<li>{@link emergency()}</li>
 * 	</ul>
 * 
 * @package    log4php
 * @license	   http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @link	   http://logging.apache.org/log4php
 */

/** @noinspection PhpUndefinedClassInspection */
class Logger implements LoggerInterface {
	
	/**
	 * Logger additivity. If set to true then child loggers will inherit
	 * the appenders of their ancestors by default.
	 * @var boolean
	 */
	private $additive = true;
	
	/** 
	 * The Logger's fully qualified class name.
	 * TODO: Determine if this is useful. 
	 */
	private $fqcn = 'Logger';

	/** The assigned Logger level. */
	private $level;
	
	/** The name of this Logger instance. */
	private $name;
	
	/** @var Logger The parent logger. Set to null if this is the root logger. */
	private $parent;
	
	/** A collection of appenders linked to this logger. */
	private $appenders = array();

	/**
	 * Constructor.
	 * @param string $name Name of the logger.	  
	 */
	public function __construct($name) {
		$this->name = $name;
	}
	
	/**
	 * Returns the logger name.
	 * @return string
	 */
	public function getName() {
		return $this->name;
	} 

	/**
	 * Returns the parent Logger. Can be null if this is the root logger.
	 * @return Logger
	 */
	public function getParent() {
		return $this->parent;
	}
	
	// ******************************************
	// *** Logging methods                    ***
    // ******************************************

    /**
     * Log a message object with the TRACE level.
     *
     * @param mixed $message message
     * @param array $context context key will substitute message variables {key} <> [key=>val]
     *        for throwable use key exception
     * @return void
     * @deprecated This method is not part of PSR-3 and may be removed in a future release
     */
    public function trace($message, array $context = array()) {
        $this->log(LoggerLevel::getLevelTrace(), $message, $context);
    }

    /**
     * Log a message object with the DEBUG level.
     * Detailed debug information.
     *
     * @param mixed $message message
     * @param array $context context key will substitute message variables {key} <> [key=>val]
     *        for throwable use key exception
     * @return void
     */
    public function debug($message, array $context = array()) {
        $this->log(LoggerLevel::getLevelDebug(), $message, $context);
    }

    /**
     * Log a message object with the INFO Level.
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param mixed $message message
     * @param array $context context key will substitute message variables {key} <> [key=>val]
     *        for throwable use key exception
     * @return void
     */
    public function info($message, array $context = array()) {
        $this->log(LoggerLevel::getLevelInfo(), $message, $context);
    }

    /**
     * Log a message object with the NOTICE level.
     * Normal but significant events.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function notice($message, array $context = array()) {
        $this->log(LoggerLevel::getLevelNotice(), $message, $context);
    }

    /**
     * Log a message object with the WARNING level.
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function warning($message, array $context = array()) {
        $this->log(LoggerLevel::getLevelWarning(), $message, $context);
    }

    /**
     * Log a message with the WARNING level.
     * warning equivalent, for downward compatibility
     *
     * @param mixed $message message
     * @param array $context context key will substitute message variables {key} <> [key=>val]
     *        for throwable use key exception
     * @return void
     * @deprecated This method is replaced by Logger::warning and may be removed in a future release
     */
	public function warn($message, array $context = array()) {
		$this->warning($message, $context);
	}

    /**
     * Log a message object with the ERROR level.
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message message
     * @param array $context context key will substitute message variables {key} <> [key=>val]
     *        for throwable use key exception
     * @return void
     */
    public function error($message, array $context = array()) {
        $this->log(LoggerLevel::getLevelError(), $message, $context);
    }

    /**
     * Log a message object with the CRITICAL level.
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function critical($message, array $context = array()) {
        $this->log(LoggerLevel::getLevelCritical(), $message, $context);
    }

    /**
     * Log a message object with the CRITICAL level.
     * critical equivalent, for downward compatibility
     *
     * @param mixed $message message
     * @param array $context context key will substitute message variables {key} <> [key=>val]
     *        for throwable use key exception
     * @return void
     * @deprecated This method is replaced by Logger::critical and may be removed in a future release
     */
    public function fatal($message, array $context = array()) {
        $this->critical($message, $context);
    }

    /**
     * Log a message object with the ALERT level.
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function alert($message, array $context = array()) {
        $this->log(LoggerLevel::getLevelAlert(), $message, $context);
    }

    /**
     * Log a message object with the EMERGENCY level.
     * System is unusable.
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    public function emergency($message, array $context = array()) {
        $this->log(LoggerLevel::getLevelEmergency(), $message, $context);
    }

    /**
     * Log a message using the provided logging level.
     *
     * @param mixed $level
     * @param string $message message
     * @param array $context context key will substitute message variables {key} <> [key=>val]
     *        for throwable use key exception
     * @return void
     */
	public function log($level, $message, array $context = array()) {
		if($this->isEnabledFor($level)) {
			$event = new LoggerLoggingEvent($this->fqcn, $this, $level, $message, null, $context);
			$this->callAppenders($event);
		}

		// Forward the event upstream if additivity is turned on
		if(isset($this->parent) && $this->getAdditivity()) {

			// Use the event if already created
			if (isset($event)) {
				$this->parent->logEvent($event);
			} else {
				$this->parent->log($level, $message, $context);
			}
		}
	}

    /**
	 * Logs an already prepared logging event object. 
	 * @param LoggerLoggingEvent $event
	 */
	public function logEvent(LoggerLoggingEvent $event) {
		if($this->isEnabledFor($event->getLevel())) {
			$this->callAppenders($event);
		}
		
		// Forward the event upstream if additivity is turned on
		if(isset($this->parent) && $this->getAdditivity()) {
			$this->parent->logEvent($event);
		}
	}
	
	/**
	 * If assertion parameter evaluates as false, then logs the message 
	 * using the ERROR level.
	 *
	 * @param bool $assertion
	 * @param string $msg message to log
	 */
	public function assertLog($assertion = true, $msg = '') {
		if($assertion == false) {
			$this->error($msg);
		}
	}
	
	/**
	 * This method creates a new logging event and logs the event without 
	 * further checks.
	 *
	 * It should not be called directly. Use {@link trace()}, {@link debug()},
	 * {@link info()}, {@link warn()}, {@link error()} and {@link fatal()} 
	 * wrappers.
	 *
	 * @param string $fqcn Fully qualified class name of the Logger
     * @param array $context context key will substitute message variables {key} <> [key=>val]
     *        for throwable use key exception
     * @param LoggerLevel $level log level
	 * @param mixed $message message to log
	 */
	public function forcedLog($fqcn, $context, LoggerLevel $level, $message) {
		$event = new LoggerLoggingEvent($fqcn, $this, $level, $message, null, $context);
		$this->callAppenders($event);
		
		// Forward the event upstream if additivity is turned on
		if(isset($this->parent) && $this->getAdditivity()) {
			$this->parent->logEvent($event);
		}
	}

	/**
	 * Forwards the given logging event to all linked appenders.
	 * @param LoggerLoggingEvent $event
	 */
	public function callAppenders($event) {
		/** @var LoggerAppender $appender */
        foreach($this->appenders as $appender) {
			$appender->doAppend($event);
		}
	}
	
	// ******************************************
	// *** Checker methods                    ***
	// ******************************************
	
	/**
	 * Check whether this Logger is enabled for a given Level passed as parameter.
	 *
	 * @param LoggerLevel $level
	 * @return boolean
	 */
	public function isEnabledFor(LoggerLevel $level) {
		return $level->isGreaterOrEqual($this->getEffectiveLevel());
	}
	
	/**
	 * Check whether this Logger is enabled for the TRACE Level.
	 * @return boolean
     * @deprecated method removed for PSR-3 compatibility
	 */
	public function isTraceEnabled() {
		return $this->isEnabledFor(LoggerLevel::getLevelTrace());
	}
	
	/**
	 * Check whether this Logger is enabled for the DEBUG Level.
	 * @return boolean
	 */
	public function isDebugEnabled() {
		return $this->isEnabledFor(LoggerLevel::getLevelDebug());
	}

	/**
	 * Check whether this Logger is enabled for the INFO Level.
	 * @return boolean
	 */
	public function isInfoEnabled() {
		return $this->isEnabledFor(LoggerLevel::getLevelInfo());
	}

    /**
     * Check whether this Logger is enabled for the NOTICE Level.
     * @return boolean
     */
    public function isNoticeEnabled() {
        return $this->isEnabledFor(LoggerLevel::getLevelNotice());
    }

    /**
     * Check whether this Logger is enabled for the WARN Level.
     * @return boolean
     */
    public function isWarningEnabled() {
        return $this->isEnabledFor(LoggerLevel::getLevelWarning());
    }

    /**
	 * Check whether this Logger is enabled for the WARN Level.
	 * @return boolean
     * @deprecated replaced by isWarningEnabled()
	 */
	public function isWarnEnabled() {
		return $this->isWarningEnabled();
	}
	
	/**
	 * Check whether this Logger is enabled for the ERROR Level.
	 * @return boolean
	 */
	public function isErrorEnabled() {
		return $this->isEnabledFor(LoggerLevel::getLevelError());
	}

    /**
     * Check whether this Logger is enabled for the CRITICAL Level.
     * @return boolean
     */
    public function isCriticalEnabled() {
        return $this->isEnabledFor(LoggerLevel::getLevelCritical());
    }
	
	/**
	 * Check whether this Logger is enabled for the CRITICAL Level.
	 * @return boolean
     * @deprecated replaced by isCriticalEnabled()
	 */
	public function isFatalEnabled() {
		return $this->isCriticalEnabled();
	}

    /**
     * Check whether this Logger is enabled for the EMERGENCY Level.
     * @return bool
     */
    public function isAlertEnabled() {
        return $this->isEnabledFor(LoggerLevel::getLevelAlert());
    }

    /**
     * Check whether this Logger is enabled for the EMERGENCY Level.
     * @return bool
     */
    public function isEmergencyEnabled() {
        return $this->isEnabledFor(LoggerLevel::getLevelEmergency());
    }
	
	// ******************************************
	// *** Configuration methods              ***
	// ******************************************
	
	/**
	 * Adds a new appender to the Logger.
	 * @param LoggerAppender $appender The appender to add.
	 */
	public function addAppender($appender) {
		$appenderName = $appender->getName();
		$this->appenders[$appenderName] = $appender;
	}
	
	/** Removes all appenders from the Logger. */
	public function removeAllAppenders() {
		foreach($this->appenders as $name => $appender) {
			$this->removeAppender($name);
		}
	} 
			
	/**
	 * Remove the appender passed as parameter form the Logger.
	 * @param mixed $appender an appender name or a {@link LoggerAppender} instance.
	 */
	public function removeAppender($appender) {
		if(is_string($appender) and isset($this->appenders[$appender])) {
            $appender = $this->appenders[$appender];
        }

        if($appender instanceof LoggerAppender) {
			$appender->close();
            unset($this->appenders[$appender->getName()]);
		}
	}
	
	/**
	 * Returns the appenders linked to this logger as an array.
	 * @return array collection of appender names
	 */
	public function getAllAppenders() {
		return $this->appenders;
	}

    /**
     * Returns a linked appender by name.
     * @param $name
     * @return LoggerAppender
     */
	public function getAppender($name) {
		return $this->appenders[$name];
	}

	/**
	 * Sets the additivity flag.
	 * @param boolean $additive
	 */
	public function setAdditivity($additive) {
		$this->additive = (bool)$additive;
	}
	
	/**
	 * Returns the additivity flag.
	 * @return boolean
	 */
	public function getAdditivity() {
		return $this->additive;
	}
 
	/**
	 * Starting from this Logger, search the Logger hierarchy for a non-null level and return it.
	 * @see LoggerLevel
	 * @return LoggerLevel|null
	 */
	public function getEffectiveLevel() {
		for($logger = $this; $logger !== null; $logger = $logger->getParent()) {
			if($logger->getLevel() !== null) {
				return $logger->getLevel();
			}
		}
        return null;
	}
  
	/**
	 * Get the assigned Logger level.
	 * @return LoggerLevel The assigned level or null if none is assigned. 
	 */
	public function getLevel() {
		return $this->level;
	}
	
	/**
	 * Set the Logger level.
	 *
	 * Use LoggerLevel::getLevelXXX() methods to get a LoggerLevel object, e.g.
	 * <code>$logger->setLevel(LoggerLevel::getLevelInfo());</code>
	 *
	 * @param LoggerLevel $level The level to set, or NULL to clear the logger level.
	 */
	public function setLevel(LoggerLevel $level = null) {
		$this->level = $level;
	}
	
	/**
	 * Checks whether an appender is attached to this logger instance.
	 *
	 * @param LoggerAppender $appender
	 * @return boolean
	 */
	public function isAttached(LoggerAppender $appender) {
		return isset($this->appenders[$appender->getName()]);
	}
	
	/**
	 * Sets the parent logger.
	 * @param Logger $logger
	 */
	public function setParent(Logger $logger) {
		$this->parent = $logger;
	} 
	
	// ******************************************
	// *** Static methods and properties      ***
	// ******************************************
	
	/** The logger hierarchy used by log4php. */
	private static $hierarchy;
	
	/** Inidicates if log4php has been initialized */
	private static $initialized = false;
	
	/**
	 * Returns the hierarchy used by this Logger.
	 *
	 * Caution: do not use this hierarchy unless you have called initialize().
	 * To get Loggers, use the Logger::getLogger and Logger::getRootLogger
	 * methods instead of operating on on the hierarchy directly.
	 *
	 * @return LoggerHierarchy
	 */
	public static function getHierarchy() {
		if(!isset(self::$hierarchy)) {
			self::$hierarchy = new LoggerHierarchy(new LoggerRoot());
		}
		return self::$hierarchy;
	}
	
	/**
	 * Returns a Logger by name. If it does not exist, it will be created.
	 *
	 * @param string $name The logger name
	 * @return Logger
	 */
	public static function getLogger($name) {
		if(!self::isInitialized()) {
			self::configure();
		}
		return self::getHierarchy()->getLogger($name);
	}
	
	/**
	 * Returns the Root Logger.
	 * @return LoggerRoot
	 */
	public static function getRootLogger() {
		if(!self::isInitialized()) {
			self::configure();
		}
		return self::getHierarchy()->getRootLogger();
	}
	
	/**
	 * Clears all Logger definitions from the logger hierarchy.
	 */
	public static function clear() {
		self::getHierarchy()->clear();
	}
	
	/**
	 * Destroy configurations for logger definitions
	 */
	public static function resetConfiguration() {
		self::getHierarchy()->resetConfiguration();
		self::getHierarchy()->clear(); // TODO: clear or not?
		self::$initialized = false;
	}
	
	/**
	 * Safely close all appenders.
	 * @deprecated This is no longer necessary due the appenders shutdown via
	 * destructors.
	 */
	public static function shutdown() {
		self::getHierarchy()->shutdown();
	}
	
	/**
	 * check if a given logger exists.
	 *
	 * @param string $name logger name
	 * @return boolean
	 */
	public static function exists($name) {
		return self::getHierarchy()->exists($name);
	}
	
	/**
	 * Returns an array this whole Logger instances.
	 * @see Logger
	 * @return array
	 */
	public static function getCurrentLoggers() {
		return self::getHierarchy()->getCurrentLoggers();
	}
	
	/**
	 * Configures log4php.
	 * 
	 * This method needs to be called before the first logging event has 
	 * occured. If this method is not called before then the default
	 * configuration will be used.
	 *
	 * @param string|array $configuration Either a path to the configuration
	 *   file, or a configuration array.
	 *   
	 * @param string|LoggerConfigurator $configurator A custom 
	 * configurator class: either a class name (string), or an object which 
	 * implements the LoggerConfigurator interface. If left empty, the default
	 * configurator implementation will be used. 
	 */
	public static function configure($configuration = null, $configurator = null) {
		self::resetConfiguration();
		$configurator = self::getConfigurator($configurator);
		$configurator->configure(self::getHierarchy(), $configuration);
		self::$initialized = true;
	}

    /**
     * Creates a logger configurator instance based on the provided
     * configurator class. If no class is given, returns an instance of
     * the default configurator.
     *
     * @param string|LoggerConfigurator $configurator The configurator class
     * or LoggerConfigurator instance.
     * @return LoggerConfiguratorDefault
     */
	private static function getConfigurator($configurator = null) {
		if ($configurator === null) {
			return new LoggerConfiguratorDefault();
		}
		
		if (is_object($configurator)) {
			if ($configurator instanceof LoggerConfigurator) {
				return $configurator;
			} else {
				trigger_error("log4php: Given configurator object [$configurator] does not implement the LoggerConfigurator interface. Reverting to default configurator.", E_USER_WARNING);
				return new LoggerConfiguratorDefault();
			}
		}
		
		if (is_string($configurator)) {
			if (!class_exists($configurator)) {
				trigger_error("log4php: Specified configurator class [$configurator] does not exist. Reverting to default configurator.", E_USER_WARNING);
				return new LoggerConfiguratorDefault();
			}
			
			$instance = new $configurator();
				
			if (!($instance instanceof LoggerConfigurator)) {
				trigger_error("log4php: Specified configurator class [$configurator] does not implement the LoggerConfigurator interface. Reverting to default configurator.", E_USER_WARNING);
				return new LoggerConfiguratorDefault();
			}
			
			return $instance;
		}
		
		trigger_error("log4php: Invalid configurator specified. Expected either a string or a LoggerConfigurator instance. Reverting to default configurator.", E_USER_WARNING);
		return new LoggerConfiguratorDefault();
	}
	
	/**
	 * Returns true if the log4php framework has been initialized.
	 * @return boolean 
	 */
	public static function isInitialized() {
		return self::$initialized;
	}










}
