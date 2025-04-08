<style>
.block_metrics {
	display: grid;
	grid-template-columns: 1fr 1fr;
	grid-gap: 2px;
	place-items: center;
	max-width: 250px;
  border: rounded;
}
.metrics_cube {
	text-align: center;
	background-color:{$colorBackground};
	color: {$colorText};
	font-size: 2em;
	padding: 24px 12px 12px;
  border: rounded;

}
.metrics_total {
	grid-column: 1/3;
  border: rounded;
	font-size: 2em;
	text-align: center;
	align-self: center;
  background-color:{$colorBackground};
	color: {$colorText};
	padding: 24px 12px 12px;
}

</style>
<div class="pkp_block">
  <h2 class="title">
    Journal Metrics
  </h2>
  <div class="content">
    <p>{$pluginName} TEST</p>
    <section class="block_metrics">
      {if $aggregatedMetrics}
        <div class="metrics_cube">
         <p>{$aggregatedMetrics.views}</p>
         <p>{translate key="plugins.block.journalMetrics.views"} </p>
        </div>
        <div class="metrics_cube">
          <p>{$aggregatedMetrics.downloads}</p>
          <p>{translate key="plugins.block.journalMetrics.downloads"} </p>
        </div>
        <div class="metrics_total">
          <p>{$aggregatedMetrics.total}</p>
          <p>{translate key="plugins.block.journalMetrics.total"} </p> 
        </div>
      {else}
        <p>{translate key="plugins.block.journalMetrics.error"}</p>
      {/if}
    </section>
  </div>
</div>
