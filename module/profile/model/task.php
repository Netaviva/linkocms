<?php

class Profile_Model_Task extends Linko_Model
{
    public function module_enable()
    {
        $iComponentId = Linko::Database()->table('module_component')
            ->select('component_id')
            ->where('route_id','=', 'user:profile')
            ->query()->fetchValue();

        $aRouteRule = array( 'username' => ':alnum', 'slug' => '.*');
        $sRouteRule = serialize($aRouteRule);

        //updating the component file for the route id user:profile
        Linko::Database()->table('module_component')
                ->update(array('component_file' => 'profile/index', 'route_rule' => $sRouteRule))
                ->where('route_id', '=', 'user:profile')->query();

        //also update the page url in the page table
        Linko::Database()->table('page')
                ->update(array('page_url' => 'profile/[username](/[slug])'))
                ->where('component_id', '=', $iComponentId)
                ->query();
    }

    public function module_disable()
    {
        $iComponentId = Linko::Database()->table('module_component')
            ->select('component_id')
            ->where('route_id','=', 'user:profile')
            ->query()->fetchValue();

        $aRouteRule = array( 'username' => ':alnum');
        $sRouteRule = serialize($aRouteRule);

        //updating the component file for the route id user:profile
        Linko::Database()->table('module_component')
            ->update(array('component_file' => 'user/profile', 'route_rule' => $sRouteRule))
            ->where('route_id', '=', 'user:profile')->query();

        //also update the page url in the page table
        //Linko::Database()->table('page')
            //->update(array('page_url' => 'pro/[username]'))
            //->where('component_id', '=', $iComponentId)
            //->query();

    }
}