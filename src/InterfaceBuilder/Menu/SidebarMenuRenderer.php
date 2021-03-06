<?php

namespace Soda\Cms\InterfaceBuilder\Menu;

class SidebarMenuRenderer
{
    public function renderRoot(Menu $menu)
    {
        return soda_cms_view('partials.menu.root', compact('menu'));
    }

    public function renderMenu(MenuItem $item)
    {
        $item->setAttribute('class', 'nav-item-group'.($item->isCurrent() ? ' active' : ''));

        $item->setLinkAttribute('role', 'button');
        $item->setLinkAttribute('data-toggle', 'collapse');
        $item->setLinkAttribute('data-parent', '.nav-sidebar');
        $item->setLinkAttribute('href', '#'.str_slug($item->getName()).'-nav');
        $item->setLinkAttribute('aria-expanded', $item->isCurrent() ? 'true' : 'false');
        $item->setLinkAttribute('aria-controls', '#'.str_slug($item->getName()).'-nav');
        $item->setLinkAttribute('class', 'nav-link'); //todo

        $item->setIconAttribute('class', $item->getIcon()); //todo

        $item->setChildrenAttribute('id', str_slug($item->getName()).'-nav');
        $item->setChildrenAttribute('class', 'collapse'.($item->isCurrent() ? ' in' : '')); //todo

        return soda_cms_view('partials.menu.group', compact('item'));
    }

    public function renderItem(MenuItem $item)
    {
        $item->setAttribute('class', 'nav-item'.($item->isCurrent() ? ' active' : ''));

        $item->setLinkAttribute('class', 'nav-link'); //todo
        $item->setLinkAttribute('href', $item->getUrl());

        $item->setIconAttribute('class', $item->getIcon()); //todo

        return soda_cms_view('partials.menu.item', compact('item'));
    }
}
