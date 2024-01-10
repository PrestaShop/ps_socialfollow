<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Validation;

class Ps_Socialfollow extends Module implements WidgetInterface
{
    const SOCIAL_NETWORKS = [
        'FACEBOOK',
        'TWITTER',
        'RSS',
        'YOUTUBE',
        'PINTEREST',
        'VIMEO',
        'INSTAGRAM',
        'LINKEDIN',
        'TIKTOK',
        'DISCORD',
    ];

    private $templateFile;

    public function __construct()
    {
        $this->name = 'ps_socialfollow';
        $this->tab = 'advertising_marketing';
        $this->author = 'PrestaShop';
        $this->version = '2.3.2';

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->trans('Social media follow links', [], 'Modules.Socialfollow.Admin');
        $this->description = $this->trans('Facebook, Twitter, let your customers know where to follow you and increase your community.', [], 'Modules.Socialfollow.Admin');

        $this->ps_versions_compliancy = ['min' => '1.7.4.0', 'max' => _PS_VERSION_];

        $this->templateFile = 'module:ps_socialfollow/ps_socialfollow.tpl';
    }

    public function install()
    {
        return parent::install() &&
            Configuration::updateValue('BLOCKSOCIAL_FACEBOOK', '') &&
            Configuration::updateValue('BLOCKSOCIAL_TWITTER', '') &&
            Configuration::updateValue('BLOCKSOCIAL_RSS', '') &&
            Configuration::updateValue('BLOCKSOCIAL_YOUTUBE', '') &&
            Configuration::updateValue('BLOCKSOCIAL_PINTEREST', '') &&
            Configuration::updateValue('BLOCKSOCIAL_VIMEO', '') &&
            Configuration::updateValue('BLOCKSOCIAL_INSTAGRAM', '') &&
            Configuration::updateValue('BLOCKSOCIAL_LINKEDIN', '') &&
            Configuration::updateValue('BLOCKSOCIAL_TIKTOK', '') &&
            Configuration::updateValue('BLOCKSOCIAL_DISCORD', '') &&
            $this->registerHook('actionFrontControllerSetMedia') &&
            $this->registerHook('displayFooter');
    }

    public function uninstall()
    {
        return Configuration::deleteByName('BLOCKSOCIAL_FACEBOOK') &&
            Configuration::deleteByName('BLOCKSOCIAL_TWITTER') &&
            Configuration::deleteByName('BLOCKSOCIAL_RSS') &&
            Configuration::deleteByName('BLOCKSOCIAL_YOUTUBE') &&
            Configuration::deleteByName('BLOCKSOCIAL_PINTEREST') &&
            Configuration::deleteByName('BLOCKSOCIAL_VIMEO') &&
            Configuration::deleteByName('BLOCKSOCIAL_INSTAGRAM') &&
            Configuration::deleteByName('BLOCKSOCIAL_LINKEDIN') &&
            Configuration::deleteByName('BLOCKSOCIAL_TIKTOK') &&
            Configuration::deleteByName('BLOCKSOCIAL_DISCORD') &&
            parent::uninstall();
    }

