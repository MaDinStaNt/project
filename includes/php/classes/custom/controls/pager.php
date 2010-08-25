<?
/*
Class CPagerControl v 1.0.1.0
Pager

history:
        v 1.0.1.0 - new parameters (url_begin, url_end) added (VK)
        v 1.0.0.0 - created (VK)
--------------------------------------------------------------------------------
*/
// class="nav-butt-img"
class CPagerControl extends CTemplateControl
{
        var $prev = '<img border="0" src="/images/prev.gif" title="Previous page" alt=""/>'; // html code to use as prev
        var $next = '<img border="0" src="/images/next.gif" title="Next page" alt=""/>'; // html code to use as next

        var $pages; // total pages (auto)
        var $items; // total items
        var $page; // current page (0-based)

        var $pages_per_page = 10; // pages per page
        var $items_per_page = 10; // items per page

        var $after_prev = ''; // html code to use after prev sign
        var $before_next = ''; // html code to use before next sign
        var $a_sel_class = ''; // CSS class for selected anchor
        var $a_unsel_class = 'page_selected'; // CSS class for unselected anchor

        var $link_start = '<td class="nav-butt accent" nowrap="nowrap" valign="middle">';
        var $link_end = '</td>';
        var $link_middle = '';

        var $url_begin = 'page=';
        var $url_end = '';
        var $query_string = '';

        function CPagerControl($subname){
                parent::CTemplateControl('Pager', $subname);
                $this->preget = InGet('r', '');
        }

        function get_anc($page, $sel = -1, $start = true, $alt = false)
        {
        		$page = $page + 1;
                if ($this->preget == '')
                {
                if ($start)
                    if ($page != $sel) return '<a href="'.$this->url_begin.$page.$this->url_end.'" class="'.$this->a_sel_class.'" '.(($alt)?("title=\"".$alt."\""):("")).'>';
                    else return '<span class="'.$this->a_unsel_class.'">';
                else
                    if ($page != $sel) return '</a>';
                    else return '</span>';
                }else
                {
                if ($start)
                {
                    if ($page != $sel) return '<a href="'.$this->url_begin.$page.$this->url_end.'" class="'.$this->a_sel_class.'" '.(($alt)?("title=\"".$alt."\""):("")).'>';
                    else return '<span class="'.$this->a_unsel_class.'">';
                }
                else
                    if ($page != $sel) return '</a>';
                    else return '</span>';
                }
        }

        function parse_vars($vars_str)
        {
                parent::parse_vars($vars_str);

                $this->prev = $this->get_input_var('prev', $this->prev);
                $this->next = $this->get_input_var('next', $this->next);

                $this->after_prev = $this->get_input_var('after_prev', $this->after_prev);
                $this->before_next = $this->get_input_var('before_next', $this->before_next);
                $this->a_sel_class = $this->get_input_var('a_sel_class', $this->a_sel_class);
                $this->a_unsel_class = $this->get_input_var('a_unsel_class', $this->a_unsel_class);

                $this->link_start = $this->get_input_var('link_start', $this->link_start);
                $this->link_end = $this->get_input_var('link_end', $this->link_end);
                $this->link_middle = $this->get_input_var('link_middle', $this->link_middle);

                $this->url_begin = $this->get_input_var('url_begin', $this->url_begin);
                $this->url_end = $this->get_input_var('url_end', $this->url_end);
        }

        function process()
        {
                $this->pages = ceil($this->items / $this->items_per_page);
                $out = '';
                if ( $this->pages > 1) $out .= '<table cellpadding="0" cellspacing="0">';
                if ($this->page > 1)
                        $out .= $this->link_start . $this->get_anc($this->page-2) . $this->prev . $this->get_anc($this->page-1, -1, false) . $this->after_prev . $this->link_end;
                if ($this->page >= $this->pages_per_page)
                {
                    $out .= $this->link_start;

                    $cur10 = floor(($this->page)/10) * 10;
                    $cursor = 10;
                    while($cur10 > $cursor)
                    {
                        $out .= $this->get_anc($cursor) . ($cursor + 1) . $this->get_anc($cursor, -1, false);
                        $cursor = $cursor + 10;
                        if ($cur10 > $cursor)
                            $out .= " |";
                        $out .= "&nbsp;";
                    }

                    $midd = (floor(($this->page)/10 - 1) * 10 + 5) - 1;
                    if ($this->pages > $midd)
                    {
                        $out .= $this->get_anc($midd, -1, true, "Go to page ".($midd+1)) . "..." . $this->get_anc($midd, -1, false);
                    }
                    $out .= "&nbsp;" . $this->link_end;
                }

                $sp = floor($this->page/$this->pages_per_page)*$this->pages_per_page;
                $ep = $sp + $this->pages_per_page;
                if ($ep > $this->pages)
                        $ep = $this->pages;

                if ( $this->pages > 1)
                        for ($i = $sp; $i<$ep; $i++)
                        {
                                if ($i > $sp)
                                        $out .= $this->link_middle;
                                $out .= $this->link_start . (($out!='')?(''):('')) . $this->get_anc($i, $this->page) . (1+$i) . $this->get_anc($i, $this->page, false) . $this->link_end;
                        }

                if ($this->pages >= ($this->pages_per_page*2))
                {
                    $out .= $this->link_start . "&nbsp;";
                    $midd = (floor(($this->page)/10 + 1) * 10 + 5) - 1;
                    if ($this->pages > $midd)
                    {
                        $out .= $this->get_anc($midd, -1, true, "Go to page ".($midd+1)) . "..." . $this->get_anc($midd, -1, false);
                    }
                    $cur10 = floor(($this->page)/10 + 2) * 10;
                    while($cur10 < $this->pages)
                    {
                        $out .= "&nbsp;" . $this->get_anc($cur10) . ($cur10+1) . $this->get_anc($cur10, -1, false);
                        $cur10 = $cur10 + 10;
                        if ($cur10 < $this->pages)
                            $out .= " |";
                    }
                }

                if ( ($this->page) < $this->pages)
                        $out .= $this->link_start . (($out!='')?(''):('')) . $this->before_next . $this->get_anc($this->page) . $this->next . $this->get_anc($this->page+1, -1, false) . $this->link_end;

                if ( $this->pages > 1) $out .= '</table>';

                return CTemplate::parse_string($out);
        }
}
?>