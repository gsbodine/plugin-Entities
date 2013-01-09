<?php

/*
 * @copyright Garrick S. Bodine, 2012
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */



class EntitiesPlugin extends Omeka_Plugin_AbstractPlugin {
    
    protected $_hooks = array(
        'install',
        'initialize',
        'uninstall'
    );
    
    protected $_filters = array();
    
    protected $_options = array();
    
    public function setUp() {
        parent::setUp();
    }
    
    public function hookInstall() {
       $db = $this->_db;
       $sql = "
        CREATE TABLE IF NOT EXISTS `$db->Entity` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `first_name` text COLLATE utf8_unicode_ci,
            `middle_name` text COLLATE utf8_unicode_ci,
            `last_name` text COLLATE utf8_unicode_ci,
            `email` text COLLATE utf8_unicode_ci,
            `institution` text COLLATE utf8_unicode_ci,
            `user_id` int(10) unsigned,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        
        $sql2 = "
        CREATE TABLE IF NOT EXISTS `$db->EntitiesRelations` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `entity_id` int(10) unsigned DEFAULT NULL,
            `relation_id` int(10) unsigned DEFAULT NULL,
            `relationship_id` int(10) unsigned DEFAULT NULL,
            `type` enum('Item','Collection','Exhibit') COLLATE utf8_unicode_ci NOT NULL,
            `time` datetime DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `relation_type` (`type`),
            KEY `relation` (`relation_id`),
            KEY `relationship` (`relationship_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        
        $sql3 = "
        CREATE TABLE IF NOT EXISTS `$db->EntityRelationships` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `name` text COLLATE utf8_unicode_ci,
            `description` text COLLATE utf8_unicode_ci,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
       
        $db->query($sql);
        $db->query($sql2);
        $db->query($sql3);
    }
    
    public function hookInitialize() {
        $this->setUp();
    }
    
    public function hookUninstall() {
        // todo
    }
    
}


?>
