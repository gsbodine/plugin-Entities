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
            $html .= '<p><span class="alert alert-danger"><i class="icon-heart"></i> This item is one of your favorites.</span>';
            $num = $this->_numberOfFavorited($item);
            
            $html .= '<div><br /><a href="/remove-favorite/'.$item->id.'" class="btn btn-small btn-danger"><i class="icon-remove-sign"></i> Un-favorite this item</a>';
            if ($num > 1) {
                $html .= '<span class="pull-right text-error"><small><i class="icon-heart-empty"></i> ' . $num . ' people have now favorited this item.</small></span></p>';
            }
            $html .= '</div>';
            
        } else {
            $html .= $this->_show_item_favorite_link($item);
        }
        echo $html;
    }
    
    public function listMostFavoritedItems($db) {
        $html = '<ul class="unstyled">';
        $items = $this->_getMostFavoritedItems($db,10);
        foreach ($items as $fave) {
            $html .= '<li><i class="icon-star"></i> '. metadata($fave['item'], array('Dublin Core','Title')) .' (favorited '. $fave['faves'] .' times)</li>';
        }
        $html .= '</ul>';
        
        return $html;
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
    
    private function _numberOfFavorited($item) {
        $er = new EntityRelationships();
        $e = $er->getRelationshipByName('Favorite');
        
        $params = array('relationship_id'=>$e->id,'relation_id'=>$item->id);
        $num = get_db()->getTable('EntitiesRelations')->findBy($params);
        return count($num);
    }
    
    private function _getMostFavoritedItems($db,$limit=null) {
        $select = new Omeka_Db_Select($db);
        $select->from(array('er'=>'entities_relations'), array('i.id','count(i.id) as faves'))
                ->join(array('e'=>'entities'), "er.entity_id = e.id",array())
                ->join(array('ers'=>'entity_relationships'), "ers.id = er.relationship_id", array())
                ->join(array('i'=>'items'), "i.id = er.relation_id")
            ->where("ers.name='favorite'")
            ->group('i.id')
            ->order('count(i.id) DESC')
            ->limit($limit);;
        $stmt = $select->query();
        while ($row = $stmt->fetch()) {
            $item = get_db()->getTable('Item')->find($row['id']);
            $items[] = array('item'=>$item,'faves'=>$row['faves']);
         }
        return $items;
    }
    
}

?>
