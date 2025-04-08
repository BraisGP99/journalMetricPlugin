<?php

/**
 * @file plugins/blocks/journalMetrics/JournalMetricsPlugin.php
 *
 * Copyright (c) 2014-2024 Simon Fraser University
 * Copyright (c) 2003-2024 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class JournalMetricsSettingsForm
 * @ingroup plugins_blocks_journalMetrics
 *
 * @brief Form for journal managers to modify journal Metrics plugin settings
 */

 namespace APP\plugins\blocks\journalMetrics;

 use APP\template\TemplateManager;
 use PKP\cache\CacheManager;
 use PKP\form\Form;

class JournalMetricsSettingsForm extends Form {

    /** @var PluginTemplatePlugin */
    public JournalMetricsPlugin $plugin;

    /**
     * Defines the settings form's template and adds validation checks.
     *
     * Always add POST and CSRF validation to secure your form.
     */
    public function __construct(PluginTemplatePlugin &$plugin)
    {
        $this->plugin = &$plugin;

        parent::__construct($this->plugin->getTemplateResource(Constants::SETTINGS_TEMPLATE));

        $this->addCheck(new FormValidatorPost($this));
        $this->addCheck(new FormValidatorCSRF($this));
    }

    /**
     * Load settings already saved in the database
     *
     * Settings are stored by context, so that each journal, press,
     * or preprint server can have different settings.
     */
    public function initData(): void
    {
        $context = Application::get()
            ->getRequest()
            ->getContext();

        $this->setData(
            Constants::PUBLICATION_STATEMENT,
            $this->plugin->getSetting(
                $context->getId(),
                Constants::PUBLICATION_STATEMENT
            )
        );

        parent::initData();
    }

    /**
     * Load data that was submitted with the form
     */
    public function readInputData(): void
    {
        $this->readUserVars([Constants::PUBLICATION_STATEMENT,Constants::SHOW_TOTAL,Constants::COLOR_BACKGROUND,Constants::COLOR_TEXT]);

        parent::readInputData();
    }

    /**
     * Fetch any additional data needed for your form.
     *
     * Data assigned to the form using $this->setData() during the initData() or readInputData() methods will be passed to the template.
     *
     * In the example below, the plugin name is passed to the
     * template so that it can be used in the URL that the form is
     * submitted to.
     */
    public function fetch($request, $template = null, $display = false): ?string
    {
        $templateMgr = TemplateManager::getManager($request);
        $templateMgr->assign('pluginName', $this->plugin->getName());

        return parent::fetch($request, $template, $display);
    }

    /**
     * Save the plugin settings and notify the user
     * that the save was successful
     */
    public function execute(...$functionArgs): mixed
    {
        $context = Application::get()
            ->getRequest()
            ->getContext();

        $this->plugin->updateSetting(
            $context->getId(),
            Constants::PUBLICATION_STATEMENT,
            $this->getData(Constants::PUBLICATION_STATEMENT)
        );

        $notificationMgr = new NotificationManager();
        $notificationMgr->createTrivialNotification(
            Application::get()->getRequest()->getUser()->getId(),
            Notification::NOTIFICATION_TYPE_SUCCESS,
            ['contents' => __('common.changesSaved')]
        );

        return parent::execute();
    }
}