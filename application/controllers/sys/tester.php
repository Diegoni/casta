<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$libraryDir = APPPATH . 'libraries/simpletest';

if(!is_dir($libraryDir))
exit("Simpletest must be located in \"$libraryDir\"");

require_once($libraryDir . '/unit_tester.php');
require_once($libraryDir . '/mock_objects.php');
require_once($libraryDir . '/reporter.php');

/**
 * SimpleTester 1.0
 * CodeIgniter-library for the SimpleTest unit test library, http://simpletest.org/
 *
 * Default settings: All php files in application/tests will be added to a
 * unit tests suite and run automatically, displaying errors.
 * See the included tests/TEMPLATE_*.php files for an introduction to SimpleTest.
 *
 * @access public
 * @author Andreas S-derlund, ciscoheat CARE OF gmail DOT com
 */
class Tester extends MY_Controller
{
	/**
	 * What reporter should be used for display.
	 * Could be either HtmlReporter, SmallReporter, MinimalReporter or ShowPasses.
	 */
	public $Reporter = 'ShowTable';

	private $testDir;
	private $testTitle;
	private $fileExtension;

	public function __construct(/*$params*/)
	{
		parent::__construct(null, null, FALSE);

		$this->testDir = APPPATH . 'tests'; //$params['testDir'];
		//var_dump($this->config);
		$this->testTitle = $this->config->item('bp.application.name'); //$params['testTitle'];
		$this->fileExtension = 'php'; //$params['fileExtension'];
	}

	function index($file = null)
	{
		echo $this->Run($file);
	}

	/**
	 * Run the tests, returning the reporter output.
	 */
	public function Run($file)
	{
		// Save superglobals that might be tested.
		if(isset($_SESSION)) $oldsession = $_SESSION;
		$oldrequest = $_REQUEST;
		$oldpost = $_POST;
		$oldget = $_GET;
		$oldfiles = $_FILES;
		$oldcookie = $_COOKIE;

		$group_test = new GroupTest($this->testTitle);

		// Add files in tests_dir
		if(is_dir($this->testDir))
		{
			if (isset($file))
			{
				$group_test->addTestFile($this->testDir . DS . $file . '.' . $this->fileExtension);
			}
			else if($dh = opendir($this->testDir))
			{
				while(($file = readdir($dh)) !== FALSE)
				{
					// Test if file ends with php, then include it.
					if(substr($file, -(strlen($this->fileExtension)+1)) == '.' . $this->fileExtension)
					{
						$group_test->addTestFile($this->testDir . DS . $file);
					}
				}
				closedir($dh);
			}
		}

		// Start the tests
		ob_start();
		$group_test->run(new $this->Reporter);
		$output_buffer = ob_get_clean();

		// Restore superglobals
		if(isset($oldsession)) $_SESSION = $oldsession;
		$_REQUEST = $oldrequest;
		$_POST = $oldpost;
		$_GET = $oldget;
		$_FILES = $oldfiles;
		$_COOKIE = $oldcookie;

		return $output_buffer;
	}
}

// Html output reporter classes //////////////////////////////////////

/**
 * Display passes
 */
class ShowPasses extends HtmlReporter
{
	function ShowPasses()
	{
		$this->HtmlReporter();
	}

	function paintPass($message)
	{
		parent::paintPass($message);
		print "<span class=\"pass\">Pass</span>: ";
		$breadcrumb = $this->getTestList();
		array_shift($breadcrumb);
		print implode("-&gt;", $breadcrumb);
		print "-&gt;$message<br />\n";
	}

	function _getCss()
	{
		return parent::_getCss() . ' .pass {color:green;}';
	}
}

/**
 * Display passes
 */
class ShowTable extends HtmlReporter
{
	function __construct()
	{
		parent::__construct('UTF-8');
		//$this->HtmlReporter();
	}

