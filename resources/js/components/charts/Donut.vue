<template>
	<apexchart type="donut" :options="options" :series="series"></apexchart>
</template>

<script>
	export default {
		data(){
    		return {
       options: {
       	chart: {
                type: 'donut',
            },
             
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
       },
       series: [44, 55, 41, 17, 15],
       labels: ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"],
       }
      
    	}
	}
	
</script>