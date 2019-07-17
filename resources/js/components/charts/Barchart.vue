<template>
	<div class="row">
		<div class="col-xl-6 col-lg-6">
	              <div class="card shadow mb-4">
	                <!-- Card Header - Dropdown -->
	                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
	                  <h6 class="m-0 font-weight-bold text-primary">Ventas - Vista General (Mensual)</h6>
	                  <div class="dropdown no-arrow">
	                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	                      <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
	                    </a>
	                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
	                      <div class="dropdown-header">Dropdown Header:</div>
	                      <a class="dropdown-item" href="#">Action</a>
	                      <a class="dropdown-item" href="#">Another action</a>
	                      <div class="dropdown-divider"></div>
	                      <a class="dropdown-item" href="#">Something else here</a>
	                    </div>
	                  </div>
	                </div>
	                <!-- Card Body -->
	                <div class="card-body">

						<div class="ct-chart">
							<canvas id="ingresos">
								
							</canvas>
						</div>
					</div>
	        	 </div>
	    </div>

	    <div class="col-xl-6 col-lg-6">
	              <div class="card shadow mb-4">
	                <!-- Card Header - Dropdown -->
	                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
	                  <h6 class="m-0 font-weight-bold text-primary">Ventas Marca - Vista General (Mes)</h6>
	                  <div class="dropdown no-arrow">
	                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	                      <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
	                    </a>
	                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
	                      <div class="dropdown-header">Dropdown Header:</div>
	                      <a class="dropdown-item" href="#">Action</a>
	                      <a class="dropdown-item" href="#">Another action</a>
	                      <div class="dropdown-divider"></div>
	                      <a class="dropdown-item" href="#">Something else here</a>
	                    </div>
	                  </div>
	                </div>
	                <!-- Card Body -->
	                <div class="card-body">

						<div class="ct-chart">
							<canvas id="marcas">
								
							</canvas>
						</div>
					</div>
	        	 </div>
	    </div>

	     <!-- Pie Chart -->
            <div class="col-xl-4 col-lg-4">
              <div class="card shadow mb-4">
                <!-- Card Header - Dropdown -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Revenue Sources</h6>
                  <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                      <div class="dropdown-header">Dropdown Header:</div>
                      <a class="dropdown-item" href="#">Action</a>
                      <a class="dropdown-item" href="#">Another action</a>
                      <div class="dropdown-divider"></div>
                      <a class="dropdown-item" href="#">Something else here</a>
                    </div>
                  </div>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                  <donut></donut>
                  <div class="mt-4 text-center small">
                    <span class="mr-2">
                      <i class="fas fa-circle text-primary"></i> Direct
                    </span>
                    <span class="mr-2">
                      <i class="fas fa-circle text-success"></i> Social
                    </span>
                    <span class="mr-2">
                      <i class="fas fa-circle text-info"></i> Referral
                    </span>
                  </div>
                </div>
              </div>
            </div>
    </div>
</template>
<script >
	export default {
		data(){
			return {
				varMarca: null,
				charMarca: null,
				marcas: [],
				varTotalMarca: [],
				varMesMarca: [],

				varIngreso: null,
				charIngreso: null,
				ingresos: [],
				varTotalIngreso: [],
				varMesIngreso: [],
			}
		},
		methods: {
			getIngresos(){
				let me = this;
				var url = '/charts';
				axios.get(url).then(function (response) {
					var respuesta = response.data;
					//cargamos los datos a las variables
					me.ingresos = respuesta.ingresos;
					me.marcas = respuesta.marcas;
					//cargamos los datos del chart
					me.loadIngresos();
					me.loadMarcas();
				})
				.catch(function (error) {
					console.log(error);
				});
			},
			loadIngresos(){
				let me = this;
				me.ingresos.map(function(x){
					me.varMesIngreso.push(x.MES);
					me.varTotalIngreso.push(x.TOTAL);
				});

				
				me.varIngreso = document.getElementById('ingresos').getContext('2d');

				 me.charIngreso = new Chart(me.varIngreso, {
				    type: 'bar',
				    data: {
				        labels: me.varMesIngreso,
				        datasets: [{
				            label: 'Ventas',
				            data: me.varTotalIngreso,
				            backgroundColor: [
				                'rgba(255, 99, 132, 0.2)',
				                'rgba(54, 162, 235, 0.2)',
				                'rgba(255, 206, 86, 0.2)',
				                'rgba(75, 192, 192, 0.2)',
				                'rgba(153, 102, 255, 0.2)',
				                'rgba(255, 159, 64, 0.2)',
				                'rgba(54, 162, 235, 0.2)',
				                'rgba(255, 206, 86, 0.2)',
				                'rgba(75, 192, 192, 0.2)',
				                'rgba(153, 102, 255, 0.2)'
				            ],
				            borderColor: [
				                'rgba(255, 99, 132, 1)',
				                'rgba(54, 162, 235, 1)',
				                'rgba(255, 206, 86, 1)',
				                'rgba(75, 192, 192, 1)',
				                'rgba(153, 102, 255, 1)',
				                'rgba(255, 159, 64, 1)'
				            ],
				            borderWidth: 1
				        }]
				    },
				    options: {
				    	tooltips: {
				              callbacks: {
				                  label: function(tooltipItem, data) {
				                      var value = data.datasets[0].data[tooltipItem.index];
				                      
				                      return 'Gs. ' + new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(value) + '';
				                  }
				              }
				          },
				        scales: {
				            yAxes: [{
				                ticks: {
				                    beginAtZero: true,
				                    callback: function(value, index, values) {
							          return value.toLocaleString();
							        }
				                }
				            }]
				        }
				    }
				});
			},
			loadMarcas(){
				let me = this;
				me.marcas.map(function(x){
					me.varMesMarca.push(x.MARCA);
					me.varTotalMarca.push(x.PRECIO);
				});

				
				me.varMarca = document.getElementById('marcas').getContext('2d');

				 me.charMarca = new Chart(me.varMarca, {
				    type: 'bar',
				    data: {
				        labels: me.varMesMarca,
				        datasets: [{
				            label: 'Ventas',
				            data: me.varTotalMarca,
				            backgroundColor: 'rgba(75, 192, 192, 0.2)',
				            borderColor: 'rgba(75, 192, 192, 1)',
				            borderWidth: 1
				        }]
				    },
				    options: {
				    	tooltips: {
				              callbacks: {
				                  label: function(tooltipItem, data) {
				                      var value = data.datasets[0].data[tooltipItem.index];
				                      
				                      return 'Gs. ' + new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(value) + '';
				                  }
				              }
				          },
				        scales: {
				            yAxes: [{
				                ticks: {
				                    beginAtZero: true,
				                    callback: function(value, index, values) {
							          return value.toLocaleString();
							        }
				                }
				            }]
				        }
				    }
				});
			}
		},
		mounted(){
			this.getIngresos();
		}
	}
</script>