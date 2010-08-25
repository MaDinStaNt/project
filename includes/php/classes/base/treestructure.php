<?php

class CTreeNodeStructure{
        var $id;
        var $title;
        var $type;
        var $class;
        var $var;
        var $value;
        var $tag;

        // childNodes
        var $childNodes =array();

        /**
         * Constructor
         *
         * @param string $title
         * @param string $image
         * @param integer $id
         * @param string $aimage
         * @param integer $expand
         * @return CTreeNodeStructure
         */
        function CTreeNodeStructure($id =NULL, $title, $type, $class, $var, $value, $tag){
                $this->id =$id;
                $this->title =$title;
                $this->type =$type;
                $this->class =$class;
                $this->var =$var;
                $this->value =$value;
                $this->tag =$tag;
        }

        /**
         * Add Node to Tree
         *
         * @param string $title
         * @param string $image
         * @param integer $id
         * @param string $aimage
         * @param integer $expand
         * @return CTreeNodeStructure
         */
                //return $this->tree->addNode($id, $title, $image, $expand);
        function &addNode($id =NULL, $title, $type, $class, $var, $value, $tag){
                $obj =& $this->findParent($this, 'parent');
                $id =(integer) $id;

                if(!$id){
                        $obj->last ++;
                        $id =(integer) ($obj->last);
                }
                $this->childNodes[$id] =new CTreeNodeStructure($id, $title, $type, $class, $var, $value, $tag);
                $this->childNodes[$id]->parent =& $this;

                return $this->childNodes[$id];
        }

        /**
         * Top/Bottom level variable
         *
         * @param CTreeNodeStructure $obj
         * @param string $var
         * @return various
         */
        function &findParent(& $obj, $var){
                if(isset($obj->$var)){
                    return $this->findParent($obj->$var, $var);
                }else{
                    return $obj;
                }
        }

        /**
         * Remove node from tree
         *
         * @param integer $id
         * @return 1/NULL
         */
        function removeNode($id){
                $obj =& $this->findParent($this, 'parent');
                if(isset($obj->ref[$id])){
                        $obj->ref[$id] =NULL;
                        return 1;
                }
                return NULL;
        }

        /**
         * Return CTreeNodeStructure from global tree
         *
         * @param integer $id
         * @return various
         */
        function &getElementById($id){
                $obj =& $this->findParent($this, 'parent');
                if(isset($obj->ref[$id])){
                        return $obj->ref[$id];
                }
                return NULL;
        }
}


class CTreeStructure{
        var $tree;
        /**
         * Constructor
         *
         * @param integer $id
         * @return CTreeView
         */
        function CTreeStructure($id=NULL){
                $this->tree = new CTreeNodeStructure(0, 'root', null, null, null, null, null);
        }

        /**
         * add Node to the tree
         *
         * @param integer $id
         * @param string $title
         * @param string $image
         * @param integer $expand
         * @return bool
         */
        function &addNode($id =NULL, $title, $type, $class, $var, $value, $tag){
                return $this->tree->addNode($id, $title, $type, $class, $var, $value, $tag);
        }

        /**
         * remove Node from tree
         *
         * @param integer $id
         * @return bool
         */
        function removeNode($id =NULL){
                return $this->tree->removeNode($id);
        }

        /**
         * get element by id in global tree
         *
         * @param integer $id
         * @return CTreeNodeStructure
         */
        function &getElementById($id){
                return $this->tree->getElementById($id);
        }
}




?>
