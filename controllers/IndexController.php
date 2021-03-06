<?php

/*
 * @copyright Garrick S. Bodine, 2012
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Description of IndexController
 *
 * @author Garrick S. Bodine <garrick.bodine@gmail.com>
 */
class Entities_IndexController extends Omeka_Controller_AbstractActionController  {
    public function indexAction() {
        parent::indexAction();
    }
    
    public function favoriteAction() {
        if (current_user()) {
            $params = $this->_getAllParams();
            
            $user = current_user();
            $entity = new Entity();
            $e = $entity->getEntityByUserId($user->id);

            $er = new EntitiesRelations;
            $erp = new EntityRelationships;

            $er->entity_id = $e->id;
            $er->relation_id = $params['id'];
            $rel = $erp->getRelationshipByName('favorite');
            $er->relationship_id = $rel->id;
            $er->type = 'Item';
            $er->time = Zend_Date::now()->toString('Y-M-d H:m:s');
            $er->save();
        }
        $this->redirect('/items/show/'.$params['id']);
    }
    
    public function removeFavoriteAction() {
        $user = current_user();
        
        if ($user) {
            $e = new Entity();
            $entity = $e->getEntityFromUser($user);
            $p = $this->_getAllParams();
            $erp = new EntityRelationships();
            $rel = $erp->getRelationshipByName('favorite');
            $fav = $this->_helper->db->getTable('EntitiesRelations')->findBy($params=array('entity_id'=>$entity->id,'relation_id'=>$p['id'],'relationship_id'=>$rel->id,'type'=>'Item'),1,1);
            $fav[0]->delete();
        } 
        $this->redirect('/items/show/'.$p['id']);
    }
}

?>