    public function getContent()
    {
        $html = '';
        if (Tools::isSubmit('submitModule')) {
            $result = $this->updateFields();
            if ($result === true) {
                $this->_clearCache('*');
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true, [], [
                    'configure' => $this->name,
                    'conf' => 4,
                ]));
            } else {
                $html .= $this->displayError(implode('<br />', $result));
            }
        }

        if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP) {
            $html .= '<p class="alert alert-warning">' .
                $this->trans('Please choose a shop to edit the social media links.', [], 'Modules.Socialfollow.Admin') .
                '</p>';
        } else {
            $html .= $this->renderForm();
        }

        return $html;
    }

    public function _clearCache($template, $cache_id = null, $compile_id = null)
    {
        parent::_clearCache($this->templateFile);
    }

    public function renderForm()
    {
        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->trans('Settings', [], 'Admin.Global'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'lang' => true,
                        'label' => $this->trans('Facebook URL', [], 'Modules.Socialfollow.Admin'),
                        'name' => 'BLOCKSOCIAL_FACEBOOK',
                        'desc' => $this->trans('Your Facebook fan page.', [], 'Modules.Socialfollow.Admin'),
                    ],
                    [
                        'type' => 'text',
                        'lang' => true,
                        'label' => $this->trans('Twitter URL', [], 'Modules.Socialfollow.Admin'),
                        'name' => 'BLOCKSOCIAL_TWITTER',
                        'desc' => $this->trans('Your official Twitter account.', [], 'Modules.Socialfollow.Admin'),
                    ],
                    [
                        'type' => 'text',
                        'lang' => true,
                        'label' => $this->trans('RSS URL', [], 'Modules.Socialfollow.Admin'),
                        'name' => 'BLOCKSOCIAL_RSS',
                        'desc' => $this->trans('The RSS feed of your choice (your blog, your store, etc.).', [], 'Modules.Socialfollow.Admin'),
                    ],
                    [
                        'type' => 'text',
                        'lang' => true,
                        'label' => $this->trans('YouTube URL', [], 'Modules.Socialfollow.Admin'),
                        'name' => 'BLOCKSOCIAL_YOUTUBE',
                        'desc' => $this->trans('Your official YouTube account.', [], 'Modules.Socialfollow.Admin'),
                    ],
                    [
                        'type' => 'text',
                        'lang' => true,
                        'label' => $this->trans('Pinterest URL:', [], 'Modules.Socialfollow.Admin'),
                        'name' => 'BLOCKSOCIAL_PINTEREST',
                        'desc' => $this->trans('Your official Pinterest account.', [], 'Modules.Socialfollow.Admin'),
                    ],
                    [
                        'type' => 'text',
                        'lang' => true,
                        'label' => $this->trans('Vimeo URL:', [], 'Modules.Socialfollow.Admin'),
                        'name' => 'BLOCKSOCIAL_VIMEO',
                        'desc' => $this->trans('Your official Vimeo account.', [], 'Modules.Socialfollow.Admin'),
                    ],
                    [
                        'type' => 'text',
                        'lang' => true,
                        'label' => $this->trans('Instagram URL:', [], 'Modules.Socialfollow.Admin'),
                        'name' => 'BLOCKSOCIAL_INSTAGRAM',
                        'desc' => $this->trans('Your official Instagram account.', [], 'Modules.Socialfollow.Admin'),
                    ],
                    [
                        'type' => 'text',
                        'lang' => true,
                        'label' => $this->trans('LinkedIn URL:', [], 'Modules.Socialfollow.Admin'),
                        'name' => 'BLOCKSOCIAL_LINKEDIN',
                        'desc' => $this->trans('Your official LinkedIn account.', [], 'Modules.Socialfollow.Admin'),
                    ],
                    [
                        'type' => 'text',
                        'lang' => true,
                        'label' => $this->trans('TikTok URL:', [], 'Modules.Socialfollow.Admin'),
                        'name' => 'BLOCKSOCIAL_TIKTOK',
                        'desc' => $this->trans('Your official TikTok account.', [], 'Modules.Socialfollow.Admin'),
                    ],
                    [
                        'type' => 'text',
                        'lang' => true,
                        'label' => $this->trans('Discord URL:', [], 'Modules.Socialfollow.Admin'),
                        'name' => 'BLOCKSOCIAL_DISCORD',
                        'desc' => $this->trans('Your official Discord account.', [], 'Modules.Socialfollow.Admin'),
                    ],
                ],
                'submit' => [
                    'title' => $this->trans('Save', [], 'Admin.Global'),
                ],
            ],
        ];

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->submit_action = 'submitModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', true, [], ['configure' => $this->name]);
        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFieldsValues(),
        ];
        $helper->languages = $this->context->controller->getLanguages();
        $helper->default_form_language = (int) $this->context->language->id;

        return $helper->generateForm([$fields_form]);
    }

    public function getConfigFieldsValues()
    {
        $result = [];
        foreach (static::SOCIAL_NETWORKS as $social) {
            $configuration_name = "BLOCKSOCIAL_$social";
            if (!empty(Configuration::get($configuration_name))) {
                $this->upgradeConfiguration($configuration_name);
            }
            foreach (Language::getIDs() as $id_lang) {
                $conf = Configuration::get($configuration_name, $id_lang);
                $result[$configuration_name][$id_lang] = $conf;
            }
        }

        return $result;
    }

    /**
     * This upgrades the configuration of the module from simple values to localized values. This assures that the
     * upgrade of the module keeps the old configurations, and that the change is transparent to the user.
     *
     * This function is only run once during upgrade, i.e. the first time the user accesses the configuration in the BO
     * after an upgrade of the module to the localized version.
     *
     * @param string $name Name of the configuration setting
     *
     * @return array Configuration value, now localized
     */
    protected function upgradeConfiguration($name)
    {
        /** @var string|array $value */
        $value = Configuration::get($name);
        if (!empty($value) && !is_array($value)) {
            $value_localized = [];
            foreach (Language::getIDs() as $id_lang) {
                $value_localized[$id_lang] = $value;
            }
            Configuration::updateValue($name, $value_localized);
            $value = $value_localized;
        }

        return $value;
    }

    public function renderWidget($hookName = null, array $configuration = [])
    {
        if (!$this->isCached($this->templateFile, $this->getCacheId('ps_socialfollow'))) {
            $this->smarty->assign($this->getWidgetVariables($hookName, $configuration));
        }

        return $this->fetch($this->templateFile, $this->getCacheId('ps_socialfollow'));
    }

    public function getWidgetVariables($hookName = null, array $configuration = [])
    {
        $social_links = [];
        $id_lang = (int) $this->context->language->id;

        if ($sf_facebook = Configuration::get('BLOCKSOCIAL_FACEBOOK', $id_lang)) {
            $social_links['facebook'] = [
                'label' => $this->trans('Facebook', [], 'Modules.Socialfollow.Shop'),
                'class' => 'facebook',
                'url' => $sf_facebook,
            ];
        }

        if ($sf_twitter = Configuration::get('BLOCKSOCIAL_TWITTER', $id_lang)) {
            $social_links['twitter'] = [
                'label' => $this->trans('Twitter', [], 'Modules.Socialfollow.Shop'),
                'class' => 'twitter',
                'url' => $sf_twitter,
            ];
        }

        if ($sf_rss = Configuration::get('BLOCKSOCIAL_RSS', $id_lang)) {
            $social_links['rss'] = [
                'label' => $this->trans('Rss', [], 'Modules.Socialfollow.Shop'),
                'class' => 'rss',
                'url' => $sf_rss,
            ];
        }

        if ($sf_youtube = Configuration::get('BLOCKSOCIAL_YOUTUBE', $id_lang)) {
            $social_links['youtube'] = [
                'label' => $this->trans('YouTube', [], 'Modules.Socialfollow.Shop'),
                'class' => 'youtube',
                'url' => $sf_youtube,
            ];
        }

        if ($sf_pinterest = Configuration::get('BLOCKSOCIAL_PINTEREST', $id_lang)) {
            $social_links['pinterest'] = [
                'label' => $this->trans('Pinterest', [], 'Modules.Socialfollow.Shop'),
                'class' => 'pinterest',
                'url' => $sf_pinterest,
            ];
        }

        if ($sf_vimeo = Configuration::get('BLOCKSOCIAL_VIMEO', $id_lang)) {
            $social_links['vimeo'] = [
                'label' => $this->trans('Vimeo', [], 'Modules.Socialfollow.Shop'),
                'class' => 'vimeo',
                'url' => $sf_vimeo,
            ];
        }

        if ($sf_instagram = Configuration::get('BLOCKSOCIAL_INSTAGRAM', $id_lang)) {
            $social_links['instagram'] = [
                'label' => $this->trans('Instagram', [], 'Modules.Socialfollow.Shop'),
                'class' => 'instagram',
                'url' => $sf_instagram,
            ];
        }

        if ($sf_linkedin = Configuration::get('BLOCKSOCIAL_LINKEDIN', $id_lang)) {
            $social_links['linkedin'] = [
                'label' => $this->trans('LinkedIn', [], 'Modules.Socialfollow.Shop'),
                'class' => 'linkedin',
                'url' => $sf_linkedin,
            ];
        }

        if ($sf_tiktok = Configuration::get('BLOCKSOCIAL_TIKTOK', $id_lang)) {
            $social_links['tiktok'] = [
                'label' => $this->trans('TikTok', [], 'Modules.Socialfollow.Shop'),
                'class' => 'tiktok',
                'url' => $sf_tiktok,
            ];
        }

        if ($sf_discord = Configuration::get('BLOCKSOCIAL_DISCORD', $id_lang)) {
            $social_links['discord'] = [
                'label' => $this->trans('Discord', [], 'Modules.Socialfollow.Shop'),
                'class' => 'ps-socialfollow-discord',
                'url' => $sf_discord,
            ];
        }

        return [
            'social_links' => $social_links,
        ];
    }

    public function hookActionFrontControllerSetMedia()
    {
        $this->context->controller->registerStylesheet(
            'ps_socialfollow_icons',
            '/modules/' . $this->name . '/views/css/ps_socialfollow.css'
        );
    }

    /**
     * Update form fields.
     * Check all social networks form value and verify the URL is valid.
     * Do nothing if a violation is spotted.
     *
     * @return array|bool true on success, errors on failure
     */
    protected function updateFields()
    {
        $validator = Validation::createValidator();
        $constraints = [new Url()];
        $values = [];
        $errors = [];
        foreach (static::SOCIAL_NETWORKS as $social) {
            foreach (Language::getIDs() as $id_lang) {
                $values[$social][$id_lang] = trim(Tools::getValue("BLOCKSOCIAL_{$social}_{$id_lang}", ''));
                $violations = $validator->validate($values[$social][$id_lang], $constraints);

                if (count($violations)) {
                    $errors[] = $this->trans('Invalid URL', [], 'Admin.Notifications.Error') . ': ' . $values[$social][$id_lang];
                }
            }
        }

        if (empty($errors)) {
            foreach (static::SOCIAL_NETWORKS as $social) {
                Configuration::updateValue("BLOCKSOCIAL_$social", $values[$social]);
            }

            return true;
        }

        return $errors;
    }
}
