<template>
	<div>

		<div class="card shadow mb-4">
	       <!-- Card Header - Dropdown -->
	       <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
	         <h6 class="m-0 font-weight-bold text-primary">{{title}} <small>- Marca - {{fecha}}</small></h6>
	         <div class="dropdown no-arrow">
	           <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	             <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
	                    </a>
	           <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
	             <div class="dropdown-header">Lapso de tiempo:</div>
	             <a class="dropdown-item" v-on:click="llamarDatos(1)" hidden>Año Actual</a>
	             <a class="dropdown-item" v-on:click="llamarDatos(2)">Mes Actual</a>
	             <div class="dropdown-divider"></div>
	             <a class="dropdown-item" v-on:click="llamarDatos(3)">Mes Pasado</a>
	           </div>
	         </div>
	       </div>
	                <!-- Card Body -->
	       <div class="card-body">
				<div v-if="response.length <= 0" class="d-flex justify-content-center col-auto" >
			          <div class="spinner-grow " role="status" >
			               <span class="sr-only">Loading...</span>
			          </div>
		        </div>
				<table v-else class="table table-sm table-striped">
				  <thead>
				    <tr>
				      <th scope="col">#</th>
				      <th scope="col">Marca</th>
				      <th scope="col">Stock</th>
				      <th scope="col">%</th>
				      <th scope="col">Suma de Ventas</th>
				      <th scope="col">%</th>
				      <th scope="col">vs PM Gs.</th>
				      <th scope="col">vs PM %.</th>
				      <th scope="col">Cantidad Vendida</th>
				      <th scope="col">%</th>
				      <th scope="col">vs PM Qty.</th>
				      <th scope="col">vs PM %.</th>
				    </tr>
				  </thead>
				  <tbody>
				    <tr v-for="(marca, index) in response">
				      <th scope="row">{{index+1}}</th>
				      <td>{{marca.MARCA_NOMBRE}}</td>
				      <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(marca.STOCK_G)}}</td>
				      <td>
				      	<div class="progress">
		  					<div class="progress-bar" role="progressbar" v-bind:style="{ width: marca.P_STOCK + '%' }"   v-bind:aria-valuenow="marca.P_STOCK" aria-valuemax="100">
		  						{{marca.P_STOCK}}%
		  					</div>
						</div>
					  </td>
					  <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(marca.PRECIO)}}</td>
				      <td>
				      	<div class="progress">
		  					<div class="progress-bar" role="progressbar" v-bind:style="{ width: marca.P_TOTAL + '%' }"   aria-valuenow="marca.P_STOCK" aria-valuemin="0" aria-valuemax="100">
		  						{{marca.P_TOTAL}}%
		  					</div>
						</div>
					  </td>
					  <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(marca.PRECIO_ANTERIOR)}}</td>
					  <td>
					  	<div v-bind:class="classObject(marca.COMPORTAMIENTO_PRECIO)">
					  	{{marca.COMPORTAMIENTO_PRECIO}}
					  		<font-awesome-icon v-if="caret(marca.COMPORTAMIENTO_PRECIO)" icon="caret-up"/>
                        	<font-awesome-icon v-else icon="caret-down"/> 
                        </div> 
					  </td>
					  <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(marca.VENDIDO)}}</td>
				      <td>
				      	<div class="progress">
		  					<div class="progress-bar" role="progressbar" v-bind:style="{ width: marca.P_VENDIDO + '%' }"   aria-valuenow="marca.P_STOCK" aria-valuemin="0" aria-valuemax="100">
		  						{{marca.P_VENDIDO}}%
		  					</div>
						</div>
					  </td>
					  <td>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(marca.VENDIDO_ANTERIOR)}}</td>
					  <td>
					  	<div v-bind:class="classObject(marca.COMPORTAMIENTO_VENDIDO)">
					  	{{marca.COMPORTAMIENTO_VENDIDO}}
					  		<font-awesome-icon v-if="caret(marca.COMPORTAMIENTO_VENDIDO)" icon="caret-up"/>
                        	<font-awesome-icon v-else icon="caret-down"/> 
                        </div> 
					  </td>
				    </tr>
				  </tbody>
				  <tfoot>
					<tr>
						<th></th>
						<th>TOTALES</th>
						<th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(response.reduce((acc, item) => acc + item.STOCK_G, 0))}}</th>
						<th></th>
						<th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(response.reduce((acc, item) => acc + item.PRECIO, 0))}}</th>
						<th></th>
						<th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(response.reduce((acc, item) => acc + item.PRECIO_ANTERIOR, 0))}}</th>
						<th></th>
							<th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(response.reduce((acc, item) => acc + item.VENDIDO, 0))}}</th>
						<th></th>		
						<th>{{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(response.reduce((acc, item) => acc + item.VENDIDO_ANTERIOR, 0))}}</th>
						<th></th>	
					</tr>
						</tfoot>
				</table>
			</div>
	    </div>	  
	</div>
</template>
<script>
	export default {
        data(){
            return {
              	datos: {},
              	response: [],
              	title: '',
              	fecha: ''
            }
        }, 
        methods: {
	        llamarDatos(opcion){
	        	let me = this;
	        	this.response = [];
	        	var today = new Date();
				var yyyy = today.getFullYear();
				var dd = String(today.getDate()).padStart(2, '0');
				//var mm = String(today.getMonth() + 1).padStart(2, '0');
				var mm = today.getMonth();

				var months    = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Deciembre'];
				
				

				if (opcion === 1) {
					me.title = "Este Año ";
					this.fecha = yyyy;
					this.datos = {
		        		AllBrand: true,
		        		AllCategory: true,
		        		Inicio: yyyy+'-01-01',
		        		Final: yyyy+'-12-31',
		        		Sucursal: 4,
		        		Opcion: 2
	        		};
				} else if (opcion === 2) {
					this.fecha = months[mm];
					this.title = 'Este Mes';
					this.datos = {
		        		AllBrand: true,
		        		AllCategory: true,
		        		Inicio: yyyy+'-'+(mm+1)+'-01',
		        		Final: yyyy+'-'+(mm+1)+'-31',
		        		Sucursal: 4,
		        		Opcion: 2
	        		};
				} else if (opcion === 3) {
					me.title = "Mes Pasado ";
					if (mm === 0) {
						mm = 12;
						yyyy = yyyy - 1;
						this.fecha = months[mm-1];
					} else {
						this.fecha = months[mm-1];
					} 
					
					this.datos = {
		        		AllBrand: true,
		        		AllCategory: true,
		        		Inicio: yyyy+'-'+mm+'-01',
		        		Final: yyyy+'-'+mm+'-31',
		        		Sucursal: 4,
		        		Opcion: 2
	        		};
				}

				axios.post('/ventas', this.datos).then(function (response) {
						me.response = response.data.marcas;
				});
	        	
	        },
	        caret: function (dato) {
		          if (dato < 0) {
		            return false;
		          } else {
		            return true;
		          }  
	        },
	        classObject: function (dato) {
	          return {
	            'text-danger': dato < -60,
	            'text-warning': (dato < -0 && dato >= -60),
	            'text-success': dato > 0
	          }
	      	}
        },
        mounted() {
			this.llamarDatos(2);
        }
    }    
</script>