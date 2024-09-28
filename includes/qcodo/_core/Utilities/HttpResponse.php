<?php


namespace Qcodo\Utilities;
use QBaseClass;
use QJsonBaseClass;
use Exception;
use ArrayObject;

class HttpResponse extends QBaseClass {
	protected $statusCode;
	protected $headersArray;
	protected $content;

	/**
	 * HttpResponse constructor.
	 * @param integer $statusCode
	 * @param string|QJsonBaseClass $content if string, this will be treated as-is.  if JsonbaseClass, it will call JsonEncode and will set the content-type accordingly
	 * @param string $contentType
	 * @throws Exception
	 */
	public function __construct($statusCode, $content = null, $contentType = null) {
		$this->statusCode = $statusCode;
		$this->headersArray = array();

		$this->setHeader('Access-Control-Allow-Methods', 'POST, GET, PUT, DELETE, OPTIONS');
		$this->setHeader('Access-Control-Allow-Origin', array_key_exists('HTTP_ORIGIN', $_SERVER) ? $_SERVER['HTTP_ORIGIN'] : '*');
		$this->setHeader('Access-Control-Allow-Credentials', 'true');

		if (array_key_exists('HTTP_ACCESS_CONTROL_REQUEST_HEADERS', $_SERVER)) {
			$this->setHeader('Access-Control-Allow-Headers', $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']);
			$this->setHeader('Access-Control-Expose-Headers', $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']);
		} else {
			$this->setHeader('Access-Control-Allow-Headers', 'session-token, ' .
				"Accept, Accept-CH, Accept-Charset, Accept-Datetime, Accept-Encoding, Accept-Ext, Accept-Features, Accept-Language, Accept-Params, Accept-Ranges, Access-Control-Allow-Credentials, Access-Control-Allow-Headers, Access-Control-Allow-Methods, Access-Control-Allow-Origin, Access-Control-Expose-Headers, Access-Control-Max-Age, Access-Control-Request-Headers, Access-Control-Request-Method, Age, Allow, Alternates, Authentication-Info, Authorization, C-Ext, C-Man, C-Opt, C-PEP, C-PEP-Info, CONNECT, Cache-Control, Compliance, Connection, Content-Base, Content-Disposition, Content-Encoding, Content-ID, Content-Language, Content-Length, Content-Location, Content-MD5, Content-Range, Content-Script-Type, Content-Security-Policy, Content-Style-Type, Content-Transfer-Encoding, Content-Type, Content-Version, Cookie, Cost, DAV, DELETE, DNT, DPR, Date, Default-Style, Delta-Base, Depth, Derived-From, Destination, Differential-ID, Digest, ETag, Expect, Expires, Ext, From, GET, GetProfile, HEAD, HTTP-date, Host, IM, If, If-Match, If-Modified-Since, If-None-Match, If-Range, If-Unmodified-Since, Keep-Alive, Label, Last-Event-ID, Last-Modified, Link, Location, Lock-Token, MIME-Version, Man, Max-Forwards, Media-Range, Message-ID, Meter, Negotiate, Non-Compliance, OPTION, OPTIONS, OWS, Opt, Optional, Ordering-Type, Origin, Overwrite, P3P, PEP, PICS-Label, POST, PUT, Pep-Info, Permanent, Position, Pragma, ProfileObject, Protocol, Protocol-Query, Protocol-Request, Proxy-Authenticate, Proxy-Authentication-Info, Proxy-Authorization, Proxy-Features, Proxy-Instruction, Public, RWS, Range, Referer, Refresh, Resolution-Hint, Resolver-Location, Retry-After, Safe, Sec-Websocket-Extensions, Sec-Websocket-Key, Sec-Websocket-Origin, Sec-Websocket-Protocol, Sec-Websocket-Version, Security-Scheme, Server, Set-Cookie, Set-Cookie2, SetProfile, SoapAction, Status, Status-URI, Strict-Transport-Security, SubOK, Subst, Surrogate-Capability, Surrogate-Control, TCN, TE, TRACE, Timeout, Title, Trailer, Transfer-Encoding, UA-Color, UA-Media, UA-Pixels, UA-Resolution, UA-Windowpixels, URI, Upgrade, User-Agent, Variant-Vary, Vary, Version, Via, Viewport-Width, WWW-Authenticate, Want-Digest, Warning, Width, X-Content-Duration, X-Content-Security-Policy, X-Content-Type-Options, X-CustomHeader, X-DNSPrefetch-Control, X-Forwarded-For, X-Forwarded-Port, X-Forwarded-Proto, X-Frame-Options, X-Modified, X-OTHER, X-PING, X-PINGOTHER, X-Powered-By, X-Requested-With");
			$this->setHeader('Access-Control-Expose-Headers', 'session-token, ' .
				"Accept, Accept-CH, Accept-Charset, Accept-Datetime, Accept-Encoding, Accept-Ext, Accept-Features, Accept-Language, Accept-Params, Accept-Ranges, Access-Control-Allow-Credentials, Access-Control-Allow-Headers, Access-Control-Allow-Methods, Access-Control-Allow-Origin, Access-Control-Expose-Headers, Access-Control-Max-Age, Access-Control-Request-Headers, Access-Control-Request-Method, Age, Allow, Alternates, Authentication-Info, Authorization, C-Ext, C-Man, C-Opt, C-PEP, C-PEP-Info, CONNECT, Cache-Control, Compliance, Connection, Content-Base, Content-Disposition, Content-Encoding, Content-ID, Content-Language, Content-Length, Content-Location, Content-MD5, Content-Range, Content-Script-Type, Content-Security-Policy, Content-Style-Type, Content-Transfer-Encoding, Content-Type, Content-Version, Cookie, Cost, DAV, DELETE, DNT, DPR, Date, Default-Style, Delta-Base, Depth, Derived-From, Destination, Differential-ID, Digest, ETag, Expect, Expires, Ext, From, GET, GetProfile, HEAD, HTTP-date, Host, IM, If, If-Match, If-Modified-Since, If-None-Match, If-Range, If-Unmodified-Since, Keep-Alive, Label, Last-Event-ID, Last-Modified, Link, Location, Lock-Token, MIME-Version, Man, Max-Forwards, Media-Range, Message-ID, Meter, Negotiate, Non-Compliance, OPTION, OPTIONS, OWS, Opt, Optional, Ordering-Type, Origin, Overwrite, P3P, PEP, PICS-Label, POST, PUT, Pep-Info, Permanent, Position, Pragma, ProfileObject, Protocol, Protocol-Query, Protocol-Request, Proxy-Authenticate, Proxy-Authentication-Info, Proxy-Authorization, Proxy-Features, Proxy-Instruction, Public, RWS, Range, Referer, Refresh, Resolution-Hint, Resolver-Location, Retry-After, Safe, Sec-Websocket-Extensions, Sec-Websocket-Key, Sec-Websocket-Origin, Sec-Websocket-Protocol, Sec-Websocket-Version, Security-Scheme, Server, Set-Cookie, Set-Cookie2, SetProfile, SoapAction, Status, Status-URI, Strict-Transport-Security, SubOK, Subst, Surrogate-Capability, Surrogate-Control, TCN, TE, TRACE, Timeout, Title, Trailer, Transfer-Encoding, UA-Color, UA-Media, UA-Pixels, UA-Resolution, UA-Windowpixels, URI, Upgrade, User-Agent, Variant-Vary, Vary, Version, Via, Viewport-Width, WWW-Authenticate, Want-Digest, Warning, Width, X-Content-Duration, X-Content-Security-Policy, X-Content-Type-Options, X-CustomHeader, X-DNSPrefetch-Control, X-Forwarded-For, X-Forwarded-Port, X-Forwarded-Proto, X-Frame-Options, X-Modified, X-OTHER, X-PING, X-PINGOTHER, X-Powered-By, X-Requested-With");
		}

		if (!is_null($content)) {
			if ($content instanceof QJsonBaseClass) {
				if (!$contentType) $contentType = 'application/json';
				$content = $content->JsonEncode();
			} else if (is_array($content) || ($content instanceof ArrayObject)) {
				if (!$contentType) $contentType = 'application/json';
				$content = QJsonBaseClass::JsonEncodeArray($content);
			}

			$this->setContent($content, $contentType);
		}
	}

	public function setContent($content, $contentType = null) {
		$this->content = $content;
		if ($contentType) $this->setHeader('Content-Type', strtolower($contentType));
//		$this->setHeader('Content-Length', strlen($this->content));
	}

	public function setHeader($header, $value = null) {
		if (strpos($header, ':') !== false) throw new Exception("Invalid Header Name: " . $header);

		if (is_null($value)) {
			$this->headersArray[$header] = null;
			unset($this->headersArray[$header]);
		} else {
			$this->headersArray[$header] = $value;
		}
	}

	public function execute() {
		// Set the Response Code itself
		header('X-PHP-Response-Code: ' . $this->statusCode, true, $this->statusCode);
		http_response_code($this->statusCode);

		// Additional Non-Standard Response Code Support (if applicable)
		if (in_array($this->statusCode, array(420))) {
			header(sprintf('HTTP/1.1 %s %s', $this->statusCode, $this->content));
		}

		// Setup Headers
		foreach ($this->headersArray as $header => $value) {
			header(sprintf('%s: %s', $header, $value));
		}

		// Output Content
		print $this->content;

		// Done
		exit();
	}
}
