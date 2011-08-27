<?php
/*
--------------------------------------------------------------------------------
        Navigation Control v0.1

        history:
                2005-05-30 - created

        use cases:
                <%it:navi type="gator" level="1" depth="1" /%>
                        - top level menu
                <%it:navi type="gator" template="sitemap" level="2" depth="2" /%>
                        - chapters and subchapters usign template 'sitemap'
                          (CUSTOM_CONTROLS_TEMPLATE_PATH.'navi-sitemap.tpl')

        see also:
                - CUSTOM_CLASSES_PATH . components\navi.php - module CNaviModule for
                  dynamic binding (request uri with code-behind and template)
                - includes/config/site-structure.php - site's structure static
                  declaration file
--------------------------------------------------------------------------------
*/

class CNavi extends CTemplateControl {
        var $ss;                        // site strucure (array)
        var $sections;                // sequence of uri sections
        var $ctv;                        // control's template variables array
        var $requestSeparator;
        var $requestTrail;

        function CNavi() {
                parent::CTemplateControl('navi');

                $navi =& $this->Application->get_module('Navi');
                $this->navi =& $this->Application->get_module('Navi');
                $this->template_vars = &$app->template_vars;
                $this->ss =& $navi->ss;
                $this->sections =& $navi->sections;
                $this->requestSeparator = $navi->requestSeparator;
                $this->requestTrail = ($navi->requestSeparator=='/') ? '/' : '';
        }


        function process() {
                $template = $this->get_input_var('template');
                $this->ctv = array();
                $this->ctv['error'] = false;
                $this->_prepare_gator();
                $templateName = 'navi.tpl';
                $this->ctv['IMAGES'] = $this->Application->template_vars['IMAGES'];
                $this->ctv['HTTP'] = $this->Application->template_vars['HTTP'];
                $this->ctv['is_log'] = $this->Application->template_vars['is_logged'];
                $this->ctv['reg_uri'] = $this->navi->getUri('register/');
                $this->ctv['fp_uri'] = $this->navi->getUri('forgotpass/');

                return CTemplate::parse_file(CUSTOM_CONTROLS_TEMPLATE_PATH . $templateName, $this->ctv);
        }


        function _prepare_gator() {
                $this->ctv['available'] = true;
                // getting parent node for the navi-gator been created
                $parent = $this->ss;
				$level = 0;
                $parentLevel = 0;
                $depth = 0;
                global $RootPath;
                if (MOD_REWRITE) {
                        $parentUri = $RootPath;
                } else {
                        $parentUri = $RootPath . 'admin/?r=';
                }
                while ($level > $parentLevel+1) {
                        if (!array_key_exists('children', $parent)) {
                                $this->ctv['error'] = 'Addressed node is not defined in the site structure.';
                                $this->ctv['available'] = false;
                                return false;
                        }
                        $children = $parent['children'];
                        reset($children);
                        $found = false;
                        while($child = current($children) and !$found) {
                                if ($child['tag']==$this->sections[$parentLevel]) {
                                        $parent =& $child;
                                        $parentLevel++;
                                        $parentUri .= $child['tag'] . $this->requestSeparator;
                                        $found = true;
                                } else {
                                        next($children);
                                }
                        }
                        if (!$found) {
                                $this->ctv['error'] = 'Addressed node is not defined in the site structure.';
                                $this->ctv['available'] = false;
                                return false;
                        }
                }
                // create result array
                $res = $this->_preapare_gator_children($parent, $parentUri, $parentLevel, $depth);

                // covert hier array into required weird tv's structure
                $this->_convert_gator_into_tv($res);
        }

        function _preapare_gator_children(&$parent, $parentUri, $parentLevel, $depth) {
                $res = array();
                if (array_key_exists('children', $parent)) {
                        $children =& $parent['children'];
                        $first = true;
                        foreach ($children as $child) {
                                if (!array_key_exists('mode', $child) or $child['mode']!='hidden') {
                                        $item = array(
                                                'title' => $child['title'],
                                                'short-title' => (isset($child['short-title']) and $child['short-title']) ? $child['short-title'] : $child['title'],
                                                'uri' => $parentUri . $child['tag'] . $this->requestTrail,
                                                'first' => $first,
                                                'no-link' => (boolean) (isset($child['mode']) and $child['mode'] == 'no-link'),
                                        );
                                        $first = false;
                                        // mode
                                        if ((sizeof($this->sections)>$parentLevel) and ($child['tag']==$this->sections[$parentLevel])) {
                                                if (sizeof($this->sections)==$parentLevel+1) { // and !$this->sectionParams
                                                        $item['mode'] = 'active';
                                                } else {
                                                        $item['mode'] = 'descendant-active';
                                                }
                                        } else {
                                                $item['mode'] = 'normal';
                                        }
                                        // grandChildren recursive call
                                        $item['children'] = $this->_preapare_gator_children($child, $parentUri . $child['tag'] . $this->requestSeparator, $parentLevel+1, $depth-1);
                                        $res[] = $item;
                                }
                        }
                }
                return $res;
        }

