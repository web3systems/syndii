<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\HelperService;
use App\Models\MenuItem;
use Illuminate\Support\Collection;

class MenuController extends Controller
{
    public function getMenu()
    {
        if (auth()->user()->isAdmin()) {
            $menuItems = MenuItem::where('parent_key', null)
                            ->where('is_active', true)
                            ->orderBy('order')
                            ->get();
        } else {
            $menuItems = MenuItem::where('parent_key', null)
                            ->where('is_active', true)
                            ->where('is_admin', false)
                            ->orderBy('order')
                            ->get();
        }
        
        return $this->buildMenuTree($menuItems);
    }

    public function getUserMenu()
    {
        $menuItems = MenuItem::where('parent_key', null)
                        ->where('is_active', true)
                        ->where('is_admin', false)
                        ->orderBy('order', 'asc')
                        ->get();
        
        
        return $this->buildMenuTree($menuItems);
    }

    public function getAdminMenu()
    {
        $menuItems = MenuItem::where('parent_key', null)
                        ->where('is_active', true)
                        ->where('is_admin', true)
                        ->orderBy('order')
                        ->get();
        
        
        return $this->buildMenuTree($menuItems);
    }

    protected function buildMenuTree(Collection $items)
    {
        $menu = [];
        foreach ($items as $item) {
            $access_status = HelperService::checkFeatureAccess($item->key);
            $menuItem = [
                'id' => $item->id,
                'key' => $item->key,
                'route' => $item->route,
                'route_slug' => $item->route_slug,
                'label' => $item->label,
                'icon' => $item->icon,
                'type' => $item->type,
                'url' => $item->url,
                'badge_text' => $item->badge_text,
                'badge_type' => $item->badge_type,
                'has_access' => $access_status,
            ];

            // Get children for this menu item
            $children = MenuItem::where('parent_key', $item->id)
                              ->where('is_active', true)
                              ->orderBy('order')
                              ->get();

            if ($children->isNotEmpty()) {
                $menuItem['children'] = $this->buildMenuTree($children);
            }

            $menu[] = $menuItem;
        }

        return $menu;
    }
}