	function paintHeader($test_name)
	{
		$this->sendNoCacheHeaders();
		print "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">";
		print "<html>\n<head>\n<title>$test_name</title>\n";
		print "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=" .
		$this->_character_set . "\">\n";
		print "<style type=\"text/css\">\n";
		print $this->_getCss() . "\n";
		print "</style>\n";
		print "</head>\n<body>\n";
		print "<h1>$test_name</h1>\n";
		flush();
		print '<table border="1">';
		print '<tr><th>Test</th><th>Titulo</th><th>Módulo</th></tr>';
	}

	function paintFooter($test_name)
	{
		print '</table>';
		if($this->getFailCount() + $this->getExceptionCount() == 0)
		{
			$text = $this->getPassCount() . " tests ok";
			print "<div style=\"background-color:#F5FFA8; text-align:center; right:10px; top:30px; border:2px solid green; z-index:10; position:absolute;\">$text</div>";
		}
		else
		{
			$colour = ($this->getFailCount() + $this->getExceptionCount() > 0 ? "red" : "green");
			print "<div style=\"";
			print "padding: 8px; margin-top: 1em; background-color: $colour; color: white;";
			print "\">";
			print $this->getTestCaseProgress() . "/" . $this->getTestCaseCount();
			print " test cases complete:\n";
			print "<strong>" . $this->getPassCount() . "</strong> passes, ";
			print "<strong>" . $this->getFailCount() . "</strong> fails and ";
			print "<strong>" . $this->getExceptionCount() . "</strong> exceptions.";
			print "</div>\n";
		}
		print "</body>\n</html>\n";
	}

	function paint($class, $title, $message)
	{
		$breadcrumb = $this->getTestList();
		array_shift($breadcrumb);

		print "<tr>\n";
		print "<td class='{$class}'>{$title}</td>\n";
		print "<td>$message</td>\n";
		print '<td>' . implode("-&gt;", $breadcrumb) . "</td>\n";
		print "</tr>\n";

	}
	function paintException($exception)
	{
		$message = 'Unexpected exception of type [' . get_class($exception) .
                '] with message ['. $exception->getMessage() .
                '] in ['. $exception->getFile() .
                ' line ' . $exception->getLine() . ']';
		$this->paint('fail', 'Exception', $this->_htmlEntities($message));
		//parent::paintException($exception);
		$this->_exceptions++;
	}

	function paintPass($message)
	{
		//$this->paint('pass', 'Pass', $message);
		//parent::paintPass($message);
		$this->_passes++;
	}
	function paintFail($message)
	{
		$this->paint('fail', 'Fails', $message);
		//parent::paintFail($message);
		$this->_fails++;
	}

	function _getCss()
	{
		return parent::_getCss() . ' .pass {color:green;}';
	}
}


/**
 * Displays a tiny div in upper right corner when ok
 */
class SmallReporter extends HtmlReporter
{
	var $test_name;

	function ShowPasses()
	{
		$this->HtmlReporter();
	}

	function paintHeader($test_name)
	{
		$this->test_name = $test_name;
	}

	function paintFooter($test_name)
	{
		if($this->getFailCount() + $this->getExceptionCount() == 0)
		{
			$text = $this->getPassCount() . " tests ok";
			print "<div style=\"background-color:#F5FFA8; text-align:center; right:10px; top:30px; border:2px solid green; z-index:10; position:absolute;\">$text</div>";
		}
		else
		{
			parent::paintFooter($test_name);
			print "</div>";
		}
	}

	function paintFail($message)
	{
		static $header = FALSE;
		if(!$header)
		{
			$this->newPaintHeader();
			$header = TRUE;
		}
		parent::paintFail($message);
	}

	function newPaintHeader()
	{
		$this->sendNoCacheHeaders();
		print "<style type=\"text/css\">\n";
		print $this->_getCss() . "\n";
		print "</style>\n";
		print "<h1 style=\"background-color:red; color:white;\">$this->test_name</h1>\n";
		print "<div style=\"background-color:#FBFBF0;\">";
		flush();
	}
}

/**
 * Minimal only displays on error
 */
class MinimalReporter extends SmallReporter
{
	function paintFooter($test_name)
	{
		if($this->getFailCount() + $this->getExceptionCount() != 0)
		{
			parent::paintFooter($test_name);
			print "</div>";
		}
	}
}
