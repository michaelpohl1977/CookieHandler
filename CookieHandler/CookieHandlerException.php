<?php

/**
 * CookieHandlerException.php
 * 
 * This file contains all static errorcodes and a special exception class for CookieHandlerDiese 
 * 
 * @author Michael Pohl (www.simatex.de)
 */




// Avoid multi declaration of CookieHandler in the same project
if (class_exists('CookieHandlerException')) 
{  
	return;
}
	



/*******************************************************************************
 * CookieHandlerException
 * 
 * Exception class for CookieHandler errors which also includes all availabe 
 * static error codes (CookieHandlerException::<error code>)
 */
class CookieHandlerException extends Exception
{
	// Available static error codes
	public static $EC_GENERAL       = 100;
	public static $EC_NONAME        = 101;
	public static $EC_INVALIDNUMBER = 102;
	public static $EC_WRITEERROR    = 103;
	public static $EC_READERROR     = 104;
	
	
	/**********************************************************************
	 * __construct ()
	 * 
	 * Constructor which overrides the constructor of the parent class
	 * 
	 * @param string $Message Error message
	 * @param int    $Code    Error code (e.g. CookieHandlerException::$EC_WRITEERROR)
	 */
	public function __construct ( $Message, $Code = 100)
	{
		parent::__construct($Message, $Code);
	}
}