<?php

/*
 * @copyright Garrick S. Bodine, 2012
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */



class EntitiesPlugin extends Omeka_Plugin_AbstractPlugin {
    
    protected $_hooks = array(
        'install',
        'initialize'
    );
    
    protected $_filters = array();
    
    protected $_options = array();
    
    public function setUp() {
        parent::setUp();
    }
    
    public function hookInstall() {
        // todo: set up Entities tables, etc.
    }
    
    public function hookInitialize() {
        
    }
}


?>
