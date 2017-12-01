<?php

namespace PHPack;

use Leafo\ScssPhp\Compiler;

class CssPack extends AbstractPack implements InterfacePack
{
    const SOURCE_EXTENSION = 'scss';

    /**
     * CssPack constructor.
     *
     * @param string $srcDir absolute path to the sources scss
     * @param string $distDir absolute path to the build directory css
     * @throws \Exception
     */
    public function __construct($srcDir, $distDir)
    {
        parent::__construct($srcDir, $distDir, 'css');
    }

    /**
     * @param string $name the name of the package from build.json
     * @param bool   $getContent get file content
     * @return string webpath to bundle or content file
     * @throws \Exception
     */
    public function compileExpanded($name, $getContent = false)
    {
        return $this->compile($name, 'Leafo\ScssPhp\Formatter\Expanded', $getContent);
    }

    /**
     * @param string $name the name of the package from build.json
     * @param bool   $getContent get file content
     * @return string webpath to bundle
     * @throws \Exception
     */
    public function compileCompact($name, $getContent = false)
    {
        return $this->compile($name, 'Leafo\ScssPhp\Formatter\Compact', $getContent);
    }

    /**
     * @param string $name the name of the package from build.json
     * @param bool   $getContent get file content
     * @return string webpath to bundle
     * @throws \Exception
     */
    public function compileCrunched($name, $getContent = false)
    {
        return $this->compile($name, 'Leafo\ScssPhp\Formatter\Crunched', $getContent);
    }

    /**
     * @param string $name
     * @param string $class
     * @param bool   $getContent get file content
     * @return bool
     * @throws \Exception
     */
    private function compile($name, $class, $getContent = false)
    {
        $files = $this->getFilesInfo($name);
        $pathFileBundle = $this->distDir . self::PREFIX_BUILD . $name . '.' . $this->extension;
        if ($this->isProduction && !$this->force && $this->existsBundle($name)) {
            return $getContent ? file_get_contents($pathFileBundle) : $this->getWebPath($name);
        }
        $content = '';
        foreach ($files as $file) {
            $pathfile = $this->srcDir . $file . '.' . self::SOURCE_EXTENSION;
            if (!file_exists($pathfile)) {
                throw new \Exception('Could not find file for bundle (' . $file . ')');
            }
            $content .= file_get_contents($pathfile);
        }
        $scss = new Compiler();
        $scss->setFormatter($class);
        $scss->setImportPaths($this->srcDir);
        $content = $scss->compile($content);
        file_put_contents($pathFileBundle, $content);
        return $getContent ? $content : $this->getWebPath($name);
    }
}