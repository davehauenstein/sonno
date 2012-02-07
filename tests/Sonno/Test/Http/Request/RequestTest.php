<?php

/**
 * @category Sonno
 * @package  Test
 * @author   Dave Hauenstein <davehauenstein@gmail.com>
 * @author   Tharsan Bhuvanendran <me@tharsan.com>
 */

namespace Sonno\Test\Request;

use Sonno\Http\Request\Request;

/**
 * Class level documentation.
 *
 * @category Sonno
 * @package  Test
 */
class RequestTest extends \PHPUnit_Framework_TestCase
{
	public function testConvenienceMethodIsHttpVerb()
	{
	    $methods = array(
 	       'isGet'     => array('GET',     'POST'),
	       'isPost'    => array('POST',    'GET'),
	       'isPut'     => array('PUT',     'POST'),
	       'isDelete'  => array('DELETE',  'POST'),
	       'isHead'    => array('HEAD',    'POST'),
	       'isTrace'   => array('TRACE',   'POST'),
	       'isOptions' => array('OPTIONS', 'POST'),
	    );

        foreach($methods as $method => $tests) {
            $requestData = array('REQUEST_METHOD' => $tests[0]);
    	    $request = new Request($requestData);
    	    $this->assertTrue($request->{$method}());

            $requestData = array('REQUEST_METHOD' => $tests[1]);
    	    $request = new Request($requestData);
    	    $this->assertFalse($request->{$method}());
        }
	}

    public function testGetRequestBodyReturnsExpectedContent()
    {
        $wrapper = './test.txt';
        $data    = 'TESTING' . rand();
        $h = fopen($wrapper, 'w');
        fwrite($h, $data);
        fclose($h);

        Request::setRequestBodyStreamWrapper($wrapper);
        $request = new Request(array('CONTENT_LENGTH' => strlen($data)));
        $body = $request->getRequestBody();

        $this->assertEquals($data, $body);

        unlink($wrapper);
    }

    public function testGetQueryParamsReadsAndParsesQueryParamString()
    {
        $requestData = array('QUERY_STRING' => 'fname=dave&lname=hauenstein');
        $request     = new Request($requestData);
        $params      = $request->getQueryParams();

        $this->assertEquals('dave', $params['fname']);
        $this->assertEquals('hauenstein', $params['lname']);
    }

    public function testGetRequestUriReadsAndParsesRequestUri()
    {
        $requestData = array('REQUEST_URI' => '/phrest-test/');
        $request     = new Request($requestData);
        $requestUri  = $request->getRequestUri();
        $this->assertEquals('/phrest-test', $requestUri);

        $requestData = array('REQUEST_URI' => '/phrest-test/?name=dave');
        $request     = new Request($requestData);
        $requestUri  = $request->getRequestUri();
        $this->assertEquals('/phrest-test', $requestUri);
    }

    public function testRequestObjectParsesRequestMethod()
    {
        $requestData = array('REQUEST_METHOD' => 'GET');
        $request     = new Request($requestData);
        $this->assertEquals('GET', $request->getMethod());
    }

    public function testRequestObjectRetrievesContentLength()
    {
        $requestData = array('CONTENT_LENGTH' => 124);
        $request     = new Request($requestData);
        $this->assertEquals(124, $request->getContentLength());
    }

    public function testRequestObjectRetrievesContentType()
    {
        $requestData = array('CONTENT_TYPE' => 'application/xml');
        $request     = new Request($requestData);
        $this->assertEquals('application/xml', $request->getContentType());
    }

    public function testInstanceOfCurrentRequestIsRetrievable()
    {
        $request = Request::getInstanceOfCurrentRequest();
        $this->assertInstanceOf('Sonno\Http\Request\Request', $request);
    }

    public function testGetQueryParamReturnsExpected()
    {
        $requestData = array('QUERY_STRING' => 'name=dave');
        $request     = new Request($requestData);
        $this->assertEquals($request->getQueryParam('name'), 'dave');
        $this->assertNull($request->getQueryParam('non-existent'));
    }

    public function testIsSecure()
    {
        $requestData = array('HTTPS' => true);
        $request     = new Request($requestData);
        $this->assertEquals(true, $request->isSecure());
    }

    public function testPortNumber()
    {
        $requestData = array('SERVER_PORT' => 31415);
        $request     = new Request($requestData);
        $this->assertEquals(31415, $request->getPort());
    }

    public function testVariantWithHighestQScoreIsSelectedNoQSpecified()
    {
        $variant1 = $this->getMock('Sonno\Http\Variant');
        $variant1->expects($this->any())
                 ->method('getMediaType')
                 ->will($this->returnValue('application/xml'));

        $variant2 = $this->getMock('Sonno\Http\Variant');
        $variant2->expects($this->any())
                 ->method('getMediaType')
                 ->will($this->returnValue('application/json'));

        $variants = array($variant1, $variant2);
        $accept   = array(
            'application/xml',
            'application/json;q=0.5',
        );

        $requestData = array('HTTP_ACCEPT' => implode(',', $accept));
        $request     = new Request($requestData);
        $variant     = $request->selectVariant($variants);

        $this->assertEquals('application/xml', $variant->getMediaType());
    }

    public function testVariantWithHighestQScoreIsSelectedQSpecified()
    {
        $variant1 = $this->getMock('Sonno\Http\Variant');
        $variant1->expects($this->any())
                 ->method('getMediaType')
                 ->will($this->returnValue('application/xml'));

        $variant2 = $this->getMock('Sonno\Http\Variant');
        $variant2->expects($this->any())
                 ->method('getMediaType')
                 ->will($this->returnValue('application/json'));

        $variants = array($variant1, $variant2);
        $accept   = array(
            'application/xml;q=0.6',
            'application/json;q=0.5',
        );

        $requestData = array('HTTP_ACCEPT' => implode(',', $accept));
        $request     = new Request($requestData);
        $variant     = $request->selectVariant($variants);

        $this->assertEquals('application/xml', $variant->getMediaType());
    }

    public function testVariantWithSubTypeWildCardAcceptTypeIsSelectedNoQSpecified()
    {
        $variant1 = $this->getMock('Sonno\Http\Variant');
        $variant1->expects($this->any())
                 ->method('getMediaType')
                 ->will($this->returnValue('application/xml'));

        $variant2 = $this->getMock('Sonno\Http\Variant');
        $variant2->expects($this->any())
                 ->method('getMediaType')
                 ->will($this->returnValue('application/json'));
        $variant3 = $this->getMock('Sonno\Http\Variant');
        $variant3->expects($this->any())
                 ->method('getMediaType')
                 ->will($this->returnValue('text/html'));

        $variants = array($variant1, $variant2, $variant3);
        $accept   = array(
            'text/*',
            'application/xml;q=0.5',
        );

        $requestData = array('HTTP_ACCEPT' => implode(',', $accept));
        $request     = new Request($requestData);
        $variant     = $request->selectVariant($variants);

        $this->assertEquals('text/html', $variant->getMediaType());
    }

    public function testVariantWithSubTypeWildCardAcceptTypeIsSelectedQSpecified()
    {
        $variant1 = $this->getMock('Sonno\Http\Variant');
        $variant1->expects($this->any())
                 ->method('getMediaType')
                 ->will($this->returnValue('application/xml'));

        $variant2 = $this->getMock('Sonno\Http\Variant');
        $variant2->expects($this->any())
                 ->method('getMediaType')
                 ->will($this->returnValue('application/json'));
         $variant3 = $this->getMock('Sonno\Http\Variant');
         $variant3->expects($this->any())
                  ->method('getMediaType')
                  ->will($this->returnValue('text/html'));

        $variants = array($variant1, $variant2, $variant3);
        $accept   = array(
            'text/*;q=0.6',
            'application/xml;q=0.5',
        );

        $requestData = array('HTTP_ACCEPT' => implode(',', $accept));
        $request     = new Request($requestData);
        $variant     = $request->selectVariant($variants);

        $this->assertEquals('text/html', $variant->getMediaType());
    }

    public function testVariantWithTypeAndSubTypeWildCardsAcceptTypeIsSelectedNoQSpecified()
    {
        $variant1 = $this->getMock('Sonno\Http\Variant');
        $variant1->expects($this->any())
                 ->method('getMediaType')
                 ->will($this->returnValue('application/xml'));

        $variant2 = $this->getMock('Sonno\Http\Variant');
        $variant2->expects($this->any())
                 ->method('getMediaType')
                 ->will($this->returnValue('text/html'));

        $variants = array($variant1, $variant2);
        $accept   = array(
            '*/*',
            'application/xml;q=0.5',
        );

        $requestData = array('HTTP_ACCEPT' => implode(',', $accept));
        $request     = new Request($requestData);
        $variant     = $request->selectVariant($variants);

        $this->assertEquals('text/html', $variant->getMediaType());
    }

    public function testVariantWithTypeAndSubTypeWildCardsAcceptTypeIsSelectedQSpecified()
    {
        $variant1 = $this->getMock('Sonno\Http\Variant');
        $variant1->expects($this->any())
                 ->method('getMediaType')
                 ->will($this->returnValue('application/xml'));

        $variant2 = $this->getMock('Sonno\Http\Variant');
        $variant2->expects($this->any())
                 ->method('getMediaType')
                 ->will($this->returnValue('text/html'));

        $variants = array($variant1, $variant2);
        $accept   = array(
            '*/*;q=0.6',
            'application/xml;q=0.5',
        );

        $requestData = array('HTTP_ACCEPT' => implode(',', $accept));
        $request     = new Request($requestData);
        $variant     = $request->selectVariant($variants);

        $this->assertEquals('text/html', $variant->getMediaType());
    }

    public function testSelectVariantIfNoAcceptHeaderIsFound()
    {
        $variant1 = $this->getMock('Sonno\Http\Variant');
        $variant1->expects($this->any())
                 ->method('getMediaType')
                 ->will($this->returnValue('application/xml'));

        $variant2 = $this->getMock('Sonno\Http\Variant');
        $variant2->expects($this->any())
                 ->method('getMediaType')
                 ->will($this->returnValue('text/html'));

        $variants    = array($variant1, $variant2);
        $request     = new Request();
        $variant     = $request->selectVariant($variants);

        $this->assertEquals('application/xml', $variant->getMediaType());
    }

    public function testSelectVariantWillIgnoreVariantsWithAnAcceptQofZero()
    {
        $variant1 = $this->getMock('Sonno\Http\Variant');
        $variant1->expects($this->any())
                 ->method('getMediaType')
                 ->will($this->returnValue('application/xml'));

        $variant2 = $this->getMock('Sonno\Http\Variant');
        $variant2->expects($this->any())
                 ->method('getMediaType')
                 ->will($this->returnValue('text/html'));

        $variants = array($variant1, $variant2);
        $accept   = array(
            'application/xml;q=0',
            'text/html',
        );

        $requestData = array('HTTP_ACCEPT' => implode(',', $accept));
        $request     = new Request($requestData);
        $variant     = $request->selectVariant($variants);

        $this->assertEquals('text/html', $variant->getMediaType());
    }

    public function testSelectVariantWillChooseAMoreSpecificVariant()
    {
        $variant1 = $this->getMock('Sonno\Http\Variant');
        $variant1->expects($this->any())
                 ->method('getMediaType')
                 ->will($this->returnValue('application/xml'));

        $variant2 = $this->getMock('Sonno\Http\Variant');
        $variant2->expects($this->any())
                 ->method('getMediaType')
                 ->will($this->returnValue('application/html+xml'));

        $variants = array($variant1, $variant2);
        $accept   = array(
            'application/html+xml',
            'application/xml',
        );

        $requestData = array('HTTP_ACCEPT' => implode(',', $accept));
        $request     = new Request($requestData);
        $variant     = $request->selectVariant($variants);

        $this->assertEquals('application/html+xml', $variant->getMediaType());
    }

    public function testSelectVariantWillChooseAHigherQOverMoreSpecificVariant()
    {
        $variant1 = $this->getMock('Sonno\Http\Variant');
        $variant1->expects($this->any())
                 ->method('getMediaType')
                 ->will($this->returnValue('application/xml'));

        $variant2 = $this->getMock('Sonno\Http\Variant');
        $variant2->expects($this->any())
                 ->method('getMediaType')
                 ->will($this->returnValue('application/html+xml'));

        $variants = array($variant1, $variant2);
        $accept   = array(
            'application/html+xml;q=0.4',
            'application/xml;q=0.5',
        );

        $requestData = array('HTTP_ACCEPT' => implode(',', $accept));
        $request     = new Request($requestData);
        $variant     = $request->selectVariant($variants);

        $this->assertEquals('application/xml', $variant->getMediaType());
    }

    public function testSelectVariantWillReturnNullIfNoSelectableVariant()
    {
        $variant1 = $this->getMock('Sonno\Http\Variant');
        $variant1->expects($this->any())
                 ->method('getMediaType')
                 ->will($this->returnValue('application/json'));

        $variants = array($variant1);
        $accept   = array(
             'application/html',
             'application/xml',
        );

        $requestData = array('HTTP_ACCEPT' => implode(',', $accept));
        $request     = new Request($requestData);
        $variant     = $request->selectVariant($variants);
        $this->assertEquals(null, $variant);
    }

    public function testEtagPreconditionEvalPassWithIfMatchHeader()
    {
        $requestData = array('HTTP_IF_MATCH' => '"abc123"');
        $request     = new Request($requestData);
        $this->assertNull($request->evaluatePreconditions(null, 'abc123'));
    }

    public function testEtagPreconditionEvalPassWithMultIfMatchHeader()
    {
        $requestData = array('HTTP_IF_MATCH' => '"abc123","123abc"');
        $request     = new Request($requestData);
        $this->assertNull($request->evaluatePreconditions(null, 'abc123'));
        $this->assertNull($request->evaluatePreconditions(null, '123abc'));
    }

    public function testEtagPreconditionEvalPassWithWildCardIfMatchHeader()
    {
        $requestData = array('HTTP_IF_MATCH' => '"*"');
        $request     = new Request($requestData);
        $this->assertNull($request->evaluatePreconditions(null, 'abc123'));
    }

    public function testEtagPreconditionEvalFailWithIfMatchHeader()
    {
        $requestData = array('HTTP_IF_MATCH' => '"abc123"');
        $request     = new Request($requestData);
        $result      = $request->evaluatePreconditions(null, 'abc124');
        $constraint  = $this->isInstanceOf('Sonno\Http\Response\Response');
        $this->assertThat($result, $constraint);
        $this->assertEquals($result->getStatusCode(), 412);
    }

    public function testEtagPreconditionEvalFailWithMultIfMatchHeader()
    {
        $requestData = array('HTTP_IF_MATCH' => '"abc123","123abc"');
        $request     = new Request($requestData);
        $result      = $request->evaluatePreconditions(null, 'abc124');
        $constraint  = $this->isInstanceOf('Sonno\Http\Response\Response');
        $this->assertThat($result, $constraint);
        $this->assertEquals($result->getStatusCode(), 412);
    }

    public function testEtagPreconditionEvalPassWithIfNoneMatchHeader()
    {
        $requestData = array('HTTP_IF_NONE_MATCH' => '"abc123"');
        $request     = new Request($requestData);
        $this->assertNull($request->evaluatePreconditions(null, '123abc'));
    }

    public function testEtagPreconditionEvalPassWithMultIfNoneMatchHeader()
    {
        $requestData = array('HTTP_IF_NONE_MATCH' => '"abc123","123abc"');
        $request     = new Request($requestData);
        $this->assertNull($request->evaluatePreconditions(null, '555test'));
    }

    public function testEtagPreconditionEvalFailWithIfNoneMatchHeader()
    {
        $requestData = array('HTTP_IF_NONE_MATCH' => '"abc123"');
        $request     = new Request($requestData);
        $result      = $request->evaluatePreconditions(null, 'abc123');
        $constraint  = $this->isInstanceOf('Sonno\Http\Response\Response');
        $this->assertThat($result, $constraint);
        $this->assertEquals($result->getStatusCode(), 304);
    }

    public function testEtagPreconditionEvalFailWithMultIfNoneMatchHeader()
    {
        $requestData = array('HTTP_IF_NONE_MATCH' => '"abc123","123abc"');
        $request     = new Request($requestData);
        $result      = $request->evaluatePreconditions(null, '123abc');
        $constraint  = $this->isInstanceOf('Sonno\Http\Response\Response');
        $this->assertThat($result, $constraint);
        $this->assertEquals($result->getStatusCode(), 304);
    }

    public function testEtagPreconditionEvalFailWithWildCardIfNoneMatchHeader()
    {
        $requestData = array('HTTP_IF_NONE_MATCH' => '"*"');
        $request     = new Request($requestData);
        $result      = $request->evaluatePreconditions(null, 'abc123');
        $constraint  = $this->isInstanceOf('Sonno\Http\Response\Response');
        $this->assertThat($result, $constraint);
        $this->assertEquals($result->getStatusCode(), 304);
    }

    public function testModTimePreconditionEvalPasswithIfModHeader()
    {
        $requestData = array(
            'HTTP_IF_MODIFIED_SINCE' => 'Sat, 29 Oct 1994 19:43:31 GMT'
        );
        $request = new Request($requestData);
        $this->assertNull($request->evaluatePreconditions('2011-01-01', null));
    }

    public function testModTimePreconditionEvalFailwithIfModHeader()
    {
        $requestData = array(
            'HTTP_IF_MODIFIED_SINCE' => 'Sat, 29 Oct 1994 19:43:31 GMT'
        );
        $request    = new Request($requestData);
        $result     = $request->evaluatePreconditions('1993-01-01');
        $constraint = $this->isInstanceOf('Sonno\Http\Response\Response');
        $this->assertThat($result, $constraint);
        $this->assertEquals($result->getStatusCode(), 304);
    }

    public function testModTimePreconditionEvalPasswithIfUnModHeader()
    {
        $requestData = array(
            'HTTP_IF_UNMODIFIED_SINCE' => 'Sat, 29 Oct 1994 19:43:31 GMT'
        );
        $request = new Request($requestData);
        $this->assertNull($request->evaluatePreconditions('1993-01-01'));
    }

    public function testModTimePreconditionEvalFailwithIfUnModHeader()
    {
        $requestData = array(
            'HTTP_IF_UNMODIFIED_SINCE' => 'Sat, 29 Oct 1994 19:43:31 GMT'
        );
        $request    = new Request($requestData);
        $result     = $request->evaluatePreconditions('1995-01-01');
        $constraint = $this->isInstanceOf('Sonno\Http\Response\Response');
        $this->assertThat($result, $constraint);
        $this->assertEquals($result->getStatusCode(), 412);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testRecieveExceptionOnSupplyingInvalidDateEvalPrecond()
    {
        $request = new Request();
        $request->evaluatePreconditions('abc123');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testRecieveExceptionOnSupplyingNoArgsToEvalPrecond()
    {
        $request = new Request();
        $request->evaluatePreconditions();
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testRecieveExceptionOnSupplyingBothArgsToEvalPrecond()
    {
        $request = new Request();
        $request->evaluatePreconditions('2011-01-01', 'abc123');
    }
}
