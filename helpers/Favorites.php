<?php

/*
 * @copyright Garrick S. Bodine, 2012
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Description of Favorites
 *
 * @author Garrick S. Bodine <garrick.bodine@gmail.com>
 */
class Entities_View_Helper_Favorites extends Zend_View_Helper_Abstract {
    public function favorites() {
        return $this;
    }
    
    public function showItemFavoriteLinks($item) {
        $html ='';
        $html = '<hr /><h4><i class="icon-heart"></i> Favorite</h4>';
        
        if ($this->_is_user_favorite($item)){
            $html .= '<p><strong>This item is one of your favorites.</strong> <a href="/remove-favorite/"'.$item->id.'" class="label label-important pull-right"><i class="icon-remove-sign"></i> Un-favorite this item</a>';
        } else {
            $html .= $this->_show_item_favorite_link($item);
        }
        echo $html;
    }
    
    /* PRIVATE FUNCTIONS */
    
    private function _is_user_favorite($item) {
        $user = current_user();
        if ($user) {
            $db = get_db();
            $e = new Entity();
            $entity = $e->getEntityFromUser($user);
            if ($entity->id !== null) {
                $erel = new EntityRelationships();
                $rel = $erel->getRelationshipByName('favorite');

                $params = array('relation_id'=>$item->id,'entity_id'=>$entity->id,'relationship_id'=>$rel->id);
                $er = $db->getTable('EntitiesRelations')->findBy($params);
                return $er;
            } 
        }
    }
    
    private function _show_item_favorite_link($item) {
        $html = '<a href="/favorite/'. $item->id . '" class="btn btn-danger"><i class="icon-heart"></i> Make this item one of my favorites</a>';
        return $html;
    }
    
}

?>
