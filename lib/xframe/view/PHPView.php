<?php

namespace xframe\view;
use \xframe\registry\Registry;

/**
 * PHPView is the view for the pure PHP view scripts.
 */
class PHPView extends TemplateView {
    
    /**
     * @var boolean
     */
    private $debug;

    /**
     * @var string
     */
    private $layout;
    
    /**
     * @var string
     */
    private $root;

    /**
     * Set up the view
     *
     * @param Registry $registry
     * @param string $root
     * @param string $tmpDir
     * @param string $template
     * @param boolean $debug
     */
    public function  __construct(Registry $registry,
                                 $root,
                                 $tmpDir,
                                 $template,
                                 $debug = false) {
        parent::__construct(
            $root."view".DIRECTORY_SEPARATOR,
            ".phtml",
            $template
        );
        $this->root = $root;
        $layout = $registry->get("PHPVIEW_LAYOUT_PATH");
        if ($layout) {
            $this->setLayoutPath($layout);
        }
        $this->debug = $debug;
    }

    /**
     * Sets the layout for this view
     * @param string $layoutPath
     */
    public function setLayoutPath($layoutPath) {
		$path = $this->root."view".DIRECTORY_SEPARATOR.$layoutPath.".phtml";
		if (file_exists($path)) {
			$this->layout = $path;
		} else {
			$this->layout = false;
		}
    }

    /**
     * Generate some HTML
     * @return string
     */
    public function execute() {
        // capture output
        ob_start();
        // run view
        if ($this->layout) {
            require $this->layout;
        } else {
            $this->content();
        }
        // store result
        $result = ob_get_contents();
        // turn off the output buffer
        ob_end_clean();

        return $result;
    }

    protected function content() {
        require $this->template;
    }

    /**
     *
     * @param string $templateFile Path to template file, relative to the view dir
     * @param array $vars $name => $value of variabels to be accessible to the partial
     * @return type
     */
    private function partial($templateFile, $vars = array()) {
        foreach ($vars as $name => $value) {
            ${$name} = $value;
        }
        // capture output
        ob_start();
        // run view
        require $this->viewDirectory.DIRECTORY_SEPARATOR.$templateFile.$this->viewExtension;
        // store result
        $result = ob_get_contents();
        // turn off the output buffer
        ob_end_clean();

        return $result;
    }

}

