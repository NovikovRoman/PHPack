<?php

namespace PHPack;

abstract class AbstractPack
{
    const FILENAME_BUILD = 'build.json';
    const PREFIX_BUILD = 'bundle-';

    protected $infoBuild;
    protected $srcDir;
    protected $distDir;
    protected $relativeWebPath;
    protected $isProduction;
    protected $extension;
    protected $force;

    /**
     * AbstractPack constructor.
     *
     * @param $srcDir
     * @param $distDir
     * @param $extension
     * @throws \Exception
     */
    public function __construct($srcDir, $distDir, $extension)
    {
        $this->setSrcDir($srcDir)->setDistDir($distDir);
        $this->extension = $extension;
    }

    public function setProduction($isProduction)
    {
        $this->isProduction = $isProduction;
        return $this;
    }

    public function setRelativeWebPath($relativeWebPath)
    {
        $this->relativeWebPath = $relativeWebPath;
    }

    /**
     * @param bool $force force=true & isProduction=true - force recompile bundle
     * @return $this
     */
    public function setForce($force)
    {
        $this->force = $force;
        return $this;
    }

    /**
     * Create canonical path to sources
     *
     * @param $srcDir
     * @return $this
     * @throws \Exception
     */
    protected function setSrcDir($srcDir)
    {
        $this->srcDir = $this->setLastSlash($srcDir);
        if (!file_exists($this->srcDir) || is_file($this->srcDir)) {
            throw new \Exception('The source directory was not found');
        }
        $pathBuildJson = $this->srcDir . self::FILENAME_BUILD;
        if (!file_exists($pathBuildJson) || is_dir($pathBuildJson)) {
            throw new \Exception('Not found build.json');
        }
        $this->infoBuild = json_decode(file_get_contents($pathBuildJson), true);
        return $this;
    }

    /**
     * Create canonical path to distribution
     *
     * @param $distDir
     * @return $this
     * @throws \Exception
     */
    protected function setDistDir($distDir)
    {
        $this->distDir = $this->setLastSlash($distDir);
        $path = $this->removeLastSlash($this->distDir);
        if (file_exists($path) && is_dir($path)) {
            return $this;
        }
        if (file_exists($path) && !is_dir($path) || !mkdir($path, 0700, true)) {
            throw new \Exception('Can not create a directory for building');
        }
        return $this;
    }

    /**
     * @param string $name the name of the package from build.json
     * @return $this
     * @throws \Exception
     */
    protected function getFilesInfo($name)
    {
        if (empty($name)) {
            throw new \Exception('Empty name');
        }
        if (empty($this->infoBuild[ $name ])) {
            throw new \Exception($name . ' in build.json not found');
        }
        return $this->infoBuild[ $name ];
    }

    /**
     * check if the bundle is assembled
     *
     * @param string $name the name of the package from build.json
     * @return bool
     */
    protected function existsBundle($name)
    {
        $filePath = $this->distDir . self::PREFIX_BUILD . $name . '.' . $this->extension;
        return file_exists($filePath) && is_file($filePath);
    }

    /**
     * check if the bundle is assembled
     *
     * @param string $name the name of the package from build.json
     * @return bool
     */
    protected function getWebPath($name)
    {
        $filename = self::PREFIX_BUILD . $name . '.' . $this->extension;
        $time = filemtime($this->distDir . $filename);
        return $this->relativeWebPath . $filename . '?t=' . $time;
    }

    private function setLastSlash($path)
    {
        return preg_match('/\/$/', $path) ? $path : $path . '/';
    }

    private function removeLastSlash($path)
    {
        return preg_replace('/(\/)$/', '', $path);
    }
}