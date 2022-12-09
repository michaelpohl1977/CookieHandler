<?php

/**
 * CookieHandler.php
 * 
 * Base file for usage of CookieHandler. 
 * This class has to be imported/included where you need cookie functionallity.
 * Important: Cookies always have to be set before the first output to the
 * browser is sent.
 * 
 * @author Michael Pohl (www.simatex.de)
 */


// Loading required exception class
require_once __DIR__.'/CookieHandlerException.php';

// Avoiding multi declarations in the same project
if (class_exists('CookieHandler')) 
{  
	return;
}




/*******************************************************************************
 * CookieHandler
 * 
 * Main class of CookieHandler that handles the complete cookie functionallity
 */
class CookieHandler
{
	// Name of current cookie
	private $_Name = '';
	// Validity path of the cookies
	private $_Path = '';
	// Domain where the current cookie is valid
	private $_Domain = '';
	// Cookie lifetime (calculated from current time) as Unix timestamp or 0, 
	// if the cookie should only be valid intil the browser session is closed
	private $_Validity = 0;
	
	
		
	 
	/**********************************************************************
	 * __construct ()
	 * 
	 * Constuctor with optional setting of cookie name, path and domain
	 * 
	 * Important: '.' in cookie names are replaced by '_'.
	 * 
	 * @param string $Name   Name of the current cookie. Empty names are not allowed
	 * @param string $Path   Optional path within the current domain, the cookie is valid in
	 * @param string $Domain Optional domain (Subdomain) under which the cookie 
	 *                       is valid. If no domain is given, the current domain
	 *                       will be used.
	 * 
	 * @throws CookieHandlerException EC_NONAME if No name is set
	 */
	public function __construct ( $Name, $Path = '', $Domain = '' ) 
	{
		$this->SetName($Name);
		$this->SetPath($Path);
		$this->SetDomain($Domain);
	}
	
	
	/**********************************************************************
	 * SetName ()
	 * 
	 * Sets the name of the cookie if it should differ from the one given in the
	 * constuctor.
	 * 
	 * Important: '.' in cookie names are replaced by '_'.
	 * 
	 * @param string $Name Name of the current cookie. Empty names are not allowed
	 * 
	 * @throws CookieHandlerException EC_NONAME if no name is set
	 */
	public function SetName ( $Name ) 
	{
		$Name = trim($Name);

		if ( empty($Name) ) 
		{
			throw new CookieHandlerException ("The cookie name mustn't be empty", 
				CookieHandlerException::$EC_NONAME);
		}

		$this->_Name = $Name;
	}
	
	
	/**********************************************************************
	 * GetName ()
	 * 
	 * Returns the set name of the cookie
	 * 
	 * @return string Name that was set in constructor or SetName()
	 */
	public function GetName ()
	{
		return $this->_Name;
	}
	
	
	/**********************************************************************
	 * SetPath ()
	 * 
	 * Sets the validity path of the cookie, if is should differ from the one
	 * set in the constructor
	 * 
	 * @param string $Path Path to set. If no parameter is given, '' is set as
	 *                     default. If the path doesn't start with '/', it is
	 *                     added automatically
	 */
	public function SetPath ( $Path = '' )
	{
		$Path = trim($Path);
		$this->_Path = empty($Path) ? $Path : (strpos($Path, '/') === 0 ? $Path : '/'.$Path);
	}
	
	
	/**********************************************************************
	 * GetPath ()
	 * 
	 * Returns the set cookie path
	 *
	 * @return string Path that was set in the constructor or SetPath()
	 */
	public function GetPath ()
	{
		return $this->_Path;
	}

	
	/**********************************************************************
	 * SetDomain ()
	 * 
	 * Sets the domain, under which the cookie is valid if it should differ from
	 * the one set in the constructor. With this domain it is possible to set
	 * the validity e.g. just for a special subdomain.
	 * 
	 * @param string $Domain Optional domain to set. 'localhost' is not allowed
	 *                       and is automatically replaced by '' (current domain)
	 */
	public function SetDomain ( $Domain = '' )
	{
		$Domain = trim($Domain);
		
		$this->_Domain = (strtolower($Domain) == 'localhost') ? '' : $Domain;
	}
	

