<?php
/**
 * Innomedia
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 *
 * @copyright 2008-2014 Innoteam Srl
 * @license   http://www.innomatic.io/license/   BSD License
 * @link      http://www.innomatic.io
 * @since     1.0.0
 */
namespace Innomedia;

/**
 *
 * @author    Alex Pagnoni <alex.pagnoni@innomatic.io>
 * @copyright 2008-2014 Innoteam Srl
 * @since     1.0.0
 */
class Module
{
    protected $name;

    public function __construct($moduleName)
    {
        $this->name = $moduleName;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getHome()
    {
        return Context::instance('\Innomedia\Context')->getModulesHome().$this->name.'/';
    }

    public function hasPages()
    {
        return file_exists($this->getHome() . 'pages');
    }

    public function hasBlocks()
    {
        return file_exists($this->getHome() . 'blocks');
    }

    public function getPagesList()
    {
        $list = array();
        if (! $this->hasPages()) {
            return $list;
        }
        if ($dh = opendir($this->getHome() . 'pages')) {
            while (($file = readdir($dh)) !== false) {
                if ($file != '.' and $file != '..' and is_file($this->getHome() . 'pages/' . $file) and strrpos($file, '.yml') and !strrpos($file, '.local.yml')) {
                    $list[] = substr($file, 0, strrpos($file, '.yml'));
                }
            }
            closedir($dh);
        }
        return $list;
    }

    public function getBlocksList()
    {
        $list = array();
        if (! $this->hasBlocks()) {
            return $list;
        }
        if ($dh = opendir($this->getHome() . 'blocks')) {
            while (($file = readdir($dh)) !== false) {
                if ($file != '.' and $file != '..' and is_file($this->getHome() . 'blocks/' . $file) and strrpos($file, '.yml') and !strrpos($file, '.local.yml')) {
                    $list[] = substr($file, 0, strrpos($file, '.yml'));
                }
            }
            closedir($dh);
        }
        return $list;
    }
}

?>