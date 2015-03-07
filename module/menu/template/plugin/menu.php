<?php

class Template_Plugin_Menu
{
    private $_iLimit = 0;

    private $_iOffset = 0;

    public function start($aParams = array())
    {
        /**
         * @var int $limit no. of menu to return before stopping
         * @var int $offset no. of menu to skip before returning
         * @var string $location menu location
         * @var string $container_class menu ul class
         * @var string $item_with_children_class
         * @var int $depth nested child depth
         * @var string $dropdown_menu_class class for dropdown ul
         */
        extract(array_merge(array(
            'limit' => 0,
            'offset' => 0,
            'location' => 'main_menu',
            'container_class' => 'menu-list',
            'item_with_children_class' => 'has-children',
            'dropdown_menu_class' => 'dropdown-menu',
            'depth' => 0,
        ), $aParams));

        $this->_iLimit = (int)$limit;
        $this->_iOffset = (int)$offset;

        $aMenus = Linko::Model('Menu')->getMenuForLocation($location);

        $sHtml = Html::openTag('ul', array('class' => $container_class));
        $sHtml .= $this->_build($aMenus, array(
            'dropdown_menu_class' => $dropdown_menu_class,
            'item_with_children_class' => $item_with_children_class
        ), $depth);
        $sHtml .= Html::closeTag('ul');

        echo $sHtml;
    }

    private function _build($aMenus, $aParam = array(), $iDepthLimit = 0, $iDepth = 0)
    {
        $sHtml = null;

        static $iLimit;
        $iCnt = 0;

        foreach($aMenus as $aMenu)
        {
            $iCnt++;

            if($this->_iOffset && ($this->_iOffset >= $iCnt))
            {
                continue;
            }

            $bHasChildren = count($aMenu['children']) && (($iDepthLimit == 0) || (($iDepthLimit > 0) && ($iDepth < $iDepthLimit)));

            $sHtml .= Html::openTag('li', array('class' => ($bHasChildren ? $aParam['item_with_children_class'] : '')));

            $sHtml .= Html::link($aMenu['menu_item_title'], $aMenu['menu_item_url'], array(
                'target' => $aMenu['menu_item_target'] == 0 ? '_self' : '_blank'
            ));

            if($bHasChildren)
            {
                $sHtml .= Html::openTag('ul', array('class' => $aParam['dropdown_menu_class']));

                $sHtml .= $this->_build($aMenu['children'], $aParam, $iDepthLimit, $iDepth + 1);

                $sHtml .= Html::closeTag('ul');
            }
            else
            {
                $iLimit++;

                if($this->_iLimit && $iLimit >= $this->_iLimit)
                {
                    return $sHtml;
                }
            }

            $sHtml .= Html::closeTag('li');
        }

        return $sHtml;
    }
}