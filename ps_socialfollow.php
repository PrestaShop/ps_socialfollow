<?php
/*
* 2007-2016 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
if (!defined('_CAN_LOAD_FILES_')) {
    exit;
}
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
class Ps_Socialfollow extends Module implements WidgetInterface
{
    public function __construct()
    {
        $this->name = 'ps_socialfollow';
        $this->version = '1.0.1';
        $this->author = 'PrestaShop';
        $this->bootstrap = true;
        parent::__construct();
        $this->displayName = $this->getTranslator()->trans('Social media follow links', array(), 'Modules.SocialFollow.Admin');
        $this->description = $this->getTranslator()->trans('Allows you to add information about your brand\'s social networking accounts.', array(), 'Modules.SocialFollow.Admin');
        $this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);
    }
    public function install()
    {
        return (parent::install() and Configuration::updateValue('BLOCKSOCIAL_FACEBOOK', '') &&
            Configuration::updateValue('BLOCKSOCIAL_TWITTER', '') &&
            Configuration::updateValue('BLOCKSOCIAL_RSS', '') &&
            Configuration::updateValue('BLOCKSOCIAL_YOUTUBE', '') &&
            Configuration::updateValue('BLOCKSOCIAL_GOOGLE_PLUS', '') &&
            Configuration::updateValue('BLOCKSOCIAL_PINTEREST', '') &&
            Configuration::updateValue('BLOCKSOCIAL_VIMEO', '') &&
            Configuration::updateValue('BLOCKSOCIAL_INSTAGRAM', '') &&
			Configuration::updateValue('BLOCKSOCIAL_LINKEDIN', '') &&
            $this->registerHook('displayFooter'));
    }
    public function uninstall()
    {
        //Delete configuration
        return (Configuration::deleteByName('BLOCKSOCIAL_FACEBOOK') &&
            Configuration::deleteByName('BLOCKSOCIAL_TWITTER') &&
            Configuration::deleteByName('BLOCKSOCIAL_RSS') &&
            Configuration::deleteByName('BLOCKSOCIAL_YOUTUBE') &&
            Configuration::deleteByName('BLOCKSOCIAL_GOOGLE_PLUS') &&
            Configuration::deleteByName('BLOCKSOCIAL_PINTEREST') &&
            Configuration::deleteByName('BLOCKSOCIAL_VIMEO') &&
            Configuration::deleteByName('BLOCKSOCIAL_INSTAGRAM') &&
			Configuration::deleteByName('BLOCKSOCIAL_LINKEDIN', '') &&
            parent::uninstall());
    }
    public function getContent()
    {
        // If we try to update the settings
        $output = '';
        if (Tools::isSubmit('submitModule')) {
            Configuration::updateValue('BLOCKSOCIAL_FACEBOOK', Tools::getValue('blocksocial_facebook', ''));
            Configuration::updateValue('BLOCKSOCIAL_TWITTER', Tools::getValue('blocksocial_twitter', ''));
            Configuration::updateValue('BLOCKSOCIAL_RSS', Tools::getValue('blocksocial_rss', ''));
            Configuration::updateValue('BLOCKSOCIAL_YOUTUBE', Tools::getValue('blocksocial_youtube', ''));
            Configuration::updateValue('BLOCKSOCIAL_GOOGLE_PLUS', Tools::getValue('blocksocial_google_plus', ''));
            Configuration::updateValue('BLOCKSOCIAL_PINTEREST', Tools::getValue('blocksocial_pinterest', ''));
            Configuration::updateValue('BLOCKSOCIAL_VIMEO', Tools::getValue('blocksocial_vimeo', ''));
            Configuration::updateValue('BLOCKSOCIAL_INSTAGRAM', Tools::getValue('blocksocial_instagram', ''));
			Configuration::updateValue('BLOCKSOCIAL_LINKEDIN', Tools::getValue('blocksocial_linkedin', ''));
            $this->_clearCache('blocksocial.tpl');
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules').'&configure='.$this->name.'&tab_module='.$this->tab.'&conf=4&module_name='.$this->name);
        }
        return $output.$this->renderForm();
    }
    public function renderForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->getTranslator()->trans('Settings', array(), 'Admin.Global'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->getTranslator()->trans('Facebook URL', array(), 'Modules.SocialFollow.Admin'),
                        'name' => 'blocksocial_facebook',
                        'desc' => $this->getTranslator()->trans('Your Facebook fan page.', array(), 'Modules.SocialFollow.Admin'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->getTranslator()->trans('Twitter URL', array(), 'Modules.SocialFollow.Admin'),
                        'name' => 'blocksocial_twitter',
                        'desc' => $this->getTranslator()->trans('Your official Twitter account.', array(), 'Modules.SocialFollow.Admin'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->getTranslator()->trans('RSS URL', array(), 'Modules.SocialFollow.Admin'),
                        'name' => 'blocksocial_rss',
                        'desc' => $this->getTranslator()->trans('The RSS feed of your choice (your blog, your store, etc.).', array(), 'Modules.SocialFollow.Admin'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->getTranslator()->trans('YouTube URL', array(), 'Modules.SocialFollow.Admin'),
                        'name' => 'blocksocial_youtube',
                        'desc' => $this->getTranslator()->trans('Your official YouTube account.', array(), 'Modules.SocialFollow.Admin'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->getTranslator()->trans('Google+ URL:', array(), 'Modules.SocialFollow.Admin'),
                        'name' => 'blocksocial_google_plus',
                        'desc' => $this->getTranslator()->trans('Your official Google+ page.', array(), 'Modules.SocialFollow.Admin'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->getTranslator()->trans('Pinterest URL:', array(), 'Modules.SocialFollow.Admin'),
                        'name' => 'blocksocial_pinterest',
                        'desc' => $this->getTranslator()->trans('Your official Pinterest account.', array(), 'Modules.SocialFollow.Admin'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->getTranslator()->trans('Vimeo URL:', array(), 'Modules.SocialFollow.Admin'),
                        'name' => 'blocksocial_vimeo',
                        'desc' => $this->getTranslator()->trans('Your official Vimeo account.', array(), 'Modules.SocialFollow.Admin'),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->getTranslator()->trans('Instagram URL:', array(), 'Modules.SocialFollow.Admin'),
                        'name' => 'blocksocial_instagram',
                        'desc' => $this->getTranslator()->trans('Your official Instagram account.', array(), 'Modules.SocialFollow.Admin'),
                    ),
					array(
                        'type' => 'text',
                        'label' => $this->getTranslator()->trans('Linkedin URL:', array(), 'Modules.SocialFollow.Admin'),
                        'name' => 'blocksocial_linkedin',
                        'desc' => $this->getTranslator()->trans('Your official Linkedin account.', array(), 'Modules.SocialFollow.Admin'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->getTranslator()->trans('Save', array(), 'Admin.Global'),
                )
            ),
        );
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table =  $this->table;
        $helper->submit_action = 'submitModule';
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
        );
        return $helper->generateForm(array($fields_form));
    }
    public function getConfigFieldsValues()
    {
        return array(
            'blocksocial_facebook' => Tools::getValue('blocksocial_facebook', Configuration::get('BLOCKSOCIAL_FACEBOOK')),
            'blocksocial_twitter' => Tools::getValue('blocksocial_twitter', Configuration::get('BLOCKSOCIAL_TWITTER')),
            'blocksocial_rss' => Tools::getValue('blocksocial_rss', Configuration::get('BLOCKSOCIAL_RSS')),
            'blocksocial_youtube' => Tools::getValue('blocksocial_youtube', Configuration::get('BLOCKSOCIAL_YOUTUBE')),
            'blocksocial_google_plus' => Tools::getValue('blocksocial_google_plus', Configuration::get('BLOCKSOCIAL_GOOGLE_PLUS')),
            'blocksocial_pinterest' => Tools::getValue('blocksocial_pinterest', Configuration::get('BLOCKSOCIAL_PINTEREST')),
            'blocksocial_vimeo' => Tools::getValue('blocksocial_vimeo', Configuration::get('BLOCKSOCIAL_VIMEO')),
            'blocksocial_instagram' => Tools::getValue('blocksocial_instagram', Configuration::get('BLOCKSOCIAL_INSTAGRAM')),
			'blocksocial_linkedin' => Tools::getValue('blocksocial_linkedin', Configuration::get('BLOCKSOCIAL_LINKEDIN')),
        );
    }
    public function renderWidget($hookName = null, array $configuration = [])
    {
        if (!$this->isCached('ps_socialfollow.tpl', $this->getCacheId())) {
            $this->smarty->assign($this->getWidgetVariables($hookName, $configuration));
        }
        return $this->display(__FILE__, 'ps_socialfollow.tpl', $this->getCacheId());
    }
    public function getWidgetVariables($hookName = null, array $configuration = [])
    {
        $social_links = [];
        if (Configuration::get('BLOCKSOCIAL_FACEBOOK')) {
            $social_links['facebook'] = [
                'label' => $this->getTranslator()->trans('Facebook', array(), 'Modules.SocialFollow.Shop'),
                'class' => 'facebook',
                'url' => Configuration::get('BLOCKSOCIAL_FACEBOOK'),
            ];
        }
        if (Configuration::get('BLOCKSOCIAL_TWITTER')) {
            $social_links['twitter'] = [
                'label' => $this->getTranslator()->trans('Twitter', array(), 'Modules.SocialFollow.Shop'),
                'class' => 'twitter',
                'url' => Configuration::get('BLOCKSOCIAL_TWITTER'),
            ];
        }
        if (Configuration::get('BLOCKSOCIAL_RSS')) {
            $social_links['rss'] = [
                'label' => $this->getTranslator()->trans('Rss', array(), 'Modules.SocialFollow.Shop'),
                'class' => 'rss',
                'url' => Configuration::get('BLOCKSOCIAL_RSS'),
            ];
        }
        if (Configuration::get('BLOCKSOCIAL_YOUTUBE')) {
            $social_links['youtube'] = [
                'label' => $this->getTranslator()->trans('YouTube', array(), 'Modules.SocialFollow.Shop'),
                'class' => 'youtube',
                'url' => Configuration::get('BLOCKSOCIAL_YOUTUBE'),
            ];
        }
        if (Configuration::get('BLOCKSOCIAL_GOOGLE_PLUS')) {
            $social_links['googleplus'] = [
                'label' => $this->getTranslator()->trans('Google +', array(), 'Modules.SocialFollow.Shop'),
                'class' => 'googleplus',
                'url' => Configuration::get('BLOCKSOCIAL_GOOGLE_PLUS'),
            ];
        }
        if (Configuration::get('BLOCKSOCIAL_PINTEREST')) {
            $social_links['pinterest'] = [
                'label' => $this->getTranslator()->trans('Pinterest', array(), 'Modules.SocialFollow.Shop'),
                'class' => 'pinterest',
                'url' => Configuration::get('BLOCKSOCIAL_PINTEREST'),
            ];
        }
        if (Configuration::get('BLOCKSOCIAL_VIMEO')) {
            $social_links['vimeo'] = [
                'label' => $this->getTranslator()->trans('Vimeo', array(), 'Modules.SocialFollow.Shop'),
                'class' => 'vimeo',
                'url' => Configuration::get('BLOCKSOCIAL_VIMEO'),
            ];
        }
        if (Configuration::get('BLOCKSOCIAL_INSTAGRAM')) {
            $social_links['instagram'] = [
                'label' => $this->getTranslator()->trans('Instagram', array(), 'Modules.SocialFollow.Shop'),
                'class' => 'instagram',
                'url' => Configuration::get('BLOCKSOCIAL_INSTAGRAM'),
            ];
        }
		if (Configuration::get('BLOCKSOCIAL_LINKEDIN')) {
            $social_links['linkedin'] = [
                'label' => $this->getTranslator()->trans('Linkedin', array(), 'Modules.SocialFollow.Shop'),
                'class' => 'linkedin',
                'url' => Configuration::get('BLOCKSOCIAL_LINKEDIN'),
            ];
        }
        return [
            'social_links' => $social_links,
        ];
    }
}