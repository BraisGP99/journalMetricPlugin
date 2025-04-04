<section class="pkp_block block_metrics">
			
				<div class="metrics_cube">
             <span></span>
					<p>{translate key="plugin.block.journalMetrics.views"} </p>
        </div>
        <div class="metrics_cube">
             <span></span>
					<p>{translate key="plugin.block.journalMetrics.downloads"} </p>
        </div>
          </div> 
        <div class="metrics_total">
             <span></span>
					<p>{translate key="plugin.block.journalMetrics.total"} </p>
  </div>

</section>

<script defer type="text/javascript"> 
    const metricData = JSON.parse($aggregatedMetrics)


    let span = document.querySelectorAll('span')
    span[0].innerText = metricData.views
    span[1].innerText = metricData.downloads
    span[2].innerText = metricData.total

</script> 