        function _convert_gator_into_tv(&$children, $parentIdx=array()) {
                $level = sizeof($parentIdx) + 1;
                if ($level==1)
                {
                        $this->ctv['ps'] = sizeof($children);
                }
                // init array links
                $loopVar =& $this->ctv['ps'.$level];
                $titleArray =& $this->ctv['ps'.$level.'_title'];
                $shortTitleArray =& $this->ctv['ps'.$level.'_short_title'];
                $uriArray =& $this->ctv['ps'.$level.'_uri'];
                $firstArray =& $this->ctv['ps'.$level.'_first'];
                $modeActiveArray =& $this->ctv['ps'.$level.'_mode_active'];
                $modeDescendantActiveArray =& $this->ctv['ps'.$level.'_mode_descendant_active'];
                $modeNormal =& $this->ctv['ps'.$level.'_mode_normal'];
                $noLink =& $this->ctv['ps'.$level.'_no_link'];
                foreach ($parentIdx as $idx)
                {
                        $loopVar =& $loopVar[$idx];
                        $titleArray =& $titleArray[$idx];
                        $shortTitleArray =& $shortTitleArray[$idx];
                        $uriArray =& $uriArray[$idx];
                        $firstArray =& $firstArray[$idx];
                        $modeActiveArray =& $modeActiveArray[$idx];
                        $modeDescendantActiveArray =& $modeDescendantActiveArray[$idx];
                        $modeNormal =& $modeNormal[$idx];
                        $noLink =& $noLink[$idx];
                }
                foreach($children as $c => $child)
                {
                        $loopVar[] = sizeof($child['children']);
                        $titleArray[] = $child['title'];
                        $shortTitleArray[] = $child['short-title'];
                        $uriArray[] = $child['uri'];
                        $firstArray[] = $child['first'];
                        $modeActiveArray[] = ($child['mode']=='active');
                        $modeDescendantActiveArray[] = ($child['mode']=='descendant-active');
                        $modeNormal[] = ($child['mode']=='normal');
                        $noLink[] = $child['no-link'];
                        if (sizeof($child['children']))
                        {
                                $idx = $parentIdx;
                                $idx[] = $c;
                                $this->_convert_gator_into_tv($child['children'], $idx);
                        }
                }
        }


        function _prepare_crumbs() {
                $this->ctv['available'] = true;

                $level = 0;
                global $RootPath;
                $uri = $RootPath;
                $parent = $this->ss;

                // add root node
                $this->ctv['ps1_title'][] = $parent['title'];
                $this->ctv['ps1_short_title'][] = (isset($parent['short-title']) and $parent['short-title']) ? $parent['short-title'] : $parent['title'];
                $this->ctv['ps1_uri'][]  = $uri;
                $this->ctv['ps1_mode_active'][] = (bool)(!sizeof($this->sections));
                $this->ctv['ps1_mode_descendant_active'][] = (bool)(sizeof($this->sections));
                $this->ctv['ps1_mode_normal'][] = false;

                foreach ($this->sections as $section) {
                        // finding node in ss
                        $children =& $parent['children'];
                        reset($children);
                        $found = false;
                        while($child =& current($children) and !$found) {
                                if ($child['tag']==$this->sections[$level]) {
                                        $parent =& $child;
                                        $level++;
                                        $found = true;
                                } else {
                                        next($children);
                                }
                        }
                        if (!$found) {
                                $this->ctv['error'] = 'Addressed node is not defined in the site structure.';
                                $this->ctv['available'] = false;
                                return false;
                        }
                        $this->ctv['ps1_title'][] = $child['title'];
                        $this->ctv['ps1_short_title'][] = (isset($child['short-title']) and $child['short-title']) ? $child['short-title'] : $child['title'];
                        $this->ctv['ps1_uri'][]  = $uri . $child['tag'] . $this->requestTrail;
                        $this->ctv['ps1_mode_active'][] = ($level==sizeof($this->sections));
                        $this->ctv['ps1_mode_descendant_active'][] = ($level!=sizeof($this->sections));
                        $this->ctv['ps1_mode_normal'][] = false;

                        $uri .= $child['tag'] . $this->requestSeparator;
                }
                $this->ctv['ps'] = $level+1;
        }
}

?>