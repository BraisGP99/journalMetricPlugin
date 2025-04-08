<?php
/**
 * @file PluginTemplatePlugin.php
 *
 * Copyright (c) 2017-2023 Simon Fraser University
 * Copyright (c) 2017-2023 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * 
 * @class PluginTemplatePlugin
 * @brief Plugin class for the PluginTemplate plugin.
 */

namespace APP\plugins\blocks\journalMetrics;


use PKP\plugins\BlockPlugin;
use APP\core\Application;
use APP\core\Services;
use PKP\core\JSONMessage;
use APP\plugins\blocks\journalMetrics\classes\Settings\Actions;
use APP\plugins\blocks\journalMetrics\classes\Settings\Manage;


class JournalMetricsPlugin extends BlockPlugin
{
    
 /**
     * Install default settings on journal creation.
     *
     * @return string
     */
    public function getContextSpecificPluginSettingsFile()
    {
        return $this->getPluginPath() . '/settings.xml';
    }

    /**
     * Provide a name for this plugin
     *
     * The name will appear in the Plugin Gallery where editors can
     * install, enable and disable plugins.
     */
    public function getDisplayName(): string
    {
        return __('plugins.block.journalMetrics.displayName');
    }

    /**
     * Provide a description for this plugin
     *
     * The description will appear in the Plugin Gallery where editors can
     * install, enable and disable plugins.
     */
    public function getDescription(): string
    {
        return __('plugins.block.journalMetrics.description');
    }


    public function getContents($templateMgr, $request = null){
    
    $request = Application::get()->getRequest();
    $journal = $request->getJournal();
    $journalId= $journal->getId();
    $showTotal = $this->getSetting($request->getContext()->getId(), 'showTotal');
    $colorBackground = $this->getSetting($request->getContext()->getId(), 'colorBackground');
    $colorText = $this->getSetting($request->getContext()->getId(), 'colorText');
    $templateMgr->assign([
        "showTotal" => $showTotal,
        "colorBackground" => $colorBackground,
        "colorText" => $colorText,
    ]);


    if ($journal !== null) {
        $templateMgr->assign('aggregatedMetrics', $this->getAggregatedMetrics($journalId));
        $cssUrl = $request->getBaseUrl().'/'.$this->getPluginPath().'/css/styles.css';
        $templateMgr->addStyleSheet('journalMetrics', $cssUrl,["contexts" => "frontend","backend"]);
        
    }
    return parent::getContents($templateMgr,$request); 
}


    private function getAggregatedMetrics($journalId){
        $request = Application::get()->getRequest();
        $statsServices = Services::get('publicationStats');
        // $statsServices = app()->get('publicationStats');

        $metricsByType = $statsServices->getTotalsByType($journalId,$request->getContext()->getId(),null,null);
    // Por algún motivo los error logs hacen display en el html en la sección de los bloques? ??? no entiendo
    //    error_log("metrics:" . print_r($metricsByType));
    //    error_log("metrics abstract:" . print_r($metricsByType['abstract']));

        $metricsByType['abstract'] = 77;

       $metricsAggregated = [
            'views' =>  $metricsByType['abstract'],
			'downloads' => 5
		];

		foreach ($metricsByType as $key => $value) {
			if ($key == 'abstract' || $key == 'suppFileViews'){
				continue;
			}
			$metricsAggregated['downloads'] += $value;
		}
		$metricsAggregated['total'] = $metricsAggregated['downloads'] + $metricsAggregated['views'];            

        return $metricsAggregated;
    }

    /**
     * Add a settings action to the plugin's entry in the plugins list.
     *
     * @param Request $request
     * @param array $actionArgs
     */
    public function getActions($request, $actionArgs): array
    {
        $actions = new Actions($this);
        return $actions->execute($request, $actionArgs, parent::getActions($request, $actionArgs));
    }

 /**
     * Load a form when the `settings` button is clicked and
     * save the form when the user saves it.
     *
     * @param array $args
     * @param Request $request
     */
    public function manage($args, $request): JSONMessage
    {
        $manage = new Manage($this);
        return $manage->execute($args, $request);
    }

    public function getSettingsForm($context): \PKP\form\Form {
        import('plugins.blocks.journalMetrics.classes.form.JournalMetricsSettingsForm');
         return new \APP\plugins\blocks\journalMetrics\classes\form\JournalMetricsSettingsForm($this, $context->getId());
}

}