<section class="pkp_block">
			<div>
				<ul>
					<li></li>
                    <li>{translate key="plugin.block.journalMetrics.views"} </li>
					<li></li>
                    <li>{translate key="plugin.block.journalMetrics.downloads"} </li>
					<li></li>
					<li>{translate key="plugin.block.journalMetrics.total"} </li>
				</ul>
			</div>
</section>

<script defer type="text/javascript"> 
    const metricData = JSON.parse($aggregatedMetrics)

    let div = document.querySelector('div')
    let lis = div.querySelectorAll('li')
    lis[0].innerText = metricData.views
    lis[2].innerText = metricData.downloads
    lis[4].innerText = metricData.total
    


</script> 
