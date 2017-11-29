<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PHPack\JsPack;
use PHPUnit\Framework\TestCase;

class JsPackTest extends TestCase
{
    const RELATIVE_WEB_PATH = '/js/';
    const SUCCESS_DIST_PATH = '/success/dist/js/';
    const SUCCESS_SRC_PATH = '/success/js/';

    public function testDistNotDir()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Can not create a directory for building');
        new JsPack(__DIR__ . '/exception/js/', __DIR__ . '/exception/distErr');
    }

    public function testDirSrcNotFound()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The source directory was not found');
        new JsPack(__DIR__ . '/exception/jsNotFound/', __DIR__ . '/exception/dist/js/');
    }

    public function testBuildJsonNotFound()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Not found build.json');
        new JsPack(__DIR__ . '/exception/jsNotBuild/', __DIR__ . '/exception/dist/js/');
    }

    /**
     * @throws Exception
     */
    public function testCouldNotFindFileForBundle()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Could not find file for bundle (init)');
        $jp = new JsPack(__DIR__ . '/exception/js/', __DIR__ . '/exception/dist/js/');
        $jp->compileCrunched('admin');
    }

    /**
     * @throws Exception
     */
    public function testBuildingMain()
    {
        $js = 'console.log(\'main.js\');';
        $this->building('main', $js);
    }

    /**
     * @throws Exception
     */
    public function testBuildingCommon()
    {
        $js = 'console.log(\'init.js\');console.log(\'library.js\');console.log(\'timer.js\');';
        $this->building('common', $js);
    }

    /**
     * @throws Exception
     */
    public function testBuildingAdmin()
    {
        $js = 'console.log(\'init.js\');console.log(\'admin.js\');console.log(\'section.js\');';
        $this->building('admin', $js);
    }

    /**
     * @throws Exception
     */
    public function testBuildingAdminObfuscation()
    {
        $jp = $this->initSuccessBuilding();
        $contentJS = $jp->setObfuscation(true)->compileCrunched('admin', true);
        $this->assertEquals($contentJS, file_get_contents(__DIR__ . '/success/jsContentAdmin.js'));
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
     * @param $js
     * @throws Exception
     */
    private function building($name, $js)
    {
        $jp = $this->initSuccessBuilding();
        $jp->setRelativeWebPath(self::RELATIVE_WEB_PATH);
        $webPath = $jp->compileCrunched($name);
        $time = filemtime(__DIR__ . self::SUCCESS_DIST_PATH . 'bundle-' . $name . '.js');
        $this->assertEquals($webPath, '/js/bundle-' . $name . '.js?t=' . $time);
        $jsBuilding = file_get_contents(
            __DIR__ . self::SUCCESS_DIST_PATH . 'bundle-' . $name . '.js'
        );
        $this->assertEquals($jsBuilding, $js);
        $files = scandir(__DIR__ . self::SUCCESS_DIST_PATH);
        sort($files);
        $this->assertEquals($files, ['.', '..', 'bundle-' . $name . '.js']);
    }

    private function initSuccessBuilding()
    {
        $this->removeBundleFiles(__DIR__ . self::SUCCESS_DIST_PATH);
        return new JsPack(
            __DIR__ . self::SUCCESS_SRC_PATH,
            __DIR__ . self::SUCCESS_DIST_PATH
        );
    }

    private function removeBundleFiles($path)
    {
        if (!file_exists($path)) {
            return $this;
        }
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
