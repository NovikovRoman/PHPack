<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PHPack\CssPack;
use PHPUnit\Framework\TestCase;

class CssPackTest extends TestCase
{
    const RELATIVE_WEB_PATH = '/css/';
    const SUCCESS_DIST_PATH = '/success/dist/css/';
    const SUCCESS_SRC_PATH = '/success/scss/';

    public function testDistNotDir()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Can not create a directory for building');
        new CssPack(__DIR__ . '/exception/scss/', __DIR__ . '/exception/distErr');
    }

    public function testDirSrcNotFound()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The source directory was not found');
        new CssPack(__DIR__ . '/exception/scssNotFound/', __DIR__ . '/exception/dist/css/');
    }

    public function testBuildJsonNotFound()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Not found build.json');
        new CssPack(__DIR__ . '/exception/scssNotBuild/', __DIR__ . '/exception/dist/css/');
    }

    /**
     * @throws Exception
     */
    public function testCouldNotFindFileForBundle()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Could not find file for bundle (init)');
        $cp = new CssPack(__DIR__ . '/exception/scss/', __DIR__ . '/exception/dist/scss/');
        $cp->compileCrunched('admin');
    }

    /**
     * @throws Exception
     */
    public function testBuildingEmptyName()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Empty name');
        $css = '';
        $this->building('', $css);
    }

    /**
     * @throws Exception
     */
    public function testBuildingPersonal()
    {
        $css = '.class3{width:1px}.class5{width:1px}.class2{width:1px}.class4{width:1px}';
        $this->building('personal', $css);
    }

    /**
     * @throws Exception
     */
    public function testBuildingCommon()
    {
        $css = '.class3{width:1px}.class5{width:1px}.class2{width:1px}.class1{width:1px}';
        $this->building('common', $css);
    }

    /**
     * @throws Exception
     */
    public function testBuildingAdmin()
    {
        $css = '.class4{width:1px}.class5{width:1px}.class1{width:1px}';
        $this->building('admin', $css);
    }

    /**
     * @throws Exception
     */
    public function testBuildingSomething()
    {
        $this->expectException(\Exception::class);
        $name = 'something';
        $this->expectExceptionMessage($name . ' in build.json not found');
        $this->building($name, '');
    }

    /**
     * @param $name
     * @param $css
     * @throws Exception
     */
    private function building($name, $css)
    {
        $cp = $this->initSuccessBuilding();
        $cp->setRelativeWebPath(self::RELATIVE_WEB_PATH);
        $webPath = $cp->compileCrunched($name);
        $time = filemtime(__DIR__ . self::SUCCESS_DIST_PATH . 'bundle-' . $name . '.css');
        $this->assertEquals($webPath, '/css/bundle-' . $name . '.css?t=' . $time);
        $cssBuilding = file_get_contents(__DIR__ . self::SUCCESS_DIST_PATH . 'bundle-' . $name . '.css');
        $this->assertEquals($cp->compileCrunched($name, true), $cssBuilding);
        $this->assertEquals($cssBuilding, $css);
        $files = scandir(__DIR__ . self::SUCCESS_DIST_PATH);
        sort($files);
        $this->assertEquals($files, ['.', '..', 'bundle-' . $name . '.css']);
    }

    private function initSuccessBuilding()
    {
        $this->removeBundleFiles(__DIR__ . self::SUCCESS_DIST_PATH);
        return new CssPack(__DIR__ . self::SUCCESS_SRC_PATH, __DIR__ . self::SUCCESS_DIST_PATH);
    }

    private function removeBundleFiles($path)
    {
        $files = scandir($path);
        foreach ($files as $file) {
            if ($file == '.' || $file == '..' || !preg_match('/^bundle\-/', $file)) {
                continue;
            }
            unlink($path . $file);
        }
        return $this;
    }
}
