{**
 * templates/settings.tpl
 *
 * Copyright (c) 2014-2023 Simon Fraser University
 * Copyright (c) 2003-2023 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * Settings form for the journalMetrics plugin.
 *}
<script>
	$(function() {ldelim}
		$('#journalMetricsSettings').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});
</script>

<form
	class="pkp_form"
	id="journalMetricsSettings"
	method="POST"
	action="{url router=$smarty.const.ROUTE_COMPONENT op="manage" category="blocks" plugin=$pluginName verb="settings" save=true}"
>
	<!-- Always add the csrf token to secure your form -->
	{csrf}

	{fbvFormSection label="plugins.block.journalMetrics.colorBackground.description"}
		{fbvElement
			type="text"
			id="colorBackground"
			value=$colorBackground
			description="plugins.block.journalMetrics.colorBackground.description"
		}
	{/fbvFormSection}
	{fbvFormSection label="plugins.block.journalMetrics.colorText.description"}
		{fbvElement
			type="text"
			id="colorText"
			value=$colorText
			description="plugins.block.journalMetrics.colorText.description"
		}
	{/fbvFormSection}
	{fbvFormSection label="plugins.block.journalMetrics.showTotal.description" list="true"}
		{fbvElement 
    		type="checkbox" 
    		id="showTotal"
    		checked=$showTotal 
    		label="plugins.block.journalMetrics.showTotal.description"
		}
	{/fbvFormSection}
	{fbvFormButtons submitText="common.save"}
</form>