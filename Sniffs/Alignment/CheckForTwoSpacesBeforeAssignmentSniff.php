<?php
/**
 * This sniff ensures a minimum of two spaces before and after assignment.
 *
 * @category PHP
 * @package PHP_CodeSniffer
 * @author Lance Erickson <lance@clockwork.net>
 **/

class Clockwork_Sniffs_Alignment_CheckForTwoSpacesBeforeAssignmentSniff implements PHP_CodeSniffer_Sniff{

    /** 
     * Looking for two or more spaces, no tabs.
     **/
    public $whitespace_regex  =  array(
        "before"  =>  "/^  +$/",
        "after"   =>  "/^  $/",
    );

    public $error_message  =  "Improper whitespace %s '%s' assignment.";


    /**
     * Returns token types to sniff.
     *
     * @return array
     **/
    public function register( ) {
        return PHP_CodeSniffer_Tokens::$assignmentTokens;
    }


    /**
     * Examines surrounding tokens for matching whitespace. Error if
     * less than two spaces are found.
     **/
    public function process( PHP_CodeSniffer_File $phpcsFile, $stackPtr ) {

        $tokens  =  $phpcsFile->getTokens( );

        // Ignore assignments used in a condition, like an IF or FOR.
        // Lifted from Generic standard - MultipleStatementAlignment.
        if ( isset( $tokens[$stackPtr]['nested_parenthesis'] ) === true) {
            foreach ( $tokens[$stackPtr]['nested_parenthesis'] as $start => $end) {
                if ( isset( $tokens[$start]['parenthesis_owner'] ) === true) {
                    return;
                }
            }
        }

        $surrounding   =  array( );

        $surrounding['before']  =  $tokens[$stackPtr - 1];
        $surrounding['after']   =  $tokens[$stackPtr + 1];

        foreach( $surrounding as $key => $token ) {

            $match  =  preg_match( $this->whitespace_regex[$key], $token['content'] );

            if ( ! $match ) {
                $data  =  array( $key, $tokens[$stackPtr]['content'] );
                $phpcsFile->addError( $this->error_message, $stackPtr, '', $data );
            }
        }
    }
}
