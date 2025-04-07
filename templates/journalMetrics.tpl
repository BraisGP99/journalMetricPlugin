{if $aggregatedMetrics}
<section class="pkp_block block_metrics">
  <p>TEST</p>

		
				<div class="metrics_cube">
             <p>{$aggregatedMetrics.views}</p>
					<p>{translate key="plugin.block.journalMetrics.views"} </p>
        </div>
        <div class="metrics_cube">
             <p>{$aggregatedMetrics.download}</p>
					<p>{translate key="plugin.block.journalMetrics.downloads"} </p>
        </div>
          </div> 
        <div class="metrics_total">
             <p>{$aggregatedMetrics.total}</p>
					<p>{translate key="plugin.block.journalMetrics.total"} </p> 
  </div>
{else}
  <p>{translate key="plugin.block.journalMetrics.error"}</p>
{/if}
</section>
