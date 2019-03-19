<?php
namespace Sanket\Test\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\UrlInterface;
use Magento\MediaStorage\Helper\File\Storage\Database;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class Uploader
{
    const IMAGE_TMP_PATH    = 'sampleimageuploader/tmp/images/image';
    const IMAGE_PATH        = 'sampleimageuploader/images/image';
    const FILE_TMP_PATH     = 'sampleimageuploader/tmp/images/file';
    const FILE_PATH         = 'sampleimageuploader/images/file';
    protected $coreFileStorageDatabase;
    protected $mediaDirectory;
    private $uploaderFactory;
    protected $storeManager;
    protected $logger;
    protected $baseTmpPath;
    protected $basePath;
    protected $allowedExtensions;
    public function __construct(
        Database $coreFileStorageDatabase,
        Filesystem $filesystem,
        UploaderFactory $uploaderFactory,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger,
        $baseTmpPath,
        $basePath,
        $allowedExtensions = []
    ) {
        $this->coreFileStorageDatabase  = $coreFileStorageDatabase;
        $this->mediaDirectory           = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->uploaderFactory          = $uploaderFactory;
        $this->storeManager             = $storeManager;
        $this->logger                   = $logger;
        $this->baseTmpPath              = $baseTmpPath;
        $this->basePath                 = $basePath;
        $this->allowedExtensions        = $allowedExtensions;
    }
    public function setBaseTmpPath($baseTmpPath)
    {
        $this->baseTmpPath = $baseTmpPath;
    }
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
    }
    public function setAllowedExtensions($allowedExtensions)
    {
        $this->allowedExtensions = $allowedExtensions;
    }
    public function getBaseTmpPath()
    {
        return $this->baseTmpPath;
    }
    public function getBasePath()
    {
        return $this->basePath;
    }
    public function getAllowedExtensions()
    {
        return $this->allowedExtensions;
    }
    public function getFilePath($path, $name)
    {
        return rtrim($path, '/') . '/' . ltrim($name, '/');
    }
    public function moveFileFromTmp($name)
    {
        $baseTmpPath = $this->getBaseTmpPath();
        $basePath = $this->getBasePath();
        $baseFilePath = $this->getFilePath($basePath, $name);
        $baseTmpFilePath = $this->getFilePath($baseTmpPath, $name);

        try {
            $this->coreFileStorageDatabase->copyFile(
                $baseTmpFilePath,
                $baseFilePath
            );
            $this->mediaDirectory->renameFile(
                $baseTmpFilePath,
                $baseFilePath
            );
        } catch (\Exception $e) {
            throw new LocalizedException(
                __('Something went wrong while saving the file(s).')
            );
        }

        return $name;
    }

    public function getBaseUrl()
    {
        return $this->storeManager
            ->getStore()
            ->getBaseUrl(
                UrlInterface::URL_TYPE_MEDIA
            );
    }
    public function saveFileToTmpDir($fileId)
    {
        $baseTmpPath = $this->getBaseTmpPath();

        $uploader = $this->uploaderFactory->create(['fileId' => $fileId]);
        $uploader->setAllowedExtensions($this->getAllowedExtensions());
        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(true);

        $result = $uploader->save($this->mediaDirectory->getAbsolutePath($baseTmpPath));

        if (!$result) {
            throw new LocalizedException(
                __('File can not be saved to the destination folder.')
            );
        }
        $result['tmp_name'] = str_replace('\\', '/', $result['tmp_name']);
        $result['path'] = str_replace('\\', '/', $result['path']);
        $result['url'] =  $this->getBaseUrl() . $this->getFilePath($baseTmpPath, $result['file']);

        if (isset($result['file'])) {
            try {
                $relativePath = rtrim($baseTmpPath, '/') . '/' . ltrim($result['file'], '/');
                $this->coreFileStorageDatabase->saveFile($relativePath);
            } catch (\Exception $e) {
                $this->logger->critical($e);
                throw new LocalizedException(
                    __('Something went wrong while saving the file(s).')
                );
            }
        }

        return $result;
    }
    public function uploadFileAndGetName($input, $data)
    {
        if (!isset($data[$input])) {
            return '';
        }
        if (is_array($data[$input]) && !empty($data[$input]['delete'])) {
            return '';
        }

        if (isset($data[$input][0]['name']) && isset($data[$input][0]['tmp_name'])) {
            try {
                $result = $this->moveFileFromTmp($data[$input][0]['file']);
                return $result;
            } catch (\Exception $e) {
                return '';
            }
        } elseif (isset($data[$input][0]['name'])) {
            return $data[$input][0]['name'];
        }
        return '';
    }
}
