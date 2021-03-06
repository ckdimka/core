<?php
/**
 * XLite_Sniffs_PHP_Files_LineEndingsSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */

if (class_exists('Generic_Sniffs_Files_LineEndingsSniff', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class Generic_Sniffs_Files_LineEndingsSniff not found');
}

/**
 * XLite_Sniffs_PHP_Files_LineEndingsSniff.
 *
 * Checks that end of line characters are "\n".
 *
 * @category  PHP
 * @package   PHP_CodeSniffer
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @copyright 2006 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @version   Release: 1.2.0RC1
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 */
class XLite_Sniffs_PHP_Files_LineEndingsSniff extends XLite_ReqCodesSniff
{

    /**
     * The valid EOL character.
     *
     * @var string
     */
    protected $eolChar = "\n";

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(T_OPEN_TAG);

    }//end register()

    /**
     * Processes this sniff, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        // We are only interested if this is the first open tag.
        if ($stackPtr !== 0) {
            if ($phpcsFile->findPrevious(T_OPEN_TAG, ($stackPtr - 1)) !== false) {
                return;
            }
        }

        if ($phpcsFile->eolChar !== $this->eolChar) {
            $expected = $this->eolChar;
            $expected = str_replace("\n", '\n', $expected);
            $expected = str_replace("\r", '\r', $expected);
            $found    = $phpcsFile->eolChar;
            $found    = str_replace("\n", '\n', $found);
            $found    = str_replace("\r", '\r', $found);
            $error    = "End of line character is invalid; expected \"$expected\" but found \"$found\"";
            $phpcsFile->addError($this->getReqPrefix('REQ.PHP.2.3.1') . $error, $stackPtr);
        }

    }//end process()

}//end class

?>
