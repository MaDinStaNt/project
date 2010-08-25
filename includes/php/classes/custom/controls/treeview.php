<?php

class CTreeNode{
        // vars for TreeNode
        var $id;
        var $text;
        var $image;
        var $aimage;
        var $expand;
        var $depth;

        // last index of tree element
        var $last =0;

        // ref to ALL IDs
        var $ref =Array();

        // images sets to levels
        var $images =Array();

        // childNodes
        var $childNodes =array();

        /**
         * Constructor
         *
         * @param string $text
         * @param string $image
         * @param integer $id
         * @param string $aimage
         * @param integer $expand
         * @return CTreeNode
         */
        function CTreeNode($text, $image ='', $id =NULL, $aimage =NULL, $expand =NULL){
                $this->id =$id;
                $this->text =$text;
                $this->image =$image;
                $this->aimage =($aimage)? 1: 0;
                $this->expand =$expand;
        }

        /**
         * Add Node to Tree
         *
         * @param string $text
         * @param string $image
         * @param integer $id
         * @param string $aimage
         * @param integer $expand
         * @return CTreeNode
         */
                //return $this->tree->addNode($id, $text, $image, $expand);
        function &addNode($text, $image ='', $id =NULL, $aimage =NULL, $expand =''){
                $obj =& $this->findParent($this, 'parent');
                $id =(integer) $id;

                if(!$id){
                        $obj->last ++;
                        $id =(integer) ($obj->last);
                }
                $this->childNodes[$id] = new CTreeNode($text, $image, $id, $aimage, $expand);
                $this->childNodes[$id]->parent =& $this;
                $obj->ref[$id]= &$this->childNodes[$id];

                return $this->childNodes[$id];
        }

        /**
         * Top/Bottom level variable
         *
         * @param CTreeNode $obj
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
         * Return CTreeNode from global tree
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


class CTreeView extends CTemplateControl{
        var $tv;
        var $Application;
        var $tree;

        // simple
        var $simple =0;

        // excol function
        var $excol ='excol(this,%id%,%aimage%);';

        // action
        var $action ='javascript:%20void(0);';

        // mouse action
        var $mouse_action ='chsel(this,%id%,%level%);';

        // id format
        var $id_format ="node_%id%_%parent%";


        var $parent = '0';
        /**
         * Constructor
         *
         * @param integer $id
         * @return CTreeView
         */
        function CTreeView(&$html_page, $id=NULL){
                parent::CTemplateControl('TreeView');

                $this->Application = &$GLOBALS['app'];
                $this->tv = &$this->Application->template_vars;
                $this->tv['id'] = $id;
                $this->tree = new CTreeNode('', 'pixel-line.gif');
        }

        /**
         * get params of tree from query string
         *
         * @param integer $node
         * @return integer
         */
        function get_item($node){
                static $p        =NULL;
                if(! is_array($p)){
                        $qs =InGetPost('tree');
                        $qs =preg_replace('/[^\d:]+/', ',', $qs);
                }
                if(! $qs and !$p){
                        $p =array();
                        return NULL;
                }

                if(! is_array($p)){
                        $p =array();
                        $qs =explode(',', $qs);

                        // expand
                        foreach($qs as $value){
                                list($k, $v) =explode(':', $value);
                                $p[$k] =$v;
                        }

                        // collapse
                        $ret ="";
                        foreach($p as $id =>$val)
                                if($id)
                                        $ret .= (($ret)? ',': ''). $id. ':Array('. ((integer)($val)%2). ',0)';

                        $this->tv['tree'] =$ret;
                }
                return (isset($p[$node->id]))? $p[$node->id]: 0;
        }

