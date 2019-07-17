<template>
		<!-- VENTA POR MARCA Y CATEGORIA -->
	<div>
		<div class="card shadow border-bottom-primary" >
		  	<div class="card-header">Venta por marcas y categorías</div>
			<div class="card-body">
			  	<div class="form-row">
			  		<div class="col-md-4 mb-3">
			  			
			  			<label for="validationTooltip01">Seleccione Sucursal</label>
						<select class="custom-select custom-select-sm" v-bind:class="{ 'is-invalid': validarSucursal }" v-model="selectedSucursal">
							 <option value="null" selected>Seleccionar</option>
							 <option v-for="sucursal in sucursales" :value="sucursal.CODIGO">{{ sucursal.DESCRIPCION }}</option>
						</select>
						<div class="invalid-feedback">
					        {{messageInvalidSucursal}}
					    </div>
					  	<label class="mt-3" for="validationTooltip01">Seleccione Intervalo de Tiempo</label>
						<div id="sandbox-container">
							<div class="input-daterange input-group">
								   <input type="text" class="input-sm form-control form-control-sm" id="selectedInicialFecha" v-model="selectedInicialFecha" v-bind:class="{ 'is-invalid': validarInicialFecha }"/>
								   <div class="input-group-append form-control-sm">
								   		<span class="input-group-text">a</span>
								   </div>
								   <input type="text" class="input-sm form-control form-control-sm" name="end" id="selectedFinalFecha" v-model="selectedFinalFecha" v-bind:class="{ 'is-invalid': validarFinalFecha }"/>
							</div>
							<div class="invalid-feedback">
					        	{{messageInvalidFecha}}
					    	</div>
						</div>					  

					</div>

					<div class="col-md-4">
						<label for="validationTooltip01">Seleccione Marcas</label> 
						<select multiple class="form-control" size="4" v-model="selectedMarca" :disabled="onMarca" v-bind:class="{ 'is-invalid': validarMarca }">
						   <option v-for="marca in marcas" :value="marca.CODIGO">{{ marca.DESCRIPCION }}</option>
						</select>
						<div class="invalid-feedback">
					        {{messageInvalidMarca}}
					    </div>
						<div class="custom-control custom-switch mt-3">
						  <input type="checkbox" class="custom-control-input" id="customSwitch1" v-on:click="todasMarcas">
						  <label class="custom-control-label" for="customSwitch1" >Seleccionar todas las Marcas</label>
						</div>
					</div>

					<div class="col-md-4">
						<label for="validationTooltip01">Seleccione Categoria</label> 
						<select multiple class="form-control" size="5" v-model="selectedCategoria" :disabled="onCategoria" v-bind:class="{ 'is-invalid': validarCategoria }">
						  <option v-for="categoria in categorias" :value="categoria.CODIGO">{{ categoria.DESCRIPCION }}</option>
						</select>
						<div class="invalid-feedback">
					        {{messageInvalidCategoria}}
					    </div>
						<div class="custom-control custom-switch mt-3">
						  <input type="checkbox" class="custom-control-input" id="customSwitch2" v-on:click="todasCategorias">
						  <label class="custom-control-label" for="customSwitch2">Seleccionar todas las Categorias</label>
						</div>
					</div>

				</div>
				<button class="btn btn-dark btn-sm" type="submit"><font-awesome-icon icon="download" /> Descargar</button>
			    <button class="btn btn-primary btn-sm" type="submit" v-on:click="llamarDatos">Generar</button>
			</div>
		</div>


		<!-- CARD PARA MARCA Y SU CATEGORIA -->

		<div class="row">

			<!-- SPINNER -->

			<div class="col-md-12">
				<div v-if="cargado" class="d-flex justify-content-center mt-3">
					<strong>Cargando...   </strong>
	                <div class="spinner-grow" role="status" aria-hidden="true"></div>
	             </div>
            </div>
            
            <!-- CHART MARCAS -->

            <div class="col-xl-6 col-lg-6">
	                <div class="card-body">
						<div class="ct-chart">
							<canvas id="marcas">
								
							</canvas>
						</div>
					</div>
	    	</div>
	     	
	     	<!-- CARD PARA MARCA Y SU CATEGORIA -->

			<div class="card border-left-primary mt-3 col-md-12" v-for="marca in responseMarca">
				<div class="row">
					
					<div class="col-md-6">
						  <div class="card-header font-weight-bold text-primary">
						    {{marca.MARCA}}
						  </div>
				    </div>
				    <div class="col-md-6">
						  <div class="card-header font-weight-bold text-primary text-right">
						    {{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(marca.TOTAL)}}
						  </div>
				    </div>
				</div>  
				
				<ul class="list-group list-group-flush">
				    <li class="list-group-item" v-for="marca in filterItems(responseCategoria, marca.CODIGO)">
				    	<div class="row">
				    		<div class="col-md-6">
				    			{{marca.LINEA}}
				    		</div>
				    		<div class="col-md-6 text-right">
				    			{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(marca.TOTAL)}}
				    		</div>
				    	</div>
					</li>
				</ul>
			</div>

		</div>

		<!-- CARD PARA MARCA Y SU CATEGORIA -->

	</div>
		<!-- FIN DE VENTA POR MARCA Y CATEGORIA -->


</template>

<script >
	export default {
        data(){
            return {
              	sucursales: [],
              	selectedSucursal: '',
              	marcas: [],
              	selectedMarca: [],
              	categorias: [],
              	selectedCategoria: [],
              	onMarca: false,
              	onCategoria: false,
              	validarSucursal: false,
              	messageInvalidSucursal: '',
              	validarMarca: false,
              	messageInvalidMarca: '',
              	validarCategoria: false,
              	messageInvalidCategoria: '',
              	selectedInicialFecha: '',
              	validarInicialFecha: false,
              	messageInvalidFecha: '',
              	selectedFinalFecha: '',
              	validarFinalFecha: false,
              	datos: {},
              	responseMarca: {},
              	responseCategoria: [],
              	varTotalMarca: [],
				varNombreMarca: [],
              	cargado: false
            }
        }, 
        methods: {
            llamarBusquedas(){	
	          axios.get('busquedas/').then((response) => {
	           	this.sucursales = response.data.sucursales;
	           	this.marcas = response.data.marcas;
	           	this.categorias = response.data.categorias;
	          }); 
	        },
	        filterItems: function(items, codigo) {
			      return items.filter(function(item) {
			      return item.MARCA === codigo;
			    })
			 },
	        todasMarcas(e){
	        	this.onMarca = !this.onMarca;
	        },
	        todasCategorias(e){
	        	this.onCategoria = !this.onCategoria;
	        },
	        llamarDatos(){
	        	let me = this;	
	        	if(this.generarConsulta() === true) {
	        		me.cargado = true;
					axios.post('/ventas', this.datos).then(function (response) {
						me.cargado = false;
					    const marcaArray = Object.keys(response.data.marcas).map(i => response.data.marcas[i])
					    me.responseMarca = marcaArray
					    const categoriaArray = Object.keys(response.data.categorias).map(i => response.data.categorias[i])
					    me.responseCategoria = categoriaArray
					    me.loadMarcas();
					});
	        	} else {
	        		alert("false");
	        	}
	        },
	        generarConsulta(){
	        	this.selectedInicialFecha = $('#selectedInicialFecha').val();
	        	this.selectedFinalFecha = $('#selectedFinalFecha').val();
	        	
	        	if (this.selectedSucursal === null || this.selectedSucursal === "null") {
	        		this.validarSucursal = true;
	        		this.messageInvalidSucursal = 'Por favor seleccione sucursal';
	        		return false;
	        	} else {
	        		this.validarSucursal = false;
	        		this.messageInvalidSucursal = '';
	        	}	

	        	if(this.onMarca === false && this.selectedMarca === null) {
	        		this.validarMarca = true;
	        		this.messageInvalidMarca = 'Por favor seleccione una o varias Marcas';
	        		return false;
	        	} else {
	        		this.validarMarca = false;
	        		this.messageInvalidMarca = '';
	        	}

	        	if(this.onCategoria === false && this.selectedCategoria === null) {
	        		this.validarCategoria = true;
	        		this.messageInvalidCategoria = 'Por favor seleccione una o varias Categorias';
	        		return false;
	        	} else {
	        		this.validarCategoria = false;
	        		this.messageInvalidCategoria = '';
	        	}

	        	if(this.selectedInicialFecha === null || this.selectedInicialFecha === "") {
	        		this.validarInicialFecha = true;
	        		this.messageInvalidFecha = 'Por favor seleccione una fecha Inicial';
	        		return false;
	        	} else {
	        		this.validarInicialFecha = false;
	        		this.messageInvalidFecha = '';
	        	}

	        	if(this.selectedFinalFecha === null || this.selectedFinalFecha === "") {
	        		this.validarFinalFecha = true;
	        		this.messageInvalidFecha = 'Por favor seleccione una fecha Final';
	        		return false;
	        	} else {
	        		this.validarFinalFecha = false;
	        		this.messageInvalidFecha = '';
	        	}		

	        	if(this.onMarca === true) {
	        		for (var key in this.marcas){
	        			this.selectedMarca[key] = this.marcas[key].CODIGO;
	        		}
	        	} 

	        	if(this.onCategoria === true) {
	        		for (var key in this.categorias){
	        			this.selectedCategoria[key] = this.categorias[key].CODIGO;
	        		}
	        	}

	        	this.datos = {
	        	Sucursal: this.selectedSucursal,
	        	Inicio: String(this.selectedInicialFecha),
	        	Final: String(this.selectedFinalFecha),
	        	Marcas: this.selectedMarca,
	        	Categorias: this.selectedCategoria };
	        	
	        	return true;
	        },
	        loadMarcas(){
				let me = this;
				me.varNombreMarca = [];
				me.varTotalMarca = [];
				me.responseMarca.map(function(x){
					me.varNombreMarca.push(x.MARCA);
					me.varTotalMarca.push(x.TOTAL);
				});

				me.varMarca = document.getElementById('marcas').getContext('2d');

				 me.charMarca = new Chart(me.varMarca, {
				    type: 'bar',
				    data: {
				        labels: me.varNombreMarca,
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
        mounted() {
        	$(function(){
		   		    $('#sandbox-container .input-daterange').datepicker({
		   		    	    keyboardNavigation: false,
    						forceParse: false
    				});
			});
			this.llamarBusquedas();
        }
    }    
</script>
