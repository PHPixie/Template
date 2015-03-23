<?php

namespace PHPixie\Template\Locators;

interface Locator{
    public function getTemplateFile($name);
}