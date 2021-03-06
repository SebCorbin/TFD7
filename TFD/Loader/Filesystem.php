<?php

/**
 * Loads template from the filesystem.
 *
 * Part of the Drupal twig extension distribution
 * http://renebakx.nl/twig-for-drupal
 */
class TFD_Loader_Filesystem extends Twig_Loader_Filesystem
{
    protected $resolverCache;

    public function __construct()
    {
        parent::__construct(array());
        $this->resolverCache = array();
    }


    public function getSource($filename)
    {
        return file_get_contents($this->getCacheKey($filename));
    }


    public function findTemplate($name)
    {

        $this->validateName($name);
        if (!isset($this->resolverCache[$name])) {
            $found = false;
            if (is_readable($name)) {
                $this->resolverCache[$name] = $name;
                $found = true;
            } else {
                $paths = twig_get_discovered_templates();
                if (array_key_exists($name, $paths)) {
                    $completeName = $paths[$name];
                    $found = $this->isTemplateReadable($name, $completeName);
                }else {
                    global $theme;
                    if (stripos($name,$theme,0) == 0){
                        $name = str_replace($theme.'::','',$name);
                        $completeName = $paths[$name];
                        $found = $this->isTemplateReadable($name, $completeName);
                    }
                }
            }
            if (!$found) throw new Twig_Error_Loader(sprintf('Could not find a cache key for template "%s"', $name));
        }
        return $this->resolverCache[$name];
    }

    /**
     * @param $name
     * @param $completeName
     * @return bool
     */
    private function isTemplateReadable($name, $completeName) {
        $found = false;
        if (is_readable($completeName)) {
            $this->resolverCache[$name] = $completeName;
            $found = TRUE;
            return $found;
        }
        return $found;
    }

}

