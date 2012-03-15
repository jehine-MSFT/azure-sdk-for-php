<?php

/**
 * LICENSE: Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * 
 * PHP version 5
 *
 * @category  Microsoft
 * @package   PEAR2\Tests\Unit\WindowsAzure\Services\Blob\Models
 * @author    Abdelrahman Elogeel <Abdelrahman.Elogeel@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      http://pear.php.net/package/azure-sdk-for-php
 */
namespace PEAR2\Tests\Unit\WindowsAzure\Services\Blob\Models;
use PEAR2\Tests\Framework\TestResources;
use PEAR2\WindowsAzure\Services\Blob\Models\BlobProperties;
use PEAR2\WindowsAzure\Core\WindowsAzureUtilities;

/**
 * Unit tests for class BlobProperties
 *
 * @category  Microsoft
 * @package   PEAR2\Tests\Unit\WindowsAzure\Services\Blob\Models
 * @author    Abdelrahman Elogeel <Abdelrahman.Elogeel@microsoft.com>
 * @copyright 2012 Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/azure-sdk-for-php
 */
class BlobPropertiesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers PEAR2\WindowsAzure\Services\Blob\Models\BlobProperties::create
     */
    public function testCreate()
    {
        // Setup
        $sample = TestResources::listBlobsOneEntry();
        $expected = $sample['Blobs']['Blob']['Properties'];
        $expectedDate = WindowsAzureUtilities::rfc1123ToDateTime($expected['Last-Modified']);
        
        // Test
        $actual = BlobProperties::create($expected);
        
        // Assert
        $this->assertEquals($expectedDate, $actual->getLastModified());
        $this->assertEquals($expected['Etag'], $actual->getEtag());
        $this->assertEquals(intval($expected['Content-Length']), $actual->getContentLength());
        $this->assertEquals($expected['Content-Type'], $actual->getContentType());
        $this->assertEquals($expected['Content-Encoding'], $actual->getContentEncoding());
        $this->assertEquals($expected['Content-Language'], $actual->getContentLanguage());
        $this->assertEquals($expected['Content-MD5'], $actual->getContentMD5());
        $this->assertEquals($expected['Cache-Control'], $actual->getCacheControl());
        $this->assertEquals(intval($expected['x-ms-blob-sequence-number']), $actual->getSequenceNumber());
        $this->assertEquals($expected['BlobType'], $actual->getBlobType());
        $this->assertEquals($expected['LeaseStatus'], $actual->getLeaseStatus());
    }
    
    /**
     * @covers PEAR2\WindowsAzure\Services\Blob\Models\BlobProperties::setLastModified
     * @covers PEAR2\WindowsAzure\Services\Blob\Models\BlobProperties::getLastModified
     */
    public function testSetLastModified()
    {
        // Setup
        $expected = WindowsAzureUtilities::rfc1123ToDateTime('Sun, 25 Sep 2011 19:42:18 GMT');
        $prooperties = new BlobProperties();
        $prooperties->setLastModified($expected);
        
        // Test
        $prooperties->setLastModified($expected);
        
        // Assert
        $this->assertEquals($expected, $prooperties->getLastModified());
    }
    
    /**
     * @covers PEAR2\WindowsAzure\Services\Blob\Models\BlobProperties::setEtag
     * @covers PEAR2\WindowsAzure\Services\Blob\Models\BlobProperties::getEtag
     */
    public function testSetEtag()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $prooperties = new BlobProperties();
        $prooperties->setEtag($expected);
        
        // Test
        $prooperties->setEtag($expected);
        
        // Assert
        $this->assertEquals($expected, $prooperties->getEtag());
    }
    
    /**
     * @covers PEAR2\WindowsAzure\Services\Blob\Models\BlobProperties::setContentType
     * @covers PEAR2\WindowsAzure\Services\Blob\Models\BlobProperties::getContentType
     */
    public function testSetContentType()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $prooperties = new BlobProperties();
        $prooperties->setContentType($expected);
        
        // Test
        $prooperties->setContentType($expected);
        
        // Assert
        $this->assertEquals($expected, $prooperties->getContentType());
    }
    
    /**
     * @covers PEAR2\WindowsAzure\Services\Blob\Models\BlobProperties::setContentLength
     * @covers PEAR2\WindowsAzure\Services\Blob\Models\BlobProperties::getContentLength
     */
    public function testSetContentLength()
    {
        // Setup
        $expected = 100;
        $prooperties = new BlobProperties();
        $prooperties->setContentLength($expected);
        
        // Test
        $prooperties->setContentLength($expected);
        
        // Assert
        $this->assertEquals($expected, $prooperties->getContentLength());
    }
    
    /**
     * @covers PEAR2\WindowsAzure\Services\Blob\Models\BlobProperties::setContentEncoding
     * @covers PEAR2\WindowsAzure\Services\Blob\Models\BlobProperties::getContentEncoding
     */
    public function testSetContentEncoding()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $prooperties = new BlobProperties();
        $prooperties->setContentEncoding($expected);
        
        // Test
        $prooperties->setContentEncoding($expected);
        
        // Assert
        $this->assertEquals($expected, $prooperties->getContentEncoding());
    }
    
    /**
     * @covers PEAR2\WindowsAzure\Services\Blob\Models\BlobProperties::setContentLanguage
     * @covers PEAR2\WindowsAzure\Services\Blob\Models\BlobProperties::getContentLanguage
     */
    public function testSetContentLanguage()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $prooperties = new BlobProperties();
        $prooperties->setContentLanguage($expected);
        
        // Test
        $prooperties->setContentLanguage($expected);
        
        // Assert
        $this->assertEquals($expected, $prooperties->getContentLanguage());
    }
    
    /**
     * @covers PEAR2\WindowsAzure\Services\Blob\Models\BlobProperties::setContentMD5
     * @covers PEAR2\WindowsAzure\Services\Blob\Models\BlobProperties::getContentMD5
     */
    public function testSetContentMD5()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $prooperties = new BlobProperties();
        $prooperties->setContentMD5($expected);
        
        // Test
        $prooperties->setContentMD5($expected);
        
        // Assert
        $this->assertEquals($expected, $prooperties->getContentMD5());
    }
    
    /**
     * @covers PEAR2\WindowsAzure\Services\Blob\Models\BlobProperties::setCacheControl
     * @covers PEAR2\WindowsAzure\Services\Blob\Models\BlobProperties::getCacheControl
     */
    public function testSetCacheControl()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $prooperties = new BlobProperties();
        $prooperties->setCacheControl($expected);
        
        // Test
        $prooperties->setCacheControl($expected);
        
        // Assert
        $this->assertEquals($expected, $prooperties->getCacheControl());
    }
    
    /**
     * @covers PEAR2\WindowsAzure\Services\Blob\Models\BlobProperties::setBlobType
     * @covers PEAR2\WindowsAzure\Services\Blob\Models\BlobProperties::getBlobType
     */
    public function testSetBlobType()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $prooperties = new BlobProperties();
        $prooperties->setBlobType($expected);
        
        // Test
        $prooperties->setBlobType($expected);
        
        // Assert
        $this->assertEquals($expected, $prooperties->getblobType());
    }
    
    /**
     * @covers PEAR2\WindowsAzure\Services\Blob\Models\BlobProperties::setLeaseStatus
     * @covers PEAR2\WindowsAzure\Services\Blob\Models\BlobProperties::getLeaseStatus
     */
    public function testSetLeaseStatus()
    {
        // Setup
        $expected = '0x8CAFB82EFF70C46';
        $prooperties = new BlobProperties();
        $prooperties->setLeaseStatus($expected);
        
        // Test
        $prooperties->setLeaseStatus($expected);
        
        // Assert
        $this->assertEquals($expected, $prooperties->getLeaseStatus());
    }
    
    /**
     * @covers PEAR2\WindowsAzure\Services\Blob\Models\BlobProperties::setSequenceNumber
     * @covers PEAR2\WindowsAzure\Services\Blob\Models\BlobProperties::getSequenceNumber
     */
    public function testSetSequenceNumber()
    {
        // Setup
        $expected = 123;
        $prooperties = new BlobProperties();
        $prooperties->setSequenceNumber($expected);
        
        // Test
        $prooperties->setSequenceNumber($expected);
        
        // Assert
        $this->assertEquals($expected, $prooperties->getSequenceNumber());
    }
}

?>
