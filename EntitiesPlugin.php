<?php

/*
 * @copyright Garrick S. Bodine, 2012
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */



class EntitiesPlugin extends Omeka_Plugin_AbstractPlugin {
    
    protected $_hooks = array(
        'install',
        'initialize',
        'uninstall',
        'after_save_item',
        'before_save_user',
        'public_items_show',
        'users_form',
        'define_routes'
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
        get_view()->addHelperPath(dirname(__FILE__) . '/helpers', 'Entities_View_Helper_');
    }
    
    public function hookUninstall() {
        // todo
    }
    
    public function hookUsersForm($args) {
        $form = $args['form'];
        $entity = new Entity;
        $entity = $entity->getEntityFromUser($args['user']);
        $form->addElement('text','first_name',array(
            'label' => __('First Name'),
            'value'=> $entity->first_name,
            'required' => false
        ));
        
        $form->addElement('text','last_name',array(
            'label' => __('Last Name'),
            'value' => $entity->last_name,
            'required' => false
        ));
        
        
        $form->addElement('text','institution',array(
            'label' => __('Institution or Affiliation'),
            'value' => $entity->institution,
            'required' => false
        ));
        
        $form->addElement('text','institution',array(
            'label' => __('Institution or Affiliation'),
            'value' => $entity->institution,
            'required' => false
        ));
        
        $form->removeElement('name');
        $form->getElement('submit')->setOrder(12);
        
        $args['form'] = $form;
        $args['entity'] = $entity;
        return $args;
    }
    
    public function hookBeforeSaveUser($args) {
        $id = $args['record']['id'];
        $e = new Entity;
        $entity = $e->getEntityByUserId($id);
        if (!$entity) {
            $entity = new Entity;
            $entity->user_id = $args['record']['id'];
        }
        $entity->setPostData($_POST);
        $entity->save();
        $args['record']['name'] = $entity->getName();
        
        return $args;
    }
    
    public function hookAfterSaveItem($args) {
        $item = $args['record'];
        $item_id = $item->id;
        
        $user = current_user();
        
        $entity = new Entity();
        $e = $entity->getEntityByUserId($user->id);

        $er = new EntitiesRelations;
        $erp = new EntityRelationships;
        
        $er->entity_id = $e->id;
        $er->relation_id = $item_id;
        $rel = $erp->getRelationshipByName('modified');
        $er->relationship_id = $rel->id;
        $er->type = 'Item';
        $er->time = Zend_Date::now()->toString('Y-M-d H:m:s');
        $er->save();
        
    }
    
    public function hookDefineRoutes($args) {
        $router = $args['router'];
        $favRoute = new Zend_Controller_Router_Route('favorite/:id',
            array('controller' => 'index',
                    'action'     => 'favorite',
                    'module'     => 'entities',
                    'page'       => '1'));
        $router->addRoute('favorite', $favRoute);
        
        $unfavRoute = new Zend_Controller_Router_Route(
             'remove-favorite/:id',
             array('controller'=> 'index',
                 'action' => 'remove-favorite',
                 'module' => 'entities',
                 'page' => '1')
        );
        $router->addRoute('remove-favorite', $unfavRoute);
    }
    
    public function hookPublicItemsShow($args) {
        if (current_user()) {
            get_view()->favorites()->showItemFavoriteLinks($args['item']);
        }
    }
    
    
}


?>
