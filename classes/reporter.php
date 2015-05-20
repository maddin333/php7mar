<?php
/**
 * PHP 7 MAR
 * Reporter Class
 *
 * @author     Alexia E. Smith <washuu@gmail.com>
 * @copyright  2015 Alexia E. Smith
 * @link       https://github.com/Alexia/php7mar
 */

namespace mar;

class reporter {
	/**
	 * Project File or Path
	 *
	 * @var		string
	 */
	private $projectPath = null;

	/**
	 * Report folder to save reports
	 *
	 * @var		string
	 */
	private $reportFolder = null;

	/**
	 * Full file path to the report file.
	 *
	 * @var		string
	 */
	private $fullFilePath = null;

	/**
	 * Line Buffer
	 *
	 * @var		array
	 */
	private $buffer = [];

	/**
	 * Start Time, date('U')
	 *
	 * @var		string
	 */
	private $startTime = null;

	/**
	 * File Handler Resource
	 *
	 * @var		string
	 */
	private $file;

	/**
	 * Main Constructor
	 *
	 * @access	public
	 * @param	string	Project file or folder
	 * @param	string	[Optional] Folder to save the report
	 * @return	void
	 */
	public function __construct($projectPath, $reportFolder = null) {
		$this->startTime = time();

		if (empty($projectPath)) {
			throw new Exception(__METHOD__.": Project path given was empty.");
		}
		$this->projectPath = $projectPath;

		$reportFolder = main::getRealPath($reportFolder);
		if ($reportFolder !== false) {
			$this->reportFolder = $reportFolder;
		} else {
			$this->reportFolder = PHP7MAR_DIR.DIRECTORY_SEPARATOR.'reports'.DIRECTORY_SEPARATOR;
		}
		$this->fullFilePath = $this->reportFolder.date('Y-m-d H:i:s ').basename($this->projectPath, '.php').".txt";

		$this->file = fopen($this->fullFilePath, 'w+');
		register_shutdown_function([$this, 'onShutdown']);

		$this->add(date('c', $this->startTime), 0, 1);
		$this->add("Scanning {$this->projectPath}", 0, 1);
	}

	/**
	 * Add a new line to the report.
	 *
	 * @access	public
	 * @param	string	Line of text to add to the buffer.
	 * @param	integer Number of new line characters to add before the line.
	 * @param	integer Number of new line characters to add after the line.
	 * @param	string	Line of text to add to the buffer.
	 * @return	void
	 */
	public function add($line, $nlBefore = 0, $nlAfter = 0) {
		$output = str_repeat("\n", $nlBefore).$line.str_repeat("\n", $nlAfter);
		if (fwrite($this->file, $output) === false) {
			die("There was an error attempting to write to the report file.\n".$this->fullFilePath."\n");
		}
	}

	/**
	 * Return the current contents of the buffer.
	 *
	 * @access	public
	 * @return	array	Array of lines
	 */
	public function getBuffer() {
		return $this->buffer;
	}

	/**
	 * Handle any file clean up on shutdown.
	 *
	 * @access	public
	 * @return	void
	 */
	public function onShutdown() {
		fclose($this->file);
	}
}
?>