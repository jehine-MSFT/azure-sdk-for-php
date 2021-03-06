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
 * @package   Tests\Unit\WindowsAzure\Blob
 * @author    Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright Microsoft Corporation
 * @license   http://www.apache.org/licenses/LICENSE-2.0  Apache License 2.0
 * @link      https://github.com/windowsazure/azure-sdk-for-php
 */
namespace Tests\Unit\WindowsAzure\MediaServices;

use Tests\Framework\MediaServicesRestProxyTestBase;
use Tests\Framework\TestResources;
use WindowsAzure\Common\Internal\Resources;
use WindowsAzure\Common\Internal\Utilities;
use WindowsAzure\Common\ServiceException;
use WindowsAzure\Common\Models\ServiceProperties;
use WindowsAzure\MediaServices\Models\Asset;
use WindowsAzure\MediaServices\Models\AccessPolicy;
use WindowsAzure\MediaServices\Models\Locator;
use WindowsAzure\MediaServices\Models\Job;
use WindowsAzure\MediaServices\Models\Task;
use WindowsAzure\MediaServices\Models\TaskOptions;
use WindowsAzure\MediaServices\Models\JobTemplate;
use WindowsAzure\MediaServices\Models\TaskTemplate;
use WindowsAzure\MediaServices\Models\StorageAccount;
use WindowsAzure\MediaServices\Models\IngestManifest;
use WindowsAzure\MediaServices\Models\IngestManifestAsset;
use WindowsAzure\MediaServices\Models\IngestManifestFile;
use WindowsAzure\MediaServices\Models\IngestManifestStatistics;
use WindowsAzure\MediaServices\Models\ContentKey;
use WindowsAzure\MediaServices\Models\ProtectionKeyTypes;
use WindowsAzure\MediaServices\Models\ContentKeyTypes;
use Tests\Framework\VirtualFileSystem;

/**
 * Unit tests for class MediaServicesRestProxy
 *
 * @category Microsoft
 * @package Tests\Unit\WindowsAzure\MediaServices
 * @author Azure PHP SDK <azurephpsdk@microsoft.com>
 * @copyright Microsoft Corporation
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License 2.0
 * @version Release: 0.4.1_2015-03
 * @link https://github.com/windowsazure/azure-sdk-for-php
 */
class MediaServicesRestProxyTest extends MediaServicesRestProxyTestBase
{

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::createAsset
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::deleteAsset
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::send
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::wrapAtomEntry
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::getPropertiesFromAtomEntry
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::_createEntity
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::_deleteEntity
     */
    public function testCreateAsset()
    {
        // Setup
        $asset = new Asset(Asset::OPTIONS_NONE);
        $asset->setName(TestResources::MEDIA_SERVICES_ASSET_NAME . $this->createSuffix());

        // Test
        $result = $this->createAsset($asset);

        // Assert
        $this->assertEquals($asset->getName(), $result->getName());
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::getAsset
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::_getEntity
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::send
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::wrapAtomEntry
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::getPropertiesFromAtomEntry
     */
    public function testGetAsset()
    {
        // Setup
        $asset = new Asset(Asset::OPTIONS_NONE);
        $name = TestResources::MEDIA_SERVICES_ASSET_NAME . $this->createSuffix();
        $asset->setName($name);
        $asset = $this->createAsset($asset);

        // Test
        $result = $this->restProxy->getAsset($asset);

        // Assert
        $this->assertEquals($asset->getId(), $result->getId());
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::getAssetList
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::send
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::wrapAtomEntry
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::getPropertiesFromAtomEntry
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::_getEntityList
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::getEntryList
     */
    public function testGetAssetList()
    {
        // Setup
        $asset = new Asset(Asset::OPTIONS_NONE);
        $asset->setName(TestResources::MEDIA_SERVICES_ASSET_NAME . $this->createSuffix());
        $asset = $this->createAsset($asset);

        // Test
        $result = $this->restProxy->getAssetList();

        // Assert
        $this->assertCount(1, $result);
        $this->assertEquals($asset->getName(), $result[0]->getName());
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::updateAsset
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::_updateEntity
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::send
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::wrapAtomEntry
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::getPropertiesFromAtomEntry
     */
    public function testUpdateAsset()
    {
        // Setup
        $asset = new Asset(Asset::OPTIONS_NONE);
        $asset = $this->createAsset($asset);
        $name = TestResources::MEDIA_SERVICES_ASSET_NAME . $this->createSuffix();

        // Test
        $asset->setName($name);
        $this->restProxy->updateAsset($asset);
        $result = $this->restProxy->getAsset($asset);

        // Assert
        $this->assertEquals($asset->getId(), $result->getId());
        $this->assertEquals($asset->getName(), $result->getName());
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::createAccessPolicy
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::deleteAccessPolicy
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::send
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::wrapAtomEntry
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::getPropertiesFromAtomEntry
     */
    public function testCreateAccessPolicy()
    {
        // Setup
        $name = TestResources::MEDIA_SERVICES_ACCESS_POLICY_NAME . $this->createSuffix();
        $access = new AccessPolicy($name);
        $access->setName(TestResources::MEDIA_SERVICES_ACCESS_POLICY_NAME . $this->createSuffix());
        $access->setDurationInMinutes(30);

        // Test
        $result = $this->createAccessPolicy($access);

        // Assert
        $this->assertEquals($access->getName(), $result->getName());
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::getAccessPolicyList
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::send
     */
    public function testGetAccessPolicyList()
    {
        // Setup
        $accessName = TestResources::MEDIA_SERVICES_ACCESS_POLICY_NAME . $this->createSuffix();

        $access = new AccessPolicy($accessName);
        $access->setDurationInMinutes(30);
        $access->setPermissions(AccessPolicy::PERMISSIONS_WRITE);
        $access = $this->createAccessPolicy($access);

        // Test
        $accessPolicies = $this->restProxy->getAccessPolicyList();

        // Assert
        $this->assertEquals(1, count($accessPolicies));
        $this->assertEquals($accessName, $accessPolicies[0]->getName());
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::getAccessPolicy
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::send
     */
    public function testGetAccessPolicy()
    {
        // Setup
        $accessName = TestResources::MEDIA_SERVICES_ACCESS_POLICY_NAME . $this->createSuffix();

        $access = new AccessPolicy($accessName);
        $access->setDurationInMinutes(30);
        $access->setPermissions(AccessPolicy::PERMISSIONS_WRITE);
        $access = $this->createAccessPolicy($access);

        // Test
        $result = $this->restProxy->getAccessPolicy($access);

        // Assert
        $this->assertEquals($access->getId(), $result->getId());
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::createLocator
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::deleteLocator
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::send
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::wrapAtomEntry
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::getPropertiesFromAtomEntry
     */
    public function testCreateLocator()
    {
        // Setup
        $asset = new Asset(Asset::OPTIONS_NONE);
        $asset->setName(TestResources::MEDIA_SERVICES_ASSET_NAME . $this->createSuffix());
        $asset = $this->createAsset($asset);

        $access = new AccessPolicy(TestResources::MEDIA_SERVICES_ACCESS_POLICY_NAME . $this->createSuffix());
        $access->setDurationInMinutes(30);
        $access->setPermissions(AccessPolicy::PERMISSIONS_READ + AccessPolicy::PERMISSIONS_WRITE + AccessPolicy::PERMISSIONS_DELETE + AccessPolicy::PERMISSIONS_LIST);
        $access = $this->createAccessPolicy($access);

        $locat = new Locator($asset, $access, 1);
        $locat->setName(TestResources::MEDIA_SERVICES_LOCATOR_NAME . $this->createSuffix());

        // Test
        $result = $this->createLocator($locat);

        // Assert
        $this->assertEquals($locat->getName(), $result->getName());
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::createFileInfos
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::uploadAssetFile
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::send
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::wrapAtomEntry
     */
    public function testCreateFileInfos()
    {
        // Setup
        $asset = new Asset(Asset::OPTIONS_NONE);
        $asset->setName(TestResources::MEDIA_SERVICES_ASSET_NAME . $this->createSuffix());
        $asset = $this->createAsset($asset);

        $access = new AccessPolicy(TestResources::MEDIA_SERVICES_ACCESS_POLICY_NAME . $this->createSuffix());
        $access->setDurationInMinutes(30);
        $access->setPermissions(AccessPolicy::PERMISSIONS_WRITE);
        $access = $this->createAccessPolicy($access);

        $locator = new Locator($asset, $access, Locator::TYPE_SAS);
        $locator->setName(TestResources::MEDIA_SERVICES_LOCATOR_NAME . $this->createSuffix());
        $locator->setStartTime(new \DateTime('now -5 minutes'));
        $locator = $this->createLocator($locator);

        $fileName = TestResources::MEDIA_SERVICES_DUMMY_FILE_NAME;
        $this->restProxy->uploadAssetFile($locator, $fileName, TestResources::MEDIA_SERVICES_DUMMY_FILE_CONTENT);

        // Test
        $this->restProxy->createFileInfos($asset);

        // Assert
        $assetFiles = $this->restProxy->getAssetFileList();
        $result = $this->restProxy->getAssetFile($assetFiles[0]);

        $this->assertEquals(1, count($assetFiles));
        $this->assertEquals($fileName, $assetFiles[0]->getName());
        $this->assertEquals($asset->getId(), $assetFiles[0]->getParentAssetId());
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::createJob
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::send
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::deleteJob
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::_getCreateEmptyJobContext
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::_getCreateTaskContext
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::wrapAtomEntry
     */
    public function testCreateJobWithTasks()
    {
        // Setup
        $name = TestResources::MEDIA_SERVICES_JOB_NAME . $this->createSuffix();

        // Test
        $result = $this->createJobWithTasks($name);

        // Assert
        $this->assertEquals($name, $result->getName());
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::getJobStatus
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::send
     */
    public function testGetJobStatus()
    {
        // Setup
        $name = TestResources::MEDIA_SERVICES_JOB_NAME . $this->createSuffix();
        $job = $this->createJobWithTasks($name);

        // Test
        $result = $this->restProxy->getJobStatus($job);

        // Assert
        $this->assertGreaterThanOrEqual(0, $result);
        $this->assertLessThanOrEqual(6, $result);
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::cancelJob
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::send
     */
    public function testCancelJob()
    {
        // Setup
        $name = TestResources::MEDIA_SERVICES_JOB_NAME . $this->createSuffix();
        $job = $this->createJobWithTasks($name);

        // Test
        $job = $this->restProxy->cancelJob($job);

        // Assert
        $this->assertNull($job);
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::createJobTemplate
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::send
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::deleteJobTemplate
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::_getCreateEmptyJobTemplateContext
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::_getCreateTaskTemplateContext
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::wrapAtomEntry
     */
    public function testCreateJobTemplate()
    {
        // Setup
        $name = TestResources::MEDIA_SERVICES_JOB_TEMPLATE_NAME . $this->createSuffix();

        // Test
        $result = $this->createJobTemplateWithTasks($name);

        // Assert
        $this->assertEquals($name, $result->getName());
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::getAssetLocators
     */
    public function testGetAssetLocators(){

        // Setup
        $asset = new Asset(Asset::OPTIONS_NONE);
        $asset->setName(TestResources::MEDIA_SERVICES_ASSET_NAME . $this->createSuffix());
        $asset = $this->createAsset($asset);

        $access = new AccessPolicy(TestResources::MEDIA_SERVICES_ACCESS_POLICY_NAME . $this->createSuffix());
        $access->setDurationInMinutes(30);
        $access->setPermissions(AccessPolicy::PERMISSIONS_READ + AccessPolicy::PERMISSIONS_WRITE + AccessPolicy::PERMISSIONS_DELETE + AccessPolicy::PERMISSIONS_LIST);
        $access = $this->createAccessPolicy($access);

        $locator = new Locator($asset, $access, Locator::TYPE_SAS);
        $locator->setName(TestResources::MEDIA_SERVICES_LOCATOR_NAME . $this->createSuffix());
        $locator = $this->createLocator($locator);

        // Test
        $result = $this->restProxy->getAssetLocators($asset);

        // Assert
        $this->assertEquals($asset->getId(), $result[0]->getAssetId());
        $this->assertEquals($access->getId(), $result[0]->getAccessPolicyId());
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::getAssetStorageAccount
     */
    public function testGetAssetStorageAccount(){

        // Setup
        $asset = new Asset(Asset::OPTIONS_NONE);
        $asset->setName(TestResources::MEDIA_SERVICES_ASSET_NAME . $this->createSuffix());
        $asset = $this->createAsset($asset);

        // Test
        $result = $this->restProxy->getAssetStorageAccount($asset);

        // Assert
        $this->assertNotEmpty($result);
        $this->assertNotEmpty($result->getName());
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::getLocator
     */
    public function testGetLocator(){

        // Setup
        $asset = new Asset(Asset::OPTIONS_NONE);
        $asset->setName(TestResources::MEDIA_SERVICES_ASSET_NAME . $this->createSuffix());
        $asset = $this->createAsset($asset);

        $access = new AccessPolicy(TestResources::MEDIA_SERVICES_ACCESS_POLICY_NAME . $this->createSuffix());
        $access->setDurationInMinutes(30);
        $access->setPermissions(AccessPolicy::PERMISSIONS_READ + AccessPolicy::PERMISSIONS_WRITE + AccessPolicy::PERMISSIONS_DELETE + AccessPolicy::PERMISSIONS_LIST);
        $access = $this->createAccessPolicy($access);

        $locator = new Locator($asset, $access, Locator::TYPE_SAS);
        $locator->setName(TestResources::MEDIA_SERVICES_LOCATOR_NAME . $this->createSuffix());
        $locator = $this->createLocator($locator);

        // Test
        $result = $this->restProxy->getLocator($locator);

        // Assert
        $this->assertEquals($locator->getId(), $result->getId());
        $this->assertEquals($asset->getId(), $result->getAssetId());
        $this->assertEquals($access->getId(), $result->getAccessPolicyId());
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::getLocatorAccessPolicy
     */
    public function testGetLocatorAccessPolicy(){

        // Setup
        $asset = new Asset(Asset::OPTIONS_NONE);
        $asset->setName(TestResources::MEDIA_SERVICES_ASSET_NAME . $this->createSuffix());
        $asset = $this->createAsset($asset);

        $access = new AccessPolicy(TestResources::MEDIA_SERVICES_ACCESS_POLICY_NAME . $this->createSuffix());
        $access->setDurationInMinutes(30);
        $access->setPermissions(AccessPolicy::PERMISSIONS_READ + AccessPolicy::PERMISSIONS_WRITE + AccessPolicy::PERMISSIONS_DELETE + AccessPolicy::PERMISSIONS_LIST);
        $access = $this->createAccessPolicy($access);

        $locator = new Locator($asset, $access, Locator::TYPE_SAS);
        $locator->setName(TestResources::MEDIA_SERVICES_LOCATOR_NAME . $this->createSuffix());
        $locator = $this->createLocator($locator);

        // Test
        $result = $this->restProxy->getLocatorAccessPolicy($locator);

        // Assert
        $this->assertEquals($access->getId(), $result->getId());
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::getLocatorAsset
     */
    public function testGetLocatorAsset(){

        // Setup
        $asset = new Asset(Asset::OPTIONS_NONE);
        $asset->setName(TestResources::MEDIA_SERVICES_ASSET_NAME . $this->createSuffix());
        $asset = $this->createAsset($asset);

        $access = new AccessPolicy(TestResources::MEDIA_SERVICES_ACCESS_POLICY_NAME . $this->createSuffix());
        $access->setDurationInMinutes(30);
        $access->setPermissions(AccessPolicy::PERMISSIONS_READ + AccessPolicy::PERMISSIONS_WRITE + AccessPolicy::PERMISSIONS_DELETE + AccessPolicy::PERMISSIONS_LIST);
        $access = $this->createAccessPolicy($access);

        $locator = new Locator($asset, $access, Locator::TYPE_SAS);
        $locator->setName(TestResources::MEDIA_SERVICES_LOCATOR_NAME . $this->createSuffix());
        $locator = $this->createLocator($locator);

        // Test
        $result = $this->restProxy->getLocatorAsset($locator);

        // Assert
        $this->assertEquals($asset->getId(), $result->getId());
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::getLocatorList
     */
    public function testGetLocatorList(){

        // Setup
        $asset = new Asset(Asset::OPTIONS_NONE);
        $asset->setName(TestResources::MEDIA_SERVICES_ASSET_NAME . $this->createSuffix());
        $asset = $this->createAsset($asset);

        $access = new AccessPolicy(TestResources::MEDIA_SERVICES_ACCESS_POLICY_NAME . $this->createSuffix());
        $access->setDurationInMinutes(30);
        $access->setPermissions(AccessPolicy::PERMISSIONS_READ + AccessPolicy::PERMISSIONS_WRITE + AccessPolicy::PERMISSIONS_DELETE + AccessPolicy::PERMISSIONS_LIST);
        $access = $this->createAccessPolicy($access);

        $locator = new Locator($asset, $access, Locator::TYPE_SAS);
        $locator->setName(TestResources::MEDIA_SERVICES_LOCATOR_NAME . $this->createSuffix());
        $locator = $this->createLocator($locator);

        // Test
        $result = $this->restProxy->getLocatorList();

        // Assert
        $this->assertCount(1, $result);
        $this->assertEquals($locator->getName(), $result[0]->getName());
        $this->assertEquals($locator->getId(), $result[0]->getId());
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::updateLocator
     */
    public function testUpdateLocator(){

        // Setup
        $asset = new Asset(Asset::OPTIONS_NONE);
        $asset->setName(TestResources::MEDIA_SERVICES_ASSET_NAME . $this->createSuffix());
        $asset = $this->createAsset($asset);

        $access = new AccessPolicy(TestResources::MEDIA_SERVICES_ACCESS_POLICY_NAME . $this->createSuffix());
        $access->setDurationInMinutes(30);
        $access->setPermissions(AccessPolicy::PERMISSIONS_READ);
        $access = $this->createAccessPolicy($access);

        $locator = new Locator($asset, $access, Locator::TYPE_ON_DEMAND_ORIGIN);
        $locator->setName(TestResources::MEDIA_SERVICES_LOCATOR_NAME . $this->createSuffix());
        $locator = $this->createLocator($locator);
        $newName = TestResources::MEDIA_SERVICES_LOCATOR_NAME . $this->createSuffix();

        // Test
        $locator->setName($newName);
        $this->restProxy->updateLocator($locator);

        // Assert
        $this->assertEquals($newName, $locator->getName());
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::getAssetFileList
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::send
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::wrapAtomEntry
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::getPropertiesFromAtomEntry
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::getAssetFile
     */
    public function testGetAssetFile()
    {
        // Setup
        $asset = $this->createAssetWithFile();
        $assetFiles = $this->restProxy->getAssetFileList();

        // Test
        $result = $this->restProxy->getAssetFile($assetFiles[0]);

        // Assert
        $this->assertEquals($assetFiles[0]->getName(), $result->getName());
        $this->assertEquals($asset->getId(), $result->getParentAssetId());
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::updateAssetFile
     */
    public function testUpdateAssetFile(){

        // Setup
        $asset = $this->createAssetWithFile();
        $newFileName = TestResources::MEDIA_SERVICES_DUMMY_FILE_NAME_1;
        $assetFiles = $this->restProxy->getAssetFileList();

        // Test
        $assetFiles[0]->setName($newFileName);
        $this->restProxy->updateAssetFile($assetFiles[0]);
        $result = $assetFiles[0]->getName();

        // Assert
        $this->assertEquals($newFileName, $result);
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::getJob
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::getJobList
     */
    public function testGetJob(){

        //Setup
        $job = $this->createJobWithTasks(TestResources::MEDIA_SERVICES_JOB_NAME . $this->createSuffix());
        $jobList = $this->restProxy->getJobList();

        // Test
        $result = $this->restProxy->getJob($jobList[0]);

        // Assert
        $this->assertEquals($job->getId(), $result->getId());
        $this->assertEquals($job->getName(), $result->getName());
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::getJobTasks
     */
    public function testGetJobTasks(){

        //Setup
        $asset = $this->createAssetWithFile();
        $outputAssetName = $this->getOutputAssetName();

        $taskBody = '<?xml version="1.0" encoding="utf-8"?><taskBody><inputAsset>JobInputAsset(0)</inputAsset><outputAsset assetCreationOptions="0" assetName="' . $outputAssetName . '">JobOutputAsset(0)</outputAsset></taskBody>';
        $mediaProcessorId = 'nb:mpid:UUID:2e7aa8f3-4961-4e0c-b4db-0e0439e524f5';
        $task = new Task($taskBody, $mediaProcessorId, TaskOptions::NONE);
        $task->setConfiguration('H.264 HD 720p VBR');

        $job = new Job();
        $job->setName(TestResources::MEDIA_SERVICES_JOB_NAME . $this->createSuffix());
        $job = $this->createJob($job, array($asset), array($task));

        // Test
        $result = $this->restProxy->getJobTasks($job);

        // Assert
        $this->assertEquals($mediaProcessorId, $result[0]->getMediaProcessorId());
        $this->assertEquals($taskBody, $result[0]->getTaskBody());
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::getJobInputMediaAssets
     */
    public function testGetJobInputMediaAssets(){

        //Setup
        $asset = $this->createAssetWithFile();
        $outputAssetName = $this->getOutputAssetName();

        $taskBody = '<?xml version="1.0" encoding="utf-8"?><taskBody><inputAsset>JobInputAsset(0)</inputAsset><outputAsset assetCreationOptions="0" assetName="' . $outputAssetName . '">JobOutputAsset(0)</outputAsset></taskBody>';
        $mediaProcessorId = 'nb:mpid:UUID:2e7aa8f3-4961-4e0c-b4db-0e0439e524f5';
        $task = new Task($taskBody, $mediaProcessorId, TaskOptions::NONE);
        $task->setConfiguration('H.264 HD 720p VBR');

        $job = new Job();
        $job->setName(TestResources::MEDIA_SERVICES_JOB_NAME . $this->createSuffix());
        $job = $this->createJob($job, array($asset), array($task));

        // Test
        $result = $this->restProxy->getJobInputMediaAssets($job);

        // Assert
        $this->assertEquals($asset->getId(), $result[0]->getId());
        $this->assertEquals($asset->getName(), $result[0]->getName());
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::getMediaProcessors
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::send
     */
    public function testGetMediaProcessors()
    {
        // Test
        $result = $this->restProxy->getMediaProcessors();

        // Assert
        $this->assertNotEmpty($result);
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::getLatestMediaProcessor
     */
    public function testGetLatestMediaProcessor()
    {
        // Setup
        $name = TestResources::MEDIA_SERVICES_PROCESSOR_NAME;

        // Test
        $result = $this->restProxy->getLatestMediaProcessor($name);

        // Assert
        $this->assertNotNull($result);
        $this->assertEquals($name, $result->getName());
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::getJobOutputMediaAssets
     */
    public function testGetJobOutputMediaAssets(){

        //Setup
        $asset = $this->createAssetWithFile();
        $outputAssetName = $this->getOutputAssetName();

        $taskBody = '<?xml version="1.0" encoding="utf-8"?><taskBody><inputAsset>JobInputAsset(0)</inputAsset><outputAsset assetCreationOptions="0" assetName="' . $outputAssetName . '">JobOutputAsset(0)</outputAsset></taskBody>';
        $mediaProcessorId = 'nb:mpid:UUID:2e7aa8f3-4961-4e0c-b4db-0e0439e524f5';
        $task = new Task($taskBody, $mediaProcessorId, TaskOptions::NONE);
        $task->setConfiguration('H.264 HD 720p VBR');

        $job = new Job();
        $job->setName(TestResources::MEDIA_SERVICES_JOB_NAME . $this->createSuffix());
        $job = $this->createJob($job, array($asset), array($task));

        // Test
        $result = $this->restProxy->getJobOutputMediaAssets($job);

        // Assert
        $this->assertNotEquals($asset->getId(), $result[0]->getId());
        $this->assertEquals($outputAssetName, $result[0]->getName());
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::getTaskList
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::_getEntityList
     */
    public function testGetTaskList(){

        // Setup
        $asset = $this->createAssetWithFile();
        $outputAssetName = $this->getOutputAssetName();

        $taskBody = '<?xml version="1.0" encoding="utf-8"?><taskBody><inputAsset>JobInputAsset(0)</inputAsset><outputAsset assetCreationOptions="0" assetName="' . $outputAssetName . '">JobOutputAsset(0)</outputAsset></taskBody>';
        $mediaProcessorId = 'nb:mpid:UUID:2e7aa8f3-4961-4e0c-b4db-0e0439e524f5';
        $task = new Task($taskBody, $mediaProcessorId, TaskOptions::NONE);
        $task->setConfiguration('H.264 HD 720p VBR');

        $job = new Job();
        $job->setName(TestResources::MEDIA_SERVICES_JOB_NAME . $this->createSuffix());
        $job = $this->createJob($job, array($asset), array($task));

        // Test
        $result = $this->restProxy->getTaskList();

        // Assert
        $this->assertEquals(1, count($result));
        $this->assertEquals($task->getName(), $result[0]->getName());
        $this->assertEquals($taskBody, $result[0]->getTaskBody());
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::getJobTemplate
     */
    public function testGetJobTemplate()
    {
        // Setup
        $name = TestResources::MEDIA_SERVICES_JOB_TEMPLATE_NAME . $this->createSuffix();
        $jobTemplate = $this->createJobTemplateWithTasks($name);

        // Test
        $result = $this->restProxy->getJobTemplate($jobTemplate);

        // Assert
        $this->assertEquals($name, $result->getName());
        $this->assertequals($jobTemplate->getId(), $result->getId());
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::getJobTemplateList
     */
    public function testGetJobTemplateList()
    {
        // Setup
        $name = TestResources::MEDIA_SERVICES_JOB_TEMPLATE_NAME . $this->createSuffix();
        $jobTemplate = $this->createJobTemplateWithTasks($name);

        // Test
        $result = $this->restProxy->getJobTemplateList();

        // Assert
        $this->assertEquals(1, count($result));
        $this->assertEquals($name, $result[0]->getName());
        $this->assertequals($jobTemplate->getId(), $result[0]->getId());
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::getJobTemplateTaskTemplateList
     */
    public function testGetJobTemplateTaskTemplateList(){

        // Setup
        $mediaProcessor = $this->restProxy->getLatestMediaProcessor('Windows Azure Media Encoder');
        $configuration = 'H.264 HD 720p VBR';
        $name = TestResources::MEDIA_SERVICES_JOB_TEMPLATE_NAME . $this->createSuffix();

        $jobTempl = $this->createJobTemplateWithTasks($name);

        // Test
        $result = $this->restProxy->getJobTemplateTaskTemplateList($jobTempl);

        // Assert
        $this->assertEquals(1, count($result));
        $this->assertequals($configuration, $result[0]->getConfiguration());
        $this->assertequals($mediaProcessor->getId(), $result[0]->getMediaProcessorId());
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::getTaskTemplateList
     */
    public function testGetTaskTemplateList(){

        // Setup
       $name = TestResources::MEDIA_SERVICES_JOB_TEMPLATE_NAME . $this->createSuffix();
       $mediaProcessor = $this->restProxy->getLatestMediaProcessor('Windows Azure Media Encoder');
       $configuration = 'H.264 HD 720p VBR';

        $jobTempl = $this->createJobTemplateWithTasks($name);

        // Test
        $result = $this->restProxy->getTaskTemplateList();

        // Assert
        $this->assertEquals(1, count($result));
        $this->assertEquals($mediaProcessor->getId(), $result[0]->getMediaProcessorId());
        $this->assertEquals($configuration, $result[0]->getConfiguration());

    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::getAssetAssetFileList
     */
    public function testGetAssetAssetFileList(){

        // Setup
        $asset = $this->createAssetWithFile();

        // Test
        $result = $this->restProxy->getAssetAssetFileList($asset);

        // Assert
        $this->assertEquals(1, count($result));
        $this->assertEquals($asset->getId(), $result[0]->getParentAssetId());
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::getAssetParentAssets
     */
    public function testGetAssetParentAsset(){

        // Setup
        $name = $this->getOutputAssetName();
        $mediaProcessor = $this->restProxy->getLatestMediaProcessor('Windows Azure Media Encoder');
        $inputAsset = $this->createAssetWithFile();

        $taskBody = '<?xml version="1.0" encoding="utf-8"?><taskBody><inputAsset>JobInputAsset(0)</inputAsset><outputAsset assetCreationOptions="0" assetName="' . $name . '">JobOutputAsset(0)</outputAsset></taskBody>';
        $task = new Task($taskBody, $mediaProcessor->getId(), TaskOptions::NONE);
        $task->setConfiguration('H.264 HD 720p VBR');

        $job = new Job();
        $job->setName($name);
        $job = $this->createJob($job, array($inputAsset), array($task));

        $assetList = $this->restProxy->getAssetList();

        // Test
        foreach($assetList as $assetElement){
            if (strcmp($assetElement->getName(), $name) == 0) {
                $parentAssetId = $this->restProxy->getAssetParentAssets($assetElement);
            }
        }

        // Assert
        $this->assertEquals(1, count($parentAssetId));
        $this->assertEquals($inputAsset->getId(),$parentAssetId[0]->getId());
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::createIngestManifest
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::deleteIngestManifest
     */
    public function testCreateIngestManifest(){

        // Setup
        $ingestManifest = new IngestManifest();
        $name = TestResources::MEDIA_SERVICES_INGEST_MANIFEST . $this->createSuffix();
        $ingestManifest->setName($name);

        // Test
        $ingestManifest = $this->createIngestManifest($ingestManifest);

        // Assert
        $this->assertEquals($name, $ingestManifest->getName());
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::getIngestManifest
     */
    public function testGetIngestManifest(){

        // Setup
        $ingestManifest = new IngestManifest();
        $name = TestResources::MEDIA_SERVICES_INGEST_MANIFEST . $this->createSuffix();
        $ingestManifest->setName($name);
        $ingestManifest = $this->createIngestManifest($ingestManifest);

        // Test
        $result = $this->restProxy->getIngestManifest($ingestManifest);

        // Assert
        $this->assertEquals($name, $result->getName());
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::getIngestManifestList
     */
    public function testGetIngestManifestList(){

        // Setup
        $ingestManifest = new IngestManifest();
        $name = TestResources::MEDIA_SERVICES_INGEST_MANIFEST . $this->createSuffix();
        $ingestManifest->setName($name);
        $ingestManifest = $this->createIngestManifest($ingestManifest);

        // Test
        $result = $this->restProxy->getIngestManifestList();

        // Assert
        $this->assertCount(1, $result);
        $this->assertEquals($name, $result[0]->getName());
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::getIngestManifestAssets
     */
    public function testGetIngestManifestAssets(){

        // Setup
        $ingestManifest = new IngestManifest();
        $name = TestResources::MEDIA_SERVICES_INGEST_MANIFEST . $this->createSuffix();
        $ingestManifest->setName($name);
        $ingestManifest = $this->createIngestManifest($ingestManifest);
        $ingestManifestFileName = TestResources::MEDIA_SERVICES_DUMMY_FILE_NAME;

        $asset = new Asset(Asset::OPTIONS_NONE);
        $asset->setName(TestResources::MEDIA_SERVICES_ASSET_NAME . $this->createSuffix());
        $asset = $this->createAsset($asset);

        $ingestManifestAsset = new IngestManifestAsset($ingestManifest->getId());
        $ingestManifestAsset = $this->createIngestManifestAsset($ingestManifestAsset, $asset);

        $ingestManifestFile = new IngestManifestFile($ingestManifestFileName, $ingestManifest->getId(), $ingestManifestAsset->getId());

        $ingestManifestFile = $this->createIngestManifestFile($ingestManifestFile);

        // Test
        $result = $this->restProxy->getIngestManifestAssets($ingestManifest);

        // Assert
        $this->assertCount(1, $result);
        $this->assertEquals($ingestManifest->getId(), $result[0]->getParentIngestManifestId());
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::getPendingIngestManifestAssets
     */
    public function testGetPendingIngestManifestAssets(){

        // Setup
        $ingestManifest = new IngestManifest();
        $name = TestResources::MEDIA_SERVICES_INGEST_MANIFEST . $this->createSuffix();
        $ingestManifest->setName($name);
        $ingestManifest = $this->createIngestManifest($ingestManifest);
        $ingestManifestFileName = TestResources::MEDIA_SERVICES_DUMMY_FILE_NAME;

        $asset = new Asset(Asset::OPTIONS_NONE);
        $asset->setName(TestResources::MEDIA_SERVICES_ASSET_NAME . $this->createSuffix());
        $asset = $this->createAsset($asset);

        $ingestManifestAsset = new IngestManifestAsset($ingestManifest->getId());
        $ingestManifestAsset = $this->createIngestManifestAsset($ingestManifestAsset, $asset);

        $ingestManifestFile = new IngestManifestFile($ingestManifestFileName, $ingestManifest->getId(), $ingestManifestAsset->getId());

        $ingestManifestFile = $this->createIngestManifestFile($ingestManifestFile);

        // Test
        $result = $this->restProxy->getPendingIngestManifestAssets($ingestManifest);

        // Assert
        $this->assertCount(1, $result);
        $this->assertEquals($ingestManifest->getId(), $result[0]->getParentIngestManifestId());
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::getIngestManifestStorageAccount
     */
    public function testGetIngestManifestStorageAccount(){

        // Setup
        $ingestManifest = new IngestManifest();
        $name = TestResources::MEDIA_SERVICES_INGEST_MANIFEST . $this->createSuffix();
        $ingestManifest->setName($name);
        $ingestManifest = $this->createIngestManifest($ingestManifest);

        $connectionParameters = TestResources::getMediaServicesConnectionParameters();
        $storageAccountName = $connectionParameters['accountName'];

        // Test
        $result = $this->restProxy->getIngestManifestStorageAccount($ingestManifest);

        // Assert
        $this->assertEquals($ingestManifest->getStorageAccountName(), $result->getName());
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::updateIngestManifest
     */
    public function testUpdateIngestManifest(){

        // Setup
        $ingestManifest = new IngestManifest();
        $name = TestResources::MEDIA_SERVICES_INGEST_MANIFEST . $this->createSuffix();
        $ingestManifest->setName($name);
        $ingestManifest = $this->createIngestManifest($ingestManifest);
        $name = TestResources::MEDIA_SERVICES_INGEST_MANIFEST . $this->createSuffix();

        // Test
        $ingestManifest->setName($name);
        $this->restProxy->updateIngestManifest($ingestManifest);

        // Assert
        $this->assertEquals($name, $ingestManifest->getName());
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::createIngestManifestAsset
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::deleteIngestManifestAsset
     */
    public function testCreateIngestManifestAsset(){

        //  Setup
        $ingestManifest = new IngestManifest();
        $name = TestResources::MEDIA_SERVICES_INGEST_MANIFEST . $this->createSuffix();
        $ingestManifest->setName($name);
        $ingestManifest = $this->createIngestManifest($ingestManifest);

        $asset = new Asset(Asset::OPTIONS_NONE);
        $asset->setName(TestResources::MEDIA_SERVICES_ASSET_NAME . $this->createSuffix());
        $asset = $this->createAsset($asset);

        $ingestManifestAsset = new IngestManifestAsset($ingestManifest->getId());

        // Test
        $ingestManifestAsset = $this->createIngestManifestAsset($ingestManifestAsset, $asset);

        // Assert
        $this->assertEquals($ingestManifest->getId(), $ingestManifestAsset->getParentIngestManifestId());
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::getIngestManifestAsset
     */
    public function testGetIngestManifestAsset(){

        //  Setup
        $ingestManifest = new IngestManifest();
        $name = TestResources::MEDIA_SERVICES_INGEST_MANIFEST . $this->createSuffix();
        $ingestManifest->setName($name);
        $ingestManifest = $this->createIngestManifest($ingestManifest);

        $asset = new Asset(Asset::OPTIONS_NONE);
        $asset->setName(TestResources::MEDIA_SERVICES_ASSET_NAME . $this->createSuffix());
        $asset = $this->createAsset($asset);

        $ingestManifestAsset = new IngestManifestAsset($ingestManifest->getId());
        $ingestManifestAsset = $this->createIngestManifestAsset($ingestManifestAsset, $asset);

        // Test
        $result = $this->restProxy->getIngestManifestAsset($ingestManifestAsset);

        // Assert
        $this->assertEquals($ingestManifest->getId(), $result->getParentIngestManifestId());
        $this->assertEquals($ingestManifestAsset->getId(), $result->getId());
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::getIngestManifestAssetList
     */
    public function testGetIngestManifestAssetList(){

        //  Setup
        $ingestManifest = new IngestManifest();
        $name = TestResources::MEDIA_SERVICES_INGEST_MANIFEST . $this->createSuffix();
        $ingestManifest->setName($name);
        $ingestManifest = $this->createIngestManifest($ingestManifest);

        $asset = new Asset(Asset::OPTIONS_NONE);
        $asset->setName(TestResources::MEDIA_SERVICES_ASSET_NAME . $this->createSuffix());
        $asset = $this->createAsset($asset);

        $ingestManifestAsset = new IngestManifestAsset($ingestManifest->getId());
        $ingestManifestAsset = $this->createIngestManifestAsset($ingestManifestAsset, $asset);

        // Test
        $result = $this->restProxy->getIngestManifestAssetList();

        // Assert
        $this->assertCount(1, $result);
        $this->assertEquals($ingestManifestAsset->getId(), $result[0]->getId());
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::getIngestManifestAssetFiles
     */
    public function testGetIngestManifestAssetFiles(){

        //  Setup
        $ingestManifest = new IngestManifest();
        $name = TestResources::MEDIA_SERVICES_INGEST_MANIFEST . $this->createSuffix();
        $ingestManifest->setName($name);
        $ingestManifest = $this->createIngestManifest($ingestManifest);
        $ingestAssetFileName = TestResources::MEDIA_SERVICES_DUMMY_FILE_NAME;

        $asset = new Asset(Asset::OPTIONS_NONE);
        $asset->setName(TestResources::MEDIA_SERVICES_ASSET_NAME . $this->createSuffix());
        $asset = $this->createAsset($asset);

        $ingestManifestAsset = new IngestManifestAsset($ingestManifest->getId());
        $ingestManifestAsset = $this->createIngestManifestAsset($ingestManifestAsset, $asset);

        $ingestManifestAssetFile = new IngestManifestFile($ingestAssetFileName, $ingestManifest->getId(), $ingestManifestAsset->getId());
        $ingestManifestAssetFile = $this->createIngestManifestFile($ingestManifestAssetFile);

        // Test
        $result = $this->restProxy->getIngestManifestAssetFiles($ingestManifestAsset);

        // Assert
        $this->assertCount(1, $result);
        $this->assertEquals($ingestManifest->getId(), $result[0]->getParentIngestManifestId());
        $this->assertEquals($ingestManifestAsset->getId(), $result[0]->getParentIngestManifestAssetId());
        $this->assertEquals($ingestAssetFileName, $result[0]->getName());
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::createIngestManifestFile
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::deleteIngestManifestFile
     */
    public function testCreateIngestManifestFile(){

        //  Setup
        $ingestManifest = new IngestManifest();
        $name = TestResources::MEDIA_SERVICES_INGEST_MANIFEST . $this->createSuffix();
        $ingestManifest->setName($name);
        $ingestManifest = $this->createIngestManifest($ingestManifest);
        $ingestAssetFileName = TestResources::MEDIA_SERVICES_DUMMY_FILE_NAME;

        $asset = new Asset(Asset::OPTIONS_NONE);
        $asset->setName(TestResources::MEDIA_SERVICES_ASSET_NAME . $this->createSuffix());
        $asset = $this->createAsset($asset);

        $ingestManifestAsset = new IngestManifestAsset($ingestManifest->getId());
        $ingestManifestAsset = $this->createIngestManifestAsset($ingestManifestAsset, $asset);

        $ingestManifestFile = new IngestManifestFile($ingestAssetFileName, $ingestManifest->getId(), $ingestManifestAsset->getId());

        // Test
        $ingestManifestFile = $this->createIngestManifestFile($ingestManifestFile);

        // Assert
        $this->assertEquals($ingestManifest->getId(), $ingestManifestFile->getParentIngestManifestId());
        $this->assertEquals($ingestManifestAsset->getId(), $ingestManifestFile->getParentIngestManifestAssetId());
        $this->assertEquals($ingestAssetFileName, $ingestManifestFile->getName());
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::getIngestManifestFile
     */
    public function testGetIngestManifestFile() {

        // Setup
        $ingestManifest = new IngestManifest();
        $name = TestResources::MEDIA_SERVICES_INGEST_MANIFEST . $this->createSuffix();
        $ingestManifest->setName($name);
        $ingestManifest = $this->createIngestManifest($ingestManifest);
        $ingestAssetFileName = TestResources::MEDIA_SERVICES_DUMMY_FILE_NAME;

        $asset = new Asset(Asset::OPTIONS_NONE);
        $asset->setName(TestResources::MEDIA_SERVICES_ASSET_NAME . $this->createSuffix());
        $asset = $this->createAsset($asset);

        $ingestManifestAsset = new IngestManifestAsset($ingestManifest->getId());
        $ingestManifestAsset = $this->createIngestManifestAsset($ingestManifestAsset, $asset);

        $ingestManifestFile = new IngestManifestFile($ingestAssetFileName, $ingestManifest->getId(), $ingestManifestAsset->getId());
        $ingestManifestFile = $this->createIngestManifestFile($ingestManifestFile);

        // Test
        $result = $this->restProxy->getIngestManifestFile($ingestManifestFile);

        // Assert
        $this->assertEquals($ingestManifestFile->getParentIngestManifestId(), $result->getParentIngestManifestId());
        $this->assertEquals($ingestManifestFile->getParentIngestManifestAssetId(), $result->getParentIngestManifestAssetId());
        $this->assertEquals($ingestManifestFile->getName(), $result->getName());
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::getIngestManifestFileList
     */
    public function testGetIngestManifestFileList(){

        //  Setup
        $ingestManifest = new IngestManifest();
        $name = TestResources::MEDIA_SERVICES_INGEST_MANIFEST . $this->createSuffix();
        $ingestManifest->setName($name);
        $ingestManifest = $this->createIngestManifest($ingestManifest);
        $ingestAssetFileName = TestResources::MEDIA_SERVICES_DUMMY_FILE_NAME . $this->createSuffix();

        $asset = new Asset(Asset::OPTIONS_NONE);
        $asset->setName(TestResources::MEDIA_SERVICES_ASSET_NAME . $this->createSuffix());
        $asset = $this->createAsset($asset);

        $ingestManifestAsset = new IngestManifestAsset($ingestManifest->getId());
        $ingestManifestAsset = $this->createIngestManifestAsset($ingestManifestAsset, $asset);

        $ingestManifestFile = new IngestManifestFile($ingestAssetFileName, $ingestManifest->getId(), $ingestManifestAsset->getId());
        $ingestManifestFile = $this->createIngestManifestFile($ingestManifestFile);

        // Test
        $result = $this->restProxy->getIngestManifestFileList();

        // Assert
        $this->assertCount(1, $result);
        $this->assertEquals($ingestManifestFile->getParentIngestManifestId(), $result[0]->getParentIngestManifestId());
        $this->assertEquals($ingestManifestFile->getParentIngestManifestAssetId(), $result[0]->getParentIngestManifestAssetId());
        $this->assertEquals($ingestManifestFile->getName(), $result[0]->getName());
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::createContentKey
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::deleteContentKey
     */
    public function testCreateContentKey(){

        // Setup
        $aesKey = Utilities::generateCryptoKey(32);

        $protectionKeyId = $this->restProxy->getProtectionKeyId(ProtectionKeyTypes::X509_CERTIFICATE_THUMBPRINT);
        $protectionKey = $this->restProxy->getProtectionKey($protectionKeyId);

        $contentKey = new ContentKey();
        $contentKey->setContentKey($aesKey, $protectionKey);
        $contentKey->setProtectionKeyId($protectionKeyId);
        $contentKey->setProtectionKeyType(ProtectionKeyTypes::X509_CERTIFICATE_THUMBPRINT);
        $contentKey->setContentKeyType(ContentKeyTypes::STORAGE_ENCRYPTION);

        // Test
        $result = $this->createContentKey($contentKey);

        // Assert
        $this->assertEquals($contentKey->getId(), $result->getId());
        //current time and value of 'Created' field in $contentKey may differ on some seconds. That's why we check the difference in the boundary of hour
        $this->assertLessThan(3600, abs(time() - $result->getCreated()->getTimestamp()));
        $this->assertEquals($contentKey->getProtectionKeyId(), $result->getProtectionKeyId());
        $this->assertEquals($contentKey->getProtectionKeyType(), $result->getProtectionKeyType());
        $this->assertEquals($contentKey->getContentKeyType(), $result->getContentKeyType());
    }

     /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::getContentKeyList
     */
    public function testGetContentKeyList(){

        // Setup
        $aesKey = Utilities::generateCryptoKey(32);

        $protectionKeyId = $this->restProxy->getProtectionKeyId(ProtectionKeyTypes::X509_CERTIFICATE_THUMBPRINT);
        $protectionKey = $this->restProxy->getProtectionKey($protectionKeyId);

        $contentKey = new ContentKey();
        $contentKey->setContentKey($aesKey, $protectionKey);
        $contentKey->setProtectionKeyId($protectionKeyId);
        $contentKey->setProtectionKeyType(ProtectionKeyTypes::X509_CERTIFICATE_THUMBPRINT);
        $contentKey->setContentKeyType(ContentKeyTypes::STORAGE_ENCRYPTION);
        $contentKey = $this->createContentKey($contentKey);

        // Test
        $result = $this->restProxy->getContentKeyList();

        // Assert
        $this->assertCount(1, $result);
        $this->assertEquals($contentKey->getId(), $result[0]->getId());
        $this->assertEquals($contentKey->getProtectionKeyId(), $result[0]->getProtectionKeyId());
        $this->assertEquals($contentKey->getProtectionKeyType(), $result[0]->getProtectionKeyType());
        $this->assertEquals($contentKey->getContentKeyType(), $result[0]->getContentKeyType());
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::getContentKey
     */
    public function testGetContentKey(){

        // Setup
        $aesKey = Utilities::generateCryptoKey(32);

        $protectionKeyId = $this->restProxy->getProtectionKeyId(ProtectionKeyTypes::X509_CERTIFICATE_THUMBPRINT);
        $protectionKey = $this->restProxy->getProtectionKey($protectionKeyId);

        $contentKey = new ContentKey();
        $contentKey->setContentKey($aesKey, $protectionKey);
        $contentKey->setProtectionKeyId($protectionKeyId);
        $contentKey->setProtectionKeyType(ProtectionKeyTypes::X509_CERTIFICATE_THUMBPRINT);
        $contentKey->setContentKeyType(ContentKeyTypes::STORAGE_ENCRYPTION);
        $contentKey = $this->createContentKey($contentKey);

        // Test
        $result = $this->restProxy->getContentKey($contentKey);

        // Assert
        $this->assertEquals($contentKey->getId(), $result->getId());
        $this->assertEquals($contentKey->getProtectionKeyId(), $result->getProtectionKeyId());
        $this->assertEquals($contentKey->getProtectionKeyType(), $result->getProtectionKeyType());
        $this->assertEquals($contentKey->getContentKeyType(), $result->getContentKeyType());
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::rebindContentKey
     */
    public function testRebindContentKey(){

        // Setup
        $aesKey = Utilities::generateCryptoKey(32);

        $protectionKeyId = $this->restProxy->getProtectionKeyId(ContentKeyTypes::STORAGE_ENCRYPTION);
        $protectionKey = $this->restProxy->getProtectionKey($protectionKeyId);

        $contentKey = new ContentKey();
        $contentKey->setContentKey($aesKey, $protectionKey);
        $contentKey->setProtectionKeyId($protectionKeyId);
        $contentKey->setProtectionKeyType(ProtectionKeyTypes::X509_CERTIFICATE_THUMBPRINT);
        $contentKey->setContentKeyType(ContentKeyTypes::STORAGE_ENCRYPTION);
        $contentKey = $this->createContentKey($contentKey);

        // Test
        $result = $this->restProxy->rebindContentKey($contentKey, '');

        // Assert
        $this->assertEquals($result, $aesKey);
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::getProtectionKeyId
     */
    public function testGetProtectionKeyId(){

        // Setup
        $contentKeyType = ContentKeyTypes::STORAGE_ENCRYPTION;

        // Test
        $protectionKeyId = $this->restProxy->getProtectionKeyId($contentKeyType);

        // Assert
        $this->assertNotNull($protectionKeyId);
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::getProtectionKey
     */
    public function testGetProtectionKey(){

        // Setup
        $contentKeyType = ContentKeyTypes::STORAGE_ENCRYPTION;
        $protectionKeyId = $this->restProxy->getProtectionKeyId($contentKeyType);

        // Test
        $protectionKey = $this->restProxy->getProtectionKey($protectionKeyId);

        // Assert
        $this->assertNotNull($protectionKey);
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::getAssetContentKeys
     */
    public function testGetAssetContentKeys(){

        // Setup
        $aesKey = Utilities::generateCryptoKey(32);

        $protectionKeyId = $this->restProxy->getProtectionKeyId(ContentKeyTypes::COMMON_ENCRYPTION);
        $protectionKey = $this->restProxy->getProtectionKey($protectionKeyId);

        $contentKey = new ContentKey();
        $contentKey->setContentKey($aesKey, $protectionKey);
        $contentKey->setProtectionKeyId($protectionKeyId);
        $contentKey->setProtectionKeyType(ProtectionKeyTypes::X509_CERTIFICATE_THUMBPRINT);
        $contentKey->setContentKeyType(ContentKeyTypes::COMMON_ENCRYPTION);
        $contentKey = $this->createContentKey($contentKey);


        $asset = new Asset(Asset::OPTIONS_COMMON_ENCRYPTION_PROTECTED);
        $asset->setName(TestResources::MEDIA_SERVICES_ASSET_NAME . $this->createSuffix());
        $asset = $this->createAsset($asset);

        $this->restProxy->linkContentKeyToAsset($asset, $contentKey);

        // Test
        $result = $this->restProxy->getAssetContentKeys($asset);

        // Assert
        $this->assertCount(1, $result);
        $this->assertEquals($protectionKeyId, $result[0]->getProtectionKeyId());
        $this->assertEquals($contentKey->getId(), $result[0]->getId());
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::linkContentKeyToAsset
     */
    public function testLinkContentKeyToAsset(){

        // Setup
        $aesKey = Utilities::generateCryptoKey(16);

        $protectionKeyId = $this->restProxy->getProtectionKeyId(ContentKeyTypes::COMMON_ENCRYPTION);
        $protectionKey = $this->restProxy->getProtectionKey($protectionKeyId);

        $contentKey = new ContentKey();
        $contentKey->setContentKey($aesKey, $protectionKey);
        $contentKey->setProtectionKeyId($protectionKeyId);
        $contentKey->setProtectionKeyType(ProtectionKeyTypes::X509_CERTIFICATE_THUMBPRINT);
        $contentKey->setContentKeyType(ContentKeyTypes::COMMON_ENCRYPTION);
        $contentKey = $this->createContentKey($contentKey);


        $asset = new Asset(Asset::OPTIONS_COMMON_ENCRYPTION_PROTECTED);
        $asset->setName(TestResources::MEDIA_SERVICES_ASSET_NAME . $this->createSuffix());
        $asset = $this->createAsset($asset);

        // Test
        $this->restProxy->linkContentKeyToAsset($asset, $contentKey);

        // Assert
        $contentKeyFromAsset = $this->restProxy->getAssetContentKeys($asset);
        $this->assertEquals($contentKey->getId(), $contentKeyFromAsset[0]->getId());
        $this->assertEquals($contentKey->getProtectionKeyId(), $contentKeyFromAsset[0]->getProtectionKeyId());
        $this->assertEquals($contentKey->getContentKeyType(), $contentKeyFromAsset[0]->getContentKeyType());
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::removeContentKeyFromAsset
     */
    public function testRemoveContentKeyFromAsset(){

        // Setup
        $aesKey = Utilities::generateCryptoKey(32);

        $protectionKeyId = $this->restProxy->getProtectionKeyId(ContentKeyTypes::COMMON_ENCRYPTION);
        $protectionKey = $this->restProxy->getProtectionKey($protectionKeyId);

        $contentKey = new ContentKey();
        $contentKey->setContentKey($aesKey, $protectionKey);
        $contentKey->setProtectionKeyId($protectionKeyId);
        $contentKey->setProtectionKeyType(ProtectionKeyTypes::X509_CERTIFICATE_THUMBPRINT);
        $contentKey->setContentKeyType(ContentKeyTypes::COMMON_ENCRYPTION);
        $contentKey = $this->createContentKey($contentKey);


        $asset = new Asset(Asset::OPTIONS_COMMON_ENCRYPTION_PROTECTED);
        $asset->setName(TestResources::MEDIA_SERVICES_ASSET_NAME . $this->createSuffix());
        $asset = $this->createAsset($asset);

        $this->restProxy->linkContentKeyToAsset($asset, $contentKey);

        // Test
        $this->restProxy->removeContentKeyFromAsset($asset, $contentKey);

        // Assert
        $contentKeyFromAsset = $this->restProxy->getAssetContentKeys($asset);
        $this->assertEmpty($contentKeyFromAsset);
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::uploadAssetFile
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::_uploadAssetFileFromString
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::_uploadAssetFileSingle
     */
    public function testUploadSmallFileFromContent()
    {
        // Setup
        $fileContent = TestResources::MEDIA_SERVICES_DUMMY_FILE_CONTENT;

        // Test
        $actual = $this->uploadFile(TestResources::MEDIA_SERVICES_DUMMY_FILE_NAME, $fileContent);

        // Assert
        $this->assertEquals(TestResources::MEDIA_SERVICES_DUMMY_FILE_CONTENT, $actual);
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::uploadAssetFile
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::_uploadAssetFileFromString
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::_uploadBlock
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::_commitBlocks
     */
    public function testUploadLargeFileFromContent()
    {
        // Setup
        $fileContent = $this->createLargeFile();

        // Test
        $actual = $this->uploadFile(TestResources::MEDIA_SERVICES_DUMMY_FILE_NAME, $fileContent);

        // Assert
        $this->assertEquals($fileContent, $actual);
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::uploadAssetFile
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::_uploadAssetFileFromResource
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::_uploadAssetFileSingle
     */
    public function testUploadSmallFileFromResource()
    {
        // Setup
        $fileContent = TestResources::MEDIA_SERVICES_DUMMY_FILE_CONTENT;

        $resource = fopen(VirtualFileSystem::newFile($fileContent), 'r');

        // Test
        $actual = $this->uploadFile(TestResources::MEDIA_SERVICES_DUMMY_FILE_NAME, $resource);

        // Assert
        $this->assertEquals($fileContent, $actual);
    }

    /**
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::uploadAssetFile
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::_uploadAssetFileFromResource
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::_uploadBlock
     * @covers WindowsAzure\MediaServices\MediaServicesRestProxy::_commitBlocks
     */
    public function testUploadLargeFileFromResource()
    {
        // Setup
        $fileContent = $this->createLargeFile();

        $resource = fopen(VirtualFileSystem::newFile($fileContent), 'r');

        // Test
        $actual = $this->uploadFile(TestResources::MEDIA_SERVICES_DUMMY_FILE_NAME, $resource);

        // Assert
        $this->assertEquals($fileContent, $actual);
    }
}
