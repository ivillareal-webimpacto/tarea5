<?php
/**
 * 2007-2020 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2020 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class Tarea5 extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'tarea5';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'webimpacto';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('tarea5');
        $this->description = $this->l('tarea5');

        $this->confirmUninstall = $this->l('');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        return parent::install() &&
            $this->installDB() &&
            $this->registerHook('header') &&
            $this->registerHook('actionFrontControllerSetMedia') &&
            $this->registerHook('displayProductAdditionalInfo') &&
            $this->registerHook('displayAdminProductsMainStepLeftColumnMiddle') &&
            $this->registerHook('backOfficeHeader');
    }

    public function uninstall()
    {
        return parent::uninstall() &&
            $this->uninstallDB();
    }

    public function installDB()
    {
        $sqlInstall = "ALTER TABLE "._DB_PREFIX_."product "."ADD texto_detalle VARCHAR(255) NULL";
        $returnSql = DB::getInstance()->execute($sqlInstall);
        return $returnSql;
    }

    public function uninstallDB()
    {
        $sqlInstall = "ALTER TABLE "._DB_PREFIX_."product ". "DROP texto_detalle";
        $returnSql = DB::getInstance()->execute($sqlInstall);
        return $returnSql;
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */

        $this->context->smarty->assign('module_dir', $this->_path);
        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');
        return $output;
    }


    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }

    public function hookActionFrontControllerSetMedia()
    {
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
    }

    public function hookdisplayProductAdditionalInfo($params)
    {
        $producto = $params['product'];
        //sacamos el campo texto_detalle;
        $sql="
         SELECT * FROM "._DB_PREFIX_."product WHERE id_product ='".$producto->id_product."';
        ";
        $row = Db::getInstance()->getRow($sql);

        $this->context->smarty->assign([
            'texto_especial' => $row['texto_detalle']
        ]);

        return $this->display(__FILE__, 'tarea5.tpl');
    }

    public function hookdisplayAdminProductsMainStepLeftColumnMiddle($params)
    {
        $product = new Product($params['id_product']);
        $this->context->smarty->assign([
            'texto_detalle' => $product->texto_detalle
        ]);

        return $this->display(__FILE__, 'campo_extra.tpl');
    }
}
