<?php

namespace PhpOffice\PhpSpreadsheet\Writer;

use ZipStream\Option\Archive;
use ZipStream\ZipStream;

class ZipStream0
{
    /**
     * @param resource $fileHandle
     */
    public static function newZipStream($fileHandle)
    {
        // Prefer ZipStream if installed
        if (class_exists(Archive::class) && class_exists(ZipStream::class)) {
            return ZipStream2::newZipStream($fileHandle);
        }

        if (class_exists(ZipStream::class)) {
            return ZipStream3::newZipStream($fileHandle);
        }

        // Fallback to built-in ZipArchive (no external package required)
        if (class_exists('ZipArchive')) {
            return new ZipArchiveStream($fileHandle);
        }

        throw new \RuntimeException('No suitable Zip handler available. Install maennchen/zipstream-php or enable ZipArchive.');
    }
}

class ZipArchiveStream
{
    private $fileHandle;
    private $tmpFilename;
    private $zip;

    public function __construct($fileHandle)
    {
        $this->fileHandle = $fileHandle;
        $this->tmpFilename = tempnam(sys_get_temp_dir(), 'phpspreadsheet_');

        $this->zip = new \ZipArchive();
        if ($this->zip->open($this->tmpFilename, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Cannot create temporary zip file.');
        }
    }

    public function addFile(string $path, string $content): void
    {
        $this->zip->addFromString($path, $content);
    }

    public function finish(): void
    {
        $this->zip->close();

        $tmp = fopen($this->tmpFilename, 'rb');
        if ($tmp === false) {
            throw new \RuntimeException('Cannot open temporary zip file for reading.');
        }

        while (!feof($tmp)) {
            $chunk = fread($tmp, 8192);
            if ($chunk === false) {
                break;
            }
            fwrite($this->fileHandle, $chunk);
        }

        fclose($tmp);
        @unlink($this->tmpFilename);
    }
}
