<?php
    /**
     * ./src/PhingShopware/Task/ActivatePlugin.php
     * @author blange <code@wbl-konzept.de>
     * @package phingShopware
     * @version $id$
     */

    namespace PhingShopware\Task;

    require_once __DIR__ . DIRECTORY_SEPARATOR . 'Base.php';

    /**
     * Activates a plugin.
     * @author blange <code@wbl-konzept.de>
     * @package phingShopware
     * @version $id$
     */
    class ControlPlugin extends Base
    {
        /**
         * Should the module be activated?
         * @var void|bool
         */
        protected $activate = null;

        /**
         * Ignores errors?
         * @var bool
         */
        protected $ignore = false;

        /**
         * Install the module first?
         * @var bool
         */
        protected $install = null;

        /**
         * Array of plugin properties.
         * @var \PropertyTask[]
         */
        protected $properties = array();

        /**
         * The plugin id.
         * @var string
         */
        protected $plugin = '';

        /**
         * Taskname for logger
         * @var string
         */
        protected $taskName = 'SWPlugin';

        /**
         * Creates a child property.
         * @return \PropertyTask
         */
        public function createProperty() {
            $this->properties[] = $return = new \PropertyTask();

            return $return;
        } // function

        /**
         * Returns the plugin in.
         * @return string
         */
        public function getPlugin()
        {
            return $this->plugin;
        } // function

        /**
         * Returns the child properties.
         * @return \PropertyTask[]
         */
        public function getProperties()
        {
            return $this->properties;
        } // function

        /**
         * Should the module be activated?
         * @return boolean|void
         */
        public function isActivate()
        {
            return $this->activate;
        } // function

        /**
         * Should the module ignore plugin errors?
         * @return boolean
         */
        public function isIgnore()
        {
            return $this->ignore;
        }

        /**
         * Install the module first?
         * @return boolean|void
         */
        public function isInstall()
        {
            return $this->install;
        } // function

        /**
         * Controlling the plugin.
         * @throws \BuildException
         */
        public function main()
        {
            if (!$plugin = $this->getPlugin()) {
                throw new \BuildException("plugin attribute is required", $this->location);
            } // if

            if ($this->isInstall()) {
                exec(
                    'php ' . SW_PATH . '/bin/console sw:plugin:install ' . escapeshellcmd($plugin),
                    $output,
                    $return
                );

                if ($return && !$this->isIgnore()) {
                    throw new \BuildException(
                        sprintf('Problem white activating/deactivating the plugin "%s": %s', $plugin, implode("n", $output))
                    );
                } // if
            } // if

            if (!is_null($activate = $this->isActivate())) {
                exec(
                    'php ' . SW_PATH . '/bin/console sw:plugin:' . ($activate ? 'activate' : 'deactivate') .
                        ' ' . escapeshellcmd($plugin),
                    $output,
                    $return
                );

                if ($return && !$this->isIgnore()) {
                    throw new \BuildException(
                        sprintf('Problem white activating/deactivating the plugin "%s": %s', $plugin, implode("n", $output))
                    );
                } // if
            } // if

            if ($props = $this->getProperties()) {
                /** @var \PropertyTask $property */
                foreach ($this->properties as $property) {
                    $name  = $property->getName();
                    $value = $property->getValue();

                    exec(
                        $cmd = sprintf(
                            'php %s/bin/console sw:plugin:config:set %s %s %s',
                            SW_PATH,
                            escapeshellcmd($plugin),
                            escapeshellcmd($name),
                            escapeshellcmd($value)
                        ),
                        $output,
                        $return
                    );

                    if ($return && !$this->isIgnore()) {
                        throw new \BuildException(
                            sprintf('Problem white setting the plugin config %s::%s: %s', $plugin, $name, implode("n", $output))
                        );
                    } // if
                } // foreach
            } // if
        } // function

        /**
         * Should the module be activated?
         * @param boolean $activate
         */
        public function setActivate($activate)
        {
            $this->activate = $activate;
        } // function

        /**
         * Ignores activation errors?
         * @param boolean $ignore
         */
        public function setIgnore($ignore)
        {
            $this->ignore = $ignore;
        } // function

        /**
         * Install the module first?
         * @param boolean $install
         */
        public function setInstall($install)
        {
            $this->install = $install;
        } // function

        /**
         * Sets the plugin id.
         * @param string $plugin
         */
        public function setPlugin($plugin)
        {
            $this->plugin = $plugin;
        } // function
    } // class