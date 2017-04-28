<?php
namespace Auwa;
/**
 * Menu Management
 *
 * @package Auwa \classes\
 * @copyright 2015 AuroraN
 */

/**
 * Menu Class
 *
 *
 * @author AuroraN <g.gaudin[@]auroran.fr>
 */ 
class Menu extends YamlModel{
    
    public $submenu = false;
    public $content = array();
    public $_folder = 'config/';
    public $file = 'menu';

    protected $objSchema = array(   'submenu'  => 'boolean',
                                    'content'  => 'array' ,
                            );  

    /*
     * Get all menus from yaml file
     */
    public static function getAllMenus($withSub=false, $excl=array()){
        $m = new Menu();
        $menus = $m->getAll();
        if (!$withSub || !empty($excl) )
            foreach ($menus as $name => $menu)
                if ( ($menu['submenu'] && !$withSub) || in_array(trim($name), $excl)) unset($menus[$name]);
        return $menus==null ? array() : $menus;
    }

    /*
     * Get a menu with all submenus
     */
    public static function getMenu($name='Main', $recursive=true){
        $menu = new Menu($name);
        if ($recursive && isset($menu) && is_array($menu->content)){
            foreach ($menu->content  as $key => $item) {
                if ( !empty($item['menu']) && !is_array($item['menu'])){
                    $menu->content[$key]['menu'] = self::getMenu($item['menu']);
                }
            }
        }
        return isset($menu->content) ? $menu->content : array();
    }

    /*
     * Get an item from a menu
     */
    public static function getItemFromMenu($key=0, $menu='main'){
        $m = self::getMenu($menu, false);
        return isset($m[$key]) ? $m[$key] : false;
    }

    /*
     * Set a menu into the yaml file
     */
    public static function setMenu($name, $items){
        if (!$name || $name=='') return false; 
        $menu = self::checkMenu($name);
        $nm = array();
        foreach ($items as $key => $value) {
            $nm[] = $value;
        }
        $menu->content  = $nm;
        return $menu->update();
    }
    public static function setSubMenu($menu, $key, $sub){
        $m = self::checkMenu($menu);
        $m->content[$key]['menu'] = $sub;
       return $m->update();
    }
    public static function checkMenu($name, $menu=false){
        if (!$menu) $menu = new Menu($name);
        if (!$menu->_exists){
            $menu->submenu = true;
            $menu->content = array();
        }
        return $menu;
    }

    /*
     * Create a menu into the yaml file
     */
    public static function renameMenu($name=false, $newName=false, $origin=false, $key=false){
        if ($key!==false && $origin){
            $menu = self::checkMenu($origin);
            $menu->content[$key]['menu'] = $newName;
            $menu->update();
        }
        $menus = \ConfigFile:: getConfig('config/menu');
        if ($newName!=='' && !isset($menus[$newName]) ){
            if ($name) {
                if (!isset($menus[$name]) && !is_array($menus[$name])) return false;
                $nm = $menus[$name];
                $menus[$newName] = $nm;
                unset($menus[$name]);
             // update all reference to this menu
                foreach ($menus as $n => $m) {
                    if ($n==$newName) continue;
                    foreach ($m['content'] as $key => $item) {
                        if (isset($item['menu']) && $item['menu']==$name)
                             $menus[$n]['content'][$key]['menu'] = $newName;
                    }
                }
            } else {
                $menus[$newName] = array('submenu'=>true, 'content'=>array());
            }
        }
        return \ConfigFile:: setConfig('config/menu', $menus);
    }

    public static function setItemToMenu($name, $key, $item){
        if (!$name || empty($item)) return false; 
        $menu= self::checkMenu($name);
        if ($key!==-1)
            $menu->content[(int)$key] = $item;
        else
            $menu->content[] = $item;
        return $menu->update();
    }
}

?>