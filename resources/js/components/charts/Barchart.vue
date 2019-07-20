<template>
	<div class="row">
		<div class="col-xl-6 col-lg-6">
	              <div class="card shadow mb-4">
	                <!-- Card Header - Dropdown -->
	                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
	                  <h6 class="m-0 font-weight-bold text-primary">Mensual</h6>
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
	                	
	                	<div v-if="varMesIngreso.length <= 0" class="d-flex justify-content-center" >
	                        <div class="spinner-grow " role="status" >
	                            <span class="sr-only">Loading...</span>
	                        </div>
                      	</div>
                      
						<div class="ct-chart">
							<canvas id="ventas">
								
							</canvas>
						</div>
					</div>
	        	 </div>
	    </div>

	    <div class="col-xl-6 col-lg-6">
	              <div class="card shadow mb-4">
	                <!-- Card Header - Dropdown -->
	                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
	                  <h6 class="m-0 font-weight-bold text-primary">Este mes</h6>
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

	                	<div v-if="varMesMarca.length <= 0" class="d-flex justify-content-center flex-wrap" >
	                        <div class="spinner-grow " role="status" >
	                            <span class="sr-only">Loading...</span>
	                        </div>
                      	</div>

						<div class="ct-chart">
							<canvas id="marcas">
								
							</canvas>
						</div>
					</div>
	        	 </div>
	    </div>

	    <!-- CATEGORIA -->

	    <div class="col-xl-6 col-lg-6">
	              <div class="card shadow mb-4">
	                <!-- Card Header - Dropdown -->
	                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
	                  <h6 class="m-0 font-weight-bold text-primary">Este mes</h6>
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

	                	<div v-if="varMesMarca.length <= 0" class="d-flex justify-content-center flex-wrap" >
	                        <div class="spinner-grow " role="status" >
	                            <span class="sr-only">Loading...</span>
	                        </div>
                      	</div>

						<div class="ct-chart">
							<canvas id="categorias">
								
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
				categorias: [],
				varTotalMarca: [],
				varVendidoMarca: [],
				varMesMarca: [],
				varTotalCategoria: [],
				varMesCategoria: [],
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
					
					//cargamos los datos a las variables
					me.ingresos = response.data.ingresos;

					const marcaArray = Object.keys(response.data.marcas).map(i => response.data.marcas[i])
					me.marcas = marcaArray
					const categoriaArray = Object.keys(response.data.categorias).map(i => response.data.categorias[i])
					me.categorias = categoriaArray
					//cargamos los datos del chart
					me.loadVentas();
					me.loadMarcas();
					me.loadCategorias();
				})
				.catch(function (error) {
					console.log(error);
				});
			},
			loadVentas(){
				// Area Chart Example
				let me = this;

				me.ingresos.map(function(x){
					me.varMesIngreso.push(x.MES);
					me.varTotalIngreso.push(x.TOTAL);
				});

				me.varIngreso = document.getElementById('ventas').getContext('2d');
			
				me.charIngreso = new Chart(me.varIngreso, {
				  type: 'line',
				  data: {
				    labels: me.varMesIngreso,
				    datasets: [{
				      label: "Total",
				      lineTension: 0.3,
				      backgroundColor: "rgba(78, 115, 223, 0.05)",
				      borderColor: "rgba(78, 115, 223, 1)",
				      pointRadius: 3,
				      pointBackgroundColor: "rgba(78, 115, 223, 1)",
				      pointBorderColor: "rgba(78, 115, 223, 1)",
				      pointHoverRadius: 3,
				      pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
				      pointHoverBorderColor: "rgba(78, 115, 223, 1)",
				      pointHitRadius: 10,
				      pointBorderWidth: 2,
				      data: me.varTotalIngreso,
				    }],
				  },
				  options: {
				    maintainAspectRatio: true,
				    layout: {
				      padding: {
				        left: 10,
				        right: 25,
				        top: 25,
				        bottom: 0
				      }
				    },
				    scales: {
				      xAxes: [{
				        time: {
				          unit: 'date'
				        },
				        gridLines: {
				          display: false,
				          drawBorder: false
				        },
				        ticks: {
				          maxTicksLimit: 7
				        }
				      }],
				      yAxes: [{
				        ticks: {
				          maxTicksLimit: 5,
				          padding: 10,
				          // Include a dollar sign in the ticks
				          callback: function(value, index, values) {
				            return 'Gs. ' + me.number_format(value);
				          }
				        },
				        gridLines: {
				          color: "rgb(234, 236, 244)",
				          zeroLineColor: "rgb(234, 236, 244)",
				          drawBorder: false,
				          borderDash: [2],
				          zeroLineBorderDash: [2]
				        }
				      }],
				    },
				    legend: {
				      display: false
				    },
				    tooltips: {
				      backgroundColor: "rgb(255,255,255)",
				      bodyFontColor: "#858796",
				      titleMarginBottom: 10,
				      titleFontColor: '#6e707e',
				      titleFontSize: 14,
				      borderColor: '#dddfeb',
				      borderWidth: 1,
				      xPadding: 15,
				      yPadding: 15,
				      displayColors: false,
				      intersect: false,
				      mode: 'index',
				      caretPadding: 10,
				      callbacks: {
				        label: function(tooltipItem, chart) {
				          var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
				          return datasetLabel + ': Gs. ' + me.number_format(tooltipItem.yLabel);
				        }
				      }
				    }
				  }
				});
			}, 
			number_format(number, decimals, dec_point, thousands_sep) {
				  // *     example: number_format(1234.56, 2, ',', ' ');
				  // *     return: '1 234,56'
				  number = (number + '').replace(',', '').replace(' ', '');
				  var n = !isFinite(+number) ? 0 : +number,
				    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
				    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
				    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
				    s = '',
				    toFixedFix = function(n, prec) {
				      var k = Math.pow(10, prec);
				      return '' + Math.round(n * k) / k;
				    };
				  // Fix for IE parseFloat(0.55).toFixed(0) = 0;
				  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
				  if (s[0].length > 3) {
				    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
				  }
				  if ((s[1] || '').length < prec) {
				    s[1] = s[1] || '';
				    s[1] += new Array(prec - s[1].length + 1).join('0');
				  }
				  return s.join(dec);
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
					me.varTotalMarca.push(x.TOTAL);
					me.varVendidoMarca.push(x.VENDIDO);
				});

				me.varMarca = document.getElementById('marcas').getContext('2d');

				 me.charMarca = new Chart(me.varMarca, {
				    type: 'bar',
				    data: {
				        labels: me.varMesMarca,
				        datasets: [{
				            label: 'Marcas',
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
				                    beginAtZero: false,
				                    callback: function(value, index, values) {
							          return value.toLocaleString();
							        }
				                }
				            }]
				        }
				    }
				});
			},
			loadCategorias(){
				let me = this;
				me.categorias.map(function(x){
					me.varMesCategoria.push(x.LINEA);
					me.varTotalCategoria.push(x.TOTAL);
				});

				
				me.varMarca = document.getElementById('categorias').getContext('2d');

				 me.charMarca = new Chart(me.varMarca, {
				    type: 'bar',
				    data: {
				        labels: me.varMesCategoria,
				        datasets: [{
				            label: 'categorias',
				            data: me.varTotalCategoria,
				            backgroundColor: 'rgba(255, 206, 86, 0.2)',
				            borderColor: 'rgba(255, 206, 86, 1)',
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

<style>

</style>