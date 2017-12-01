<?php

namespace PHPack;

use JShrink\Minifier;
use Tholu\Packer\Packer;

class JsPack extends AbstractPack implements InterfacePack
{
    private $obfuscation;

    /**
     * JsPack constructor.
     *
     * @param string $srcDir absolute path to the sources js
     * @param string $distDir absolute path to the build directory js
     * @throws \Exception
     */
    public function __construct($srcDir, $distDir)
    {
        parent::__construct($srcDir, $distDir, 'js');
        $this->obfuscation = false;
    }

    public function setObfuscation($obfuscation)
    {
        $this->obfuscation = $obfuscation;
        return $this;
    }

    /**
     * Method of creation for analogy with CssPack
     *
     * @param string $name the name of the package from build.json
     * @param bool   $getContent get file content
     * @return string webpath to bundle
     * @throws \Exception
     */
    public function compileExpanded($name, $getContent = false)
    {
        return $this->compile($name, ['flaggedComments' => false], $getContent);
    }

    /**
     * Method of creation for analogy with CssPack
     *
     * @param string $name the name of the package from build.json
     * @param bool   $getContent get file content
     * @return string webpath to bundle
     * @throws \Exception
     */
    public function compileCompact($name, $getContent = false)
    {
        return $this->compile($name, ['flaggedComments' => false], $getContent);
    }

    /**
     * Method of creation for analogy with CssPack
     *
     * @param string $name the name of the package from build.json
     * @param bool   $getContent get file content
     * @return string webpath to bundle
     * @throws \Exception
     */
    public function compileCrunched($name, $getContent = false)
    {
        return $this->compile($name, ['flaggedComments' => false], $getContent);
    }

    /**
     * @param string $name
     * @param array  $options
     * @param bool   $getContent get file content
     * @return bool
     * @throws \Exception
     */
    private function compile($name, $options, $getContent = false)
    {
        $files = $this->getFilesInfo($name);
        $pathFileBundle = $this->distDir . self::PREFIX_BUILD . $name . '.' . $this->extension;
        if ($this->isProduction && !$this->force && $this->existsBundle($name)) {
            return $getContent ? file_get_contents($pathFileBundle) : $this->getWebPath($name);
        }
        $content = '';
        foreach ($files as $file) {
            $pathfile = $this->srcDir . $file . '.' . $this->extension;
            if (!file_exists($pathfile)) {
                throw new \Exception('Could not find file for bundle (' . $file . ')');
            }
            $content .= file_get_contents($pathfile);
        }
        $content = Minifier::minify($content, $options);
        if (!empty($this->obfuscation)) {
            $packer = new Packer($content, 'Normal');
            $content = $packer->pack();
        }
        file_put_contents($pathFileBundle, $content);
        return $getContent ? $content : $this->getWebPath($name);
    }
}