	/**********************************************************************
	 * GetDomain ()
	 * 
	 * Returns the set domain
	 * 
	 * @return string Domain that was set in the constructor or SetDomain()
	 */
	public function GetDomain ()
	{
		return $this->_Domain;
	}
	
	
	/**********************************************************************
	 * SetValidity ()
	 * 
	 * Calculates the cookie lifetime in seconds, which is used to calculate
	 * a valid Unix expiry timestamp when the cookie is set.
	 * The parameters can be combined at will and stand for validity from
	 * current time (e.g. valid for 3 days, 12 hours and 30 minutes).
	 * If no parameter or 0 is given, the cookie's lifetime ends with the current
	 * browser session.
	 * 
	 * @param int $Days    Optional validity in days
	 * @param int $Hours   Optional validity in hours
	 * @param int $Minutes Optional validity in minutes
	 * @param int $Seconds Optional validity in Seconds
	 * 
	 * @return int Calculated validity in seconds
	 * 
	 * @throws CookieHandlerException EC_INVALIDNUMBER if one of the parameters
	 *                                is no number or below 0
	 */
	public function SetValidity ( $Days = 0, $Hours = 0, $Minutes = 0, $Seconds = 0 )
	{
		// SetValidity is also called by different methods, which can pass NULL
		// as parameters
		if (is_null($Days))
			$Days = 0;
		if (is_null($Hours))
			$Hours = 0;
		if (is_null($Minutes))
			$Minutes = 0;
		if (is_null($Seconds))
			$Seconds = 0;
		
		// Are all parameters numeric and greater 0?
		$EC = CookieHandlerException::$EC_INVALIDNUMBER;
		if (is_numeric($Seconds) === FALSE || $Seconds < 0)
			throw new CookieHandlerException ("'$Seconds' is not valid for seconds", $EC);
		else if (is_numeric($Minutes) === FALSE || $Minutes < 0)
			throw new CookieHandlerException ("'$Minutes' is not valid for minutes", $EC);
		else if (is_numeric($Hours) === FALSE || $Hours < 0)
			throw new CookieHandlerException ("'$Hours' is not valid for hours", $EC);
		else if (is_numeric($Days) === FALSE || $Days < 0)
			throw new CookieHandlerException ("'$Days' is not valid for days", $EC);

		$this->_Validity = $Seconds + ($Minutes * 60) +	($Hours * 3600) +	($Days * 86400);

		return $this->_Validity;
	}
	
	
	/**********************************************************************
	 * GetValidity ()
	 * 
	 * Returns the set validity in seconds
	 * 
	 * @return int Validity set for this cookie
	 */
	public function GetValidity ()
	{
		return $this->_Validity;
	}
	
	
	/**********************************************************************
	 * DeleteCookie ()
	 * 
	 * Removes the current cookie with end of the session and deletes all stored
	 * cookie.
	 * 
	 * @throws CookieHandlerException EC_WRITEERROR if the empty cookie content
	 *                                neccessary for deletion couldn't be written
	 */
	public function DeleteCookie ()
	{
		$this->WriteTextContent('', 0);
		unset($_COOKIE[$this->_Name]);
	}

	
	/**********************************************************************
	 * WriteTextContent ()
	 * 
	 * Writes the given text as cookie content. Optional it's possible to directly
	 * set a cookie lifetime as described in SetValidity().
	 * 
	 * Important: Because a cookie is part of the HTTP-header, it has to be set
	 *            BEFORE any other output (even spaces etc.)
	 * 
	 * @param string $TextContent Text to set as cookie content
	 * @param int    $Days        Optional validity in days
	 * @param int    $Hours       Optional validity in hours
	 * @param int    $Minutes     Optional validity in minutes
	 * @param int    $Seconds     Optional validity in Seconds
	 * 
	 * @throws CookieHandlerException EC_WRITEERROR if the text couldn't be written
	 * @throws CookieHandlerException EC_INVALIDNUMBER if one of the validity parameters
	 *                                is no number or below 0
	 */
	public function WriteTextContent ($TextContent, $Days = NULL, $Hours = NULL, $Minutes = NULL, 
		$Seconds = NULL)
	{
		// If lifetime is not NULL, a new cookie validity is set
		if (!is_null($Days))
		{
			$this->SetValidity($Days, $Hours, $Minutes, $Seconds);
		}

		$this->writeContent($TextContent);
	}
	

	/**********************************************************************
	 * WriteJsonContent ()
	 * 
	 * Converts a given array to a JSON string and sets it as cookie content
	 * e.g. Array
	 * 		array ('a'=>1,'b'=>2,'c'=>3,'d'=>4,'e'=>5)
	 * is converted to
	 * 		"{"a":1,"b":2,"c":3,"d":4,"e":5}"
	 * 
	 * Optional it's possible to directly set a cookie lifetime as described in 
	 * SetValidity().
	 * 
	 * Important: Because a cookie is part of the HTTP-header, it has to be set
	 *            BEFORE any other output (even spaces etc.)
	 * 
	 * @param array $JsonArray Array that should be converted to JSON and set as 
	 *                         cookie content
	 * @param int   $Days      Optionale Optional validity in days
	 * @param int   $Hours     Optional validity in hours
	 * @param int   $Minutes   Optional validity in minutes
	 * @param int   $Seconds   Optional validity in Seconds	 
	 * 
	 * @throws CookieHandlerException EC_WRITEERROR if the text couldn't be written
	 * @throws CookieHandlerException EC_INVALIDNUMBER if one of the validity parameters
	 *                                is no number or below 0
	 */
	public function WriteJsonContent ($JsonArray, $Days = NULL, $Hours = NULL, $Minutes = NULL, 
		$Seconds = NULL)
	{
		// If lifetime is not NULL, a new cookie validity is set
		if (!is_null($Days))
		{
			$this->setValidity($Days, $Hours, $Minutes, $Seconds);
		}

		$JsonString = json_encode($JsonArray);
		if ($JsonString == FALSE)
			throw new CookieHandlerException ("Invalid JSON object", CookieHandlerException::$EC_WRITEERROR);
		
		$this->writeContent($JsonString);
	}

	
	/**********************************************************************
	 * WriteObjectContent ()
	 * 
	 * Converts a given PHP object in a writable string and sets it as cookie
	 * content.
	 * Important: It's recommended to use this function with care. In certain
	 *            circumstances an object can be manipulated by third parties.
	 *            It's also important to make sure the converted string content
	 *            doesn't exceed the cookie content length limit vo 4.096 bytes.
	 * 
	 * Optional it's possible to directly set a cookie lifetime as described in 
	 * SetValidity().
	 * 
	 * Important: Because a cookie is part of the HTTP-header, it has to be set
	 *            BEFORE any other output (even spaces etc.)
	 *	
	 * @param mixed Object   PHP object/variable that should be stored as string 
	 *                       as cookie content
	 * @param int   $Days    Optionale Optional validity in days
	 * @param int   $Hours   Optional validity in hours
	 * @param int   $Minutes Optional validity in minutes
	 * @param int   $Seconds Optional validity in Seconds	
	 * 
	 * @throws CookieHandlerException EC_WRITEERROR if the text couldn't be written
	 * @throws CookieHandlerException EC_INVALIDNUMBER if one of the validity parameters
	 *                                is no number or below 0
	 */
	public function WriteObjectContent ($Object, $Days = NULL, $Hours = NULL, 
		$Minutes = NULL, $Seconds = NULL)
	{
		// If lifetime is not NULL, a new cookie validity is set
		if (!is_null($Days))
		{
			$this->setValidity($Days, $Hours, $Minutes, $Seconds);
		}

		return $this->writeContent(serialize($Object));
	}

	
	/**********************************************************************
	 * ReadTextContent ()
	 * 
	 * Reads user's cookie data with the set name and returns it's content
	 * 
	 * @return string Content of the found cookie with set name
	 * 
	 * @throws CookieHandlerException EC_READERROR if the cookie couldn't be found
	 *                                or the content was unreadable
	 */
	public function ReadTextContent ()
	{ 
		$Content = filter_input(INPUT_COOKIE,$this->_Name);
		
		if (is_null($Content) || $Content === FALSE)
			throw new CookieHandlerException ("Cookie-Inhalt von '".$this->_Name."' konnte nicht gelesen werden", 
				CookieHandlerException::$EC_READERROR);
		
		return $Content;
	}
	

	/**********************************************************************
	 * ReadJsonContent ()
	 *
	 * Reads user's cookie data with the set name and returns json string 
	 * content as converted associative array.
	 *	
	 * @return array Countent of the found cookie with the set name as converted
	 *               associative array
	 * 
	 * @throws CookieHandlerException EC_READERROR if the cookie couldn't be found
	 *                                or the content was unreadable
	 */
	public function ReadJsonContent ()
	{
		$Content = $this->ReadTextContent();
		
		return json_decode($Content, TRUE);
	}
	
	
	/**********************************************************************
	 * ReadObjectContent ()
	 *
	 * Read user's cookie data with the set name and returns the content as
	 * deserialised PHP object/variable 
	 *	
	 * @return mixed Content of the found cookie with the set name as deserialised
	 *               PHP object/variable
	 * 
	 * @throws CookieHandlerException EC_READERROR if the cookie couldn't be found
	 *                                or the content was unreadable
	 */
	public function ReadObjectContent ()
	{
		$Content = $this->ReadTextContent();
		
		return unserialize($Content);
	}
	
	
	/**********************************************************************
	 * writeContent ()
	 *
	 * Main function for setting of a cookie (is used by several methods) that
	 * sets the cookie and content with all previously set settings
	 *
	 * @param string $Content Text to set as cookie content
	 * 
	 * @throws CookieHandlerException EC_WRITEERROR if the text couldn't be written
	 */
	private function writeContent ($Content)
	{
		// Since setcookie() doesn't generate any return value that shows errors,
		// there's a workaround. The output buffer is activated, then setcookie()
		// is executed and at the end there's a check, if something was written
		// to the output buffer - if it is so, it's probably an error.
		ob_start();

		setcookie($this->_Name, $Content, ($this->_Validity > 0 ? time() + $this->_Validity : 0),
			$this->_Path, $this->_Domain);

		$strObContent = ob_get_contents();
		if ($strObContent != '')
		{
			$Message = strip_tags($strObContent);
			ob_end_clean();
			
			throw new CookieHandlerException($Message, CookieHandlerException::$EC_WRITEERROR);
		}

		ob_end_clean();
	}
}