        /**
         * add Node to the tree
         *
         * @param integer $id
         * @param string $text
         * @param string $image
         * @param integer $expand
         * @return bool
         */
        function &addNode($id =NULL, $text, $image =NULL, $expand =NULL){
                return $this->tree->addNode($id, $text, $image, $expand);
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
         * @return CTreeNode
         */
        function &getElementById($id){
                return $this->tree->getElementById($id);
        }

        /**
         * Setup image for all images in same level (depth), or by id
         *
         * @param string $image
         * @param integer $depth
         * @param integer $or_id
         * @param string $aimage
         * @return string (src of image or NULL)
         */
        function set_enable_image($image =NULL, $depth =NULL, $or_id =NULL, $aimage =NULL){
            if(is_null($depth) and is_null($or_id)){
                return;
            }
            if(! is_null($image)){
                if(! is_null($depth)){
                    $this->images['depth_'. ((integer)($depth))] =array($image, $aimage);
                }else if(! is_null($or_id)){
                    $this->images['id_'. ((integer)($id))] =array($image, $aimage);
                }
            }else{
                if(! is_null($depth)){
                    $depth =(integer)($depth);
                    return (isset($this->images['depth_'. $depth]))? $this->images['depth_'. $depth]: NULL;
                }else if(! is_null($id)){
                    $id =(integer)($id);
                    return (isset($this->images['id_'. $id]))? $this->images['id_'. $id]: NULL;
                }
            }
            return NULL;
        }

        /**
         * Get HTML view for each node
         *
         * @param CTreeNode $node
         * @param integer $depth
         * @param integer $position
         * @param integer $count
         * @param integer $parent
         * @return string
         */
        function _get_html_node($node, $depth =NULL, $position =0, $count =0, $parent =0){
                global $ImagesPath;
                $is_first = ($position ==0);
                $is_last = ($position ==$count);
                $is_one = ($is_first and $is_last);
                $is_childs = ($node->childNodes)? TRUE: FALSE;

                $base = ($depth == 0)? 'v': 'c';
                $class = ($is_last && $is_first && !$depth)? '': $base.(($is_last)? '-last': (($is_first)? '-first': ''));

                if(! $node->image){
                    if(($tmp = $this->set_enable_image(NULL, NULL, $node->id)) or ($tmp =$this->set_enable_image(NULL, $depth))){
                        $node->image = $tmp[0];
                        if($tmp[1]){
                            $node->aimage = $tmp[1];
                        }
                    }
                }

                // opening from query_string (tree=...)
                //echo $node->image;
                if( ($is_set = $this->get_item($node))){
                    $node->expand = $is_set;
                    if($node->image and $node->aimage){
                        $node->image = preg_replace("/(_active)?(?=\.\w+(\?[;&=%\w]+)?)/", "_active", $node->image);
                    }
                }

                if(! $this->tv['simple'] or $is_childs){
                    $out .= sprintf('<tr><td class="%s"><div><img src="%s" onclick="%s" alt="" border="0" class="hand" /></div></td>',
                        $class,
                        $ImagesPath. 'tree/'. (($is_childs)? (($node->expand ==1 or $node->expand ==3)? 'collapse': 'expand'): 'blank'). '.gif',
                        ($is_childs)? $this->_unescape($this->tv['onclick'], $node, $depth, $parent): ''
                    );
                }else{
                    $out .='<tr><td><div class="space">&nbsp;</div></td>';
                }
                $out .='<td class="'. $base. '-h"><div>&nbsp;</div></td>'.
                        '<td valign="top"><table cellpadding="0" cellspacing="0" border="0"><tbody><tr><td>';

                // image or DIV (on simple view)
                if(! $this->tv['simple'] or $node->image){
                	/*
                        $out .=sprintf('<div><img src="%s" alt="" border="0" class="ico" /></div>',
                                ($node->image)?
                                $this->_unescape($ImagesPath. 'tree/'. $node->image, $node, $depth, $parent):
                                (($node->expand %2==1 and $is_childs)?
                                        $this->_unescape($ImagesPath. 'tree/pixel-cross.gif', $node, $depth, $parent):
                                        $this->_unescape($ImagesPath. 'tree/pixel-line.gif', $node, $depth, $parent)
                                )
                        );
                        */
                }else{
                        $out .= '<div class="space">&nbsp;</div>';
                }

                $out .='</td>';

                // space
                $out .='<td class="space"><div>&nbsp;</div></td>';
                // caption
//                echo(count($node->childNodes));
                $out .= sprintf(
                    $this->_unescape('<td class="txt"><a href="%s" onclick="%s" '. (((!$node->expand or $node->expand <2) && $node->text != InGetPost('path_id')) ?' class="treeView"': ' class="treeViewSel"'). ' id="'. ($this->id_format). '">%s</a>', $node, $depth, $parent),
                    (((!$node->expand or $node->expand <2) && $node->id !=InGetPost('path_id'))?
                        $this->_unescape($this->tv['action'], $node, $depth, $parent):
                        '#'),
                    (((!$node->expand or $node->expand <2) && $node->id !=InGetPost('path_id')) ?$this->_unescape($this->tv['mouse_action'], $node, $depth, $parent): 'void(0)'),
                    $node->text);

                $out .=(((!$node->expand or $node->expand <2) && $node->id !=InGetPost('path_id'))?
                    '':
                    '<script language="javascript" type="text/javascript">'. "\n".
                    '<!-- <![CDATA['. "\n".
                    $this->_unescape('expsel="'. ($this->id_format). '";', $node, $depth, $parent)."\n".
                    '// ]]> -->'."\n".
                    '</script>'). '</td>';

                $out .='</tr></tbody></table></td></tr>';

                if($node->childNodes){
                    $out .='<tr'. (($node->expand %2 ==1)? '': ' style="display: none;"'). '>';
                    $out .='<td'. (($is_last)? '': ' class="v"'). '><div class="space"></div></td>';
                    $out .='<td></td><td colspan="2">';

                    // for all childs
                    $out .=$this->_get_html($node, $depth+1, 1, $node->id);

                    $out .='</td></tr>';
                }


                return $out;
        }

        /**
         * Get html view of the tree
         *
         * @param CTreeView $obj
         * @param integer $depth
         * @param integer $new_table
         * @return string
         */
        function _get_html($obj, $depth =0, $new_table =0, $parent =0){
            global $DebugLevel;
            $DebugLevel =0;

            $out ='';
            if(! $depth or $new_table){
                $out .='<table cellpadding="0" cellspacing="0" border="0"><tbody>';
            }

            if( $obj->childNodes){
                $count =count($obj->childNodes)-1;
                $index =0;
                foreach($obj->childNodes as $node){
                                $out .= $this->_get_html_node($node, $depth, $index, $count, $parent);
                                $index ++;
                }
            }else{
                $out .= $this->_get_html_node($obj, $depth, 0, 0);
            }

            if(! $depth or $new_table){
                $out .='</tbody></table>';
            }
            return $out;
        }

        /**
         * Replace extra variables as %var_name%
         *
         * @param string $text
         * @param CTreeNode $obj
         * @param integer $depth
         * @param integer $parent
         * @return string
         */
        function _unescape($text, $obj, $depth =0, $parent){
            static $qs =NULL;
            if(! $qs){
                $qs ="?". htmlentities(preg_replace("/[&?]?(path_id|tree)=[^&]+/i", "", $_SERVER['QUERY_STRING'])."&path_id");
            }

            $vars =array(
                // node text
                '%text%' =>$obj->text,
                // node image
                '%image%' =>$obj->image,
                // node id
                '%id%' =>$obj->id,
                // active image
                '%aimage%' =>$obj->aimage,
                // expanded
                '%expand%' =>$obj->expand,
                // have childs ?
                '%childs%' =>($obj->childNodes)? 1: 0,
                // level
                '%level%' =>$depth,
                // parent id
                '%parent%' =>$parent,
                // query string
                '%qs%' =>$qs,
            );

            return str_replace(array_keys($vars), array_values($vars), $text);
        }

        function process(){
            $this->tv['action'] = $this->get_input_var('action', $this->action);
            $this->tv['mouse_action'] = $this->get_input_var('mouse_action', $this->mouse_action);
            $this->tv['onclick'] = $this->get_input_var('onclick', $this->excol);
            $this->tv['simple'] = $this->get_input_var('simple', $this->simple);

            $this->tv['tree_content']=$this->_get_html($this->tree);
            return CTemplate::parse_file(CUSTOM_CONTROLS_TEMPLATE_PATH. "treeview.tpl", $this->tv);
        }

}




?>
