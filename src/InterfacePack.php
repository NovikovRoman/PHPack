<?php

namespace PHPack;

interface InterfacePack
{
    public function __construct($srcDir, $distDir);

    public function compileExpanded($name, $getContent = false);

    public function compileCompact($name, $getContent = false);

    public function compileCrunched($name, $getContent = false);
}