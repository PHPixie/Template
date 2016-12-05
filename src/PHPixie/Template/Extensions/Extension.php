<?php

namespace PHPixie\Template\Extensions;

/**
 * Extension interface
 */
interface Extension
{
    /**
     * Extension name
     * @return string
     */
    public function name();

    /**
     * Map of methods that should be available in templates.
     * @return array
     */
    public function methods();

    /**
     * Map of method aliases
     * @return array
     */
    public function aliases();
}