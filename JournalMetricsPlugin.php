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

namespace APP\plugins\generic\journalMetrics;


use PKP\plugins\GenericPlugin;
use APP\core\Application;
use APP\core\Services;
use APP\facades\Repo;
use PKP\plugins\Hook;
use PKP\template\TemplateManager;



class JournalMetricsPlugin extends GenericPlugin
{
    /** @copydoc GenericPlugin::register() */
    public function register($category, $path, $mainContextId = null): bool
    {
        $success = parent::register($category, $path);

        if ($success && $this->getEnabled()) {

            $this->addLocaleData();

            Hook::add('TemplateManager::display',[$this,'readyData']);
            Hook::add('TemplateManager::display',[$this,'putData']);
        }

        return $success;
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

    /**
     *
     *
     * @param Request $request
     * @param $args
     */
   public function readyData($hookName, $args)
{
    $templateMgr = $args[0];
    $request = Application::get()->getRequest();
    $router = $request->getRouter();
    $requestedArgs = $router->getRequestedArgs($request);

    $journalId = $requestedArgs[0] ?? null;
    error_log("Requested journalId: " . $journalId);  

    $journal = is_numeric($journalId) ? Repo::submission()->get(intval($journalId)) ?? null : null;
    
    error_log("Journal: " . print_r($journal, true));  

    if ($journal !== null) {
        $templateMgr->assign('aggregatedMetrics', $this->getAggregatedMetrics($journalId));
        $metricsHtml = $templateMgr->fetch($this->getTemplateResource('journalMetrics.tpl'));
        $templateMgr->assign('journalMetricsHtml', $metricsHtml);
    }
    return false; 
}


    private function getAggregatedMetrics($journalId){
        $request = Application::get()->getRequest();
        $statsServices = Services::get('publicationStats');
        // $statsServices = app()->get('publicationStats');

        $metricsByType = $statsServices->getTotalsByType($journalId,$request->getContext()->getId(),null,null);
       
        $metricsAggregated = [
			'views' =>  $metricsByType['abstract'],
			'downloads' => 0
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

   
   public function putData($hookName, $args)
{
    $smarty =& $args[0];
    $output =& $args[2];
    $request = Application::get()->getRequest();
    $router = $request->getRouter();
    $requestedArgs = $router->getRequestedArgs($request);

    $journalId = $requestedArgs[0] ?? null;
    error_log("Requested journalId (putData): " . $journalId);

    $journal = is_numeric($journalId) ? Repo::submission()->get(intval($journalId)) ?? null : null;
    error_log("Journal (putData): " . print_r($journal, true));

    if ($journal !== null) {
        $metricsAggregated = $this->getAggregatedMetrics($journalId);
        error_log("Aggregated Metrics: " . print_r($metricsAggregated, true));

        $smarty->assign('metricsAggregated', $metricsAggregated);
        $metricsHtml = $smarty->fetch($this->getTemplateResource('journalMetrics.tpl'));
        $output .= $metricsHtml;
    }
}

}
