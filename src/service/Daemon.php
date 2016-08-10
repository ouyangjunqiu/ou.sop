<?php
namespace phpservice\service;


/**
 * Daemon base class
 *
 * Requirements:
 * Unix like operating system
 * PHP 4 >= 4.3.0 or PHP 5
 * PHP compiled with:
 * --enable-sigchild
 * --enable-pcntl
 *
 * @package binarychoice.system.unix
 * @author Michal 'Seth' Golebiowski <seth at binarychoice dot pl>
 * @copyright Copyright 2005 Seth
 * @since 1.0.3
 */
class Daemon
{
    /**#@+
     * @access public
     */
    /**
     * User ID
     *
     * @var int
     * @since 1.0
     */
    public $userID = 1000;

    /**
     * Group ID
     *
     * @var integer
     * @since 1.0
     */
    public $groupID = 1000;

    /**
     * Terminate daemon when set identity failure ?
     *
     * @var bool
     * @since 1.0.3
     */
    protected $requireSetIdentity = false;

    /**
     * Path to PID file
     *
     * @var string
     * @since 1.0.1
     */
    protected $pidFileLocation = '/tmp/service.daemon.pid';

    /**
     * Home path
     *
     * @var string
     * @since 1.0
     */
    protected $homePath = '/';
    /**#@-*/


    /**#@+
     * @access protected
     */
    /**
     * Current process ID
     *
     * @var int
     * @since 1.0
     */
    private $_pid = 0;

    /**
     * Is this process a children
     *
     * @var boolean
     * @since 1.0
     */
    private $_isChildren = false;

    /**
     * Is daemon running
     *
     * @var boolean
     * @since 1.0
     */
    private $_isRunning = false;

    /**
     * @var null
     */
    private $task = null;

    /**
     * @param Task $task
     */
    public function __construct($task = null)
    {
        $this->task = $task;
        #error_reporting(0);
        set_time_limit(0);
        #ob_implicit_flush();

        register_shutdown_function(array(&$this, 'releaseDaemon'));
    }

    /**
     * Starts daemon
     *
     * @access public
     * @since 1.0
     * @return bool
     */
    public function start()
    {
        if (!$this->_daemonize()) {
            Logger::log('Could not start daemon', DLOG_ERROR);

            return false;
        }

        $this->_isRunning = true;


        while ($this->_isRunning) {
            $this->_doTask();
        }

        return true;
    }

    /**
     * Stops daemon
     *
     * @access public
     * @since 1.0
     * @return void
     */
    public function stop()
    {
        $this->_isRunning = false;
    }

    /**
     *
     */
    protected function _doTask()
    {
        // override this method
        if ($this->task != null && $this->task instanceof Task) {
            try {
                $this->task->run();
            } catch (TaskException $e) {
                Logger::log($e->getMessage(), DLOG_ERROR);
            } catch (\Exception $e){
                Logger::log($e->getMessage(), DLOG_ERROR);
            }
        }

    }

    /**
     * Daemonize
     *
     * Several rules or characteristics that most daemons possess:
     * 1) Check is daemon already running
     * 2) Fork child process
     * 3) Sets identity
     * 4) Make current process a session laeder
     * 5) Write process ID to file
     * 6) Change home path
     * 7) umask(0)
     *
     * @access private
     * @since 1.0
     * @return boolean
     */
    private function _daemonize()
    {
        #ob_end_flush();

        if ($this->_isDaemonRunning()) {
            // Deamon is already running. Exiting
            return false;
        }

        if (!$this->_fork()) {
            // Coudn't fork. Exiting.
            return false;
        }

        if (!$this->_setIdentity() && $this->requireSetIdentity) {
            // Required identity set failed. Exiting
            return false;
        }

        if (!posix_setsid()) {
            Logger::log('Could not make the current process a session leader', DLOG_ERROR);

            return false;
        }

        if (!$fp = @fopen($this->pidFileLocation, 'w')) {
            Logger::log('Could not write to PID file', DLOG_ERROR);

            return false;
        } else {
            fputs($fp, $this->_pid);
            fclose($fp);
        }

        @chdir($this->homePath);
        umask(0);

        declare(ticks = 1);

        pcntl_signal(SIGCHLD, array(&$this, 'sigHandler'));
        pcntl_signal(SIGTERM, array(&$this, 'sigHandler'));

        return true;
    }

    /**
     * Cheks is daemon already running
     *
     * @access private
     * @since 1.0.3
     * @return bool
     */
    private function _isDaemonRunning()
    {
        $oldPid = @file_get_contents($this->pidFileLocation);

        if ($oldPid !== false && posix_kill(trim($oldPid), 0)) {
            Logger::log('Daemon already running with PID: ' . $oldPid, (DLOG_TO_CONSOLE | DLOG_ERROR));

            return true;
        } else {
            return false;
        }
    }

    /**
     * Forks process
     *
     * @access private
     * @since 1.0
     * @return bool
     */
    private function _fork()
    {
        $pid = pcntl_fork();

        if ($pid == -1) // error
        {
            Logger::log('Could not fork', DLOG_ERROR);

            return false;
        } else if ($pid > 0) // parent
        {
            exit();
        } else // children
        {

            $this->_isChildren = true;
            $this->_pid = posix_getpid();
            Logger::log('Children Run' . $this->_pid);
            return true;
        }
    }

    /**
     * Sets identity of a daemon and returns result
     *
     * @access private
     * @since 1.0
     * @return bool
     */
    private function _setIdentity()
    {
        if (!posix_setgid($this->groupID) || !posix_setuid($this->userID)) {
            Logger::log('Could not set identity', DLOG_WARNING);

            return false;
        } else {
            return true;
        }
    }

    /**
     * @param $sigNo
     */
    public function sigHandler($sigNo)
    {
        switch ($sigNo) {
            case SIGTERM:   // Shutdown
                exit();
                break;

            case SIGCHLD:   // Halt
                while (pcntl_waitpid(-1, $status, WNOHANG) > 0) ;
                break;
        }
    }

    /**
     * Releases daemon pid file
     * This method is called on exit (destructor like)
     *
     * @access public
     * @since 1.0
     * @return void
     */
    public function releaseDaemon()
    {
        if ($this->_isChildren && file_exists($this->pidFileLocation)) {
            unlink($this->pidFileLocation);
        }
    }
}