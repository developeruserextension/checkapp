<?php
/**
 * Created by:  Milan Simek
 * Company:     Plugin Company
 *
 * LICENSE: http://plugin.company/docs/magento-extensions/magento-extension-license-agreement
 *
 * YOU WILL ALSO FIND A PDF COPY OF THE LICENSE IN THE DOWNLOADED ZIP FILE
 *
 * FOR QUESTIONS AND SUPPORT
 * PLEASE DON'T HESITATE TO CONTACT US AT:
 *
 * SUPPORT@PLUGIN.COMPANY
 */
namespace PluginCompany\LicenseManager\Model;

use Magento\Framework\DataObject;

class LicenseManager
{

    /**
     * @var \Magento\Framework\Module\Dir\Reader
     */
    protected $moduleReader;

    /** @var \Magento\Framework\Filesystem\Io\File */
    protected $ioAdapter;

    public function __construct
    (
        \Magento\Framework\Module\Dir\Reader $moduleReader,
        \Magento\Framework\Filesystem\Io\File $ioAdapter
    ) {
        $this->moduleReader = $moduleReader;
        $this->ioAdapter = $ioAdapter;
    }

    public function getProprietaryModuleLicenses()
    {
        $modules = $this->getProprietaryModules();
        $licenses = [];
        foreach($modules as $key => $name)
        {
            $licenses[$name] = $this->getLicenseDataForModuleKey($key);
        }
        return $licenses;
    }

    public function getLicenseDataForModuleKey($key)
    {
        $licenseData = new DataObject();

        $xml = simplexml_load_string($this->getLicenseXmlForModuleKey($key));
        if($xml){
            $licenseData->addData(
                [
                    'order_id' => (string)$xml->order_id,
                    'license_key' => (string)$xml->license_key,
                    'is_valid' => (string)$xml->is_valid,
                    'extension_key' => $key,
                    'is_development' => (string)$xml->is_development,
                    'license_error' => (string)$xml->license_error
                ]
            );
        }else{
            $licenseData->addData(
                [
                    'is_valid' => false,
                    'extension_key' => $key
                ]
            );
        }
        return $licenseData;
    }

    public function getLicenseXmlForModuleKey($key)
    {
        return $this->readLicenseXml($key);
    }

    public function getLicenseXmlPath($extensionKey)
    {
        return $this->getEtcDir($extensionKey) . DIRECTORY_SEPARATOR . 'license.xml';
    }

    private function getEtcDir($extensionKey)
    {
        return $this->moduleReader->getModuleDir('etc', $extensionKey);
    }

    public function writeLicenseXml($xml, $extensionKey)
    {
        $this->chmodEtcDir777($extensionKey);
        $this->ioAdapter->write($this->getLicenseXmlPath($extensionKey), $xml);
        return $this;
    }

    private function chmodEtcDir777($extensionKey)
    {
        $this->ioAdapter->chmod($this->getEtcDir($extensionKey), 0777, true);
        return $this;
    }

    public function deleteLicenseXml($extensionKey)
    {
        $this->ioAdapter->rm($this->getLicenseXmlPath($extensionKey));
        return $this;
    }

    public function readLicenseXml($extensionKey)
    {
        return $this->ioAdapter->read($this->getLicenseXmlPath($extensionKey));
    }

    public function getUnlicensedModules()
    {
        $modules = $this->getProprietaryModules();
        $unlicensed = [];
        foreach($modules as $key => $name){
            if(!$this->isLicenseValid($key))
                $unlicensed[$key] = $name;
        }
        return $unlicensed;
    }

    public function isLicenseValid($module)
    {
        $licenseFile = $this->readLicenseXml($module);
        if(!$licenseFile){
            return false;
        }
        $xml = simplexml_load_string($licenseFile);
        $string = (string)$xml->is_valid;
        return (bool)$string;
    }

    public function getProprietaryModules()
    {
        $modules = [];
        foreach($this->getPluginCompanyModulesXml() as $moduleXml)
        {
            $xml = simplexml_load_string($moduleXml);
            if($this->isModuleProprietaryXmlElement($xml)){
                $modules[$this->getModuleNameFromXmlElement($xml)] = $this->getReadableModuleNameFromXmlElement($xml);
            }
        }
        return $modules;
    }

    public function isModuleProprietaryXmlElement($xml)
    {
        if($this->getLicenseTypeFromXmlElement($xml) == 'proprietary'){
            return true;
        }
    }

    public function getLicenseTypeFromXmlElement($xml)
    {
        try{
            return (string)$xml->module[0]->pc_license[0]->type;
        }catch(\Exception $e)
        {
            return false;
        }
    }

    public function getModuleNameFromXmlElement($xml)
    {
        return (string)$xml->module[0]["name"];
    }

    public function getReadableModuleNameFromXmlElement($xml)
    {
        return (string)$xml->module[0]->pc_license[0]->name[0];
    }

    public function getPluginCompanyModulesXml()
    {
        $moduleXml = [];
        foreach($this->getAllModuleXmlFiles() as $xml){
            if(stristr($xml,'plugincompany')){
                $moduleXml[] = $xml;
            }
        }
        return $moduleXml;
    }

    public function getAllModuleXmlFiles()
    {
        return $this->moduleReader->getConfigurationFiles('module.xml')->toArray();
    }

}