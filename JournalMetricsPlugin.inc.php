<?php
/**
 * @file PluginTemplatePlugin.php
 *
 * Copyright (c) 2017-2023 Simon Fraser University
 * Copyright (c) 2017-2023 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
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

class JournalMetricsPlugin extends GenericPlugin
{
    /** @copydoc GenericPlugin::register() */
    public function register($category, $path, $mainContextId = null): bool
    {
        $success = parent::register($category, $path);

        if ($success && $this->getEnabled()) {
           
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
        return __('plugins.generic.journalMetrics.description');
    }

    /**
     * Add a settings action to the plugin's entry in the plugins list.
     *
     * @param Request $request
     * @param array $actionArgs
     */
    public function readyData($hookName, $args)
    {
        $templateManager = TemplateManager::getManager($request);
        $request = Applications::get()->getRequest();
        $router = $request->getRouter();
        $journalId = $router->getRequestedArgs($request)[0] ?? null;

        if($journalId !== null){
            $templateManager->assign('aggregatedMetrics',$this->getAggregatedMetrics($journalId));
            $metricsHtml = $templateMgr->fetch($this->getTemplateResource('journalMetrics.tpl'));
            $templateManager->assign('journalMetricsHtml',$metricsHtml);
        }
    }

    /**
 * @file classes/core/PKPServices.php
 *
 * Copyright (c) 2014-2024 Simon Fraser University
 * Copyright (c) 2000-2024 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class PKPServices
 * 
 * @brief Pimple Dependency Injection Container.
 * OLLO non sei se está deprecated na 3.5
 * @deprecate 3.5.0 Consider using {@see app()->get('SERVICE_NAME')}
 * @see app()->get('SERVICE_NAME')
 * 
 */
    
    private function getAggregatedMetrics($journalId){
        $request = Application::get()->getRequest();
        $statsServices = Services::get('publicationStats');
        // $statsServices = app()->get('publicationStats');


     /*
        $args = [
            'submissionIds' => [$submissionId],
            'contextIds' => [$contextId],
            'dateStart' => $dateStart ?? $defaultArgs['dateStart'],
            'dateEnd' => $dateEnd ?? $defaultArgs['dateEnd'],
            'assocTypes' => [Application::ASSOC_TYPE_SUBMISSION, Application::ASSOC_TYPE_SUBMISSION_FILE, Application::ASSOC_TYPE_SUBMISSION_FILE_COUNTER_OTHER]
        ];

        Devolve un array asociativo coa seguinte estrutura:

        return [
            'abstract' => $abstractViews,
            'pdf' => $pdfViews,
            'html' => $htmlViews,
            'other' => $otherViews,
            'suppFileViews' => $suppFileViews
        ];
*/
        $metricsByType = $statsServices->getTotalsByType($journalId,$request->getContext()->getId());

        /*

-> w3schools

         <?php
function myfunction($v)
{
  return($v*$v);
}

$a=array(1,2,3,4,5);
print_r(array_map("myfunction",$a));
?> 

-> https://www.php.net/manual/en/function.array-reduce.php

<?php

    // Attribute List
    $attribs = [
        'name' => 'first_name',
        'value' => 'Edward'
    ];

    // Attribute string formatted for use inside HTML element
    $formatted_attribs = array_reduce(
        array_keys($attribs),                       // We pass in the array_keys instead of the array here
        function ($carry, $key) use ($attribs) {    // ... then we 'use' the actual array here
            return $carry . ' ' . $key . '="' . htmlspecialchars( $attribs[$key] ) . '"';
        },
        ''
    );

echo $formatted_attribs;

?>

en cada iteración metric é un array asociativo composto polos tipos de métricas, no reduce recorremos todos menos os abstract para determinar cantas métricas de acceso a pdf, html, epub e outros tivo AKA descargas
        */ 

        $metricsAggregated  = array_map(function ($metric){
            $views = $metric['abstract'] ?? 0;
            $downloads = array_reduce(array_keys($metric),function ($acc,$curr) use ($metric){
                
                if($curr !== 'abstract'){
                    $acc += $metric[$curr]; 
                }
              return $acc;  
            });
            return[
                'views'=>$views,
                'downloads'=>$downloads,
                'total'=>($views+$downloads)
            ];
        },$metricsByType);

        return json_encode($metricsAggregated);
    }


    public function putData($hookName,$args){
        

        $request = Application::get()->getRequest();
        $router = $request->getRouer();
        $journalId = $router->getRequestedArgs($request)[0];
        $journal = is_numeric($journalId) ? Repo::submission()->get(intval($journalId)) ?? null:null;

        if($journal !== null){
            $templateManager = TemplateManager::getManager($request);
            $templateManager->addStyleSheet('styles-css'.'/'.$this->getPluginPath().'/styles/metrics-css');
        }
        return false;
    }
}

// For backwards compatibility -- expect this to be removed approx. OJS/OMP/OPS 3.6
if (!PKP_STRICT_MODE) {
    class_alias('\APP\plugins\generic\pluginTemplate\PluginTemplatePlugin', '\PluginTemplatePlugin');}