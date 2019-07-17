<template>
	<div class="container-fluid mt-4">
		<div class="mb-3">
			<label for="validationTooltip01">Seleccione Reporte</label>
			<select class="custom-select custom-select-sm" v-on:change="cambioReporte">
				<option selected>Seleccionar</option>
				<option value="1">Listar Marcas</option>
				<option value="2">Ventas por Marca</option>
				<option value="3">Ventas por Marca y Categoria</option>
			</select>			
		</div>

		<!-- VENTA POR MARCA -->
	<transition name="fade">	
		<div class="card" v-if="reporte === '2'" id="reporte1">
		  	<div class="card-header">VENTA POR MARCA</div>
			<div class="card-body">
			  	<div class="form-row">
			  		<div class="col-md-4 mb-3">
			  
					  	<label for="validationTooltip01">Seleccione Reporte</label>
						<select class="custom-select custom-select-sm">
							 <option selected>Seleccionar</option>
							 <option value="1">Listar Marcas</option>
							 <option value="2">Ventas por Marca</option>
							 <option value="3">Ventas por Marca y Categoria</option>
						</select>

						<label class="mt-3" for="validationTooltip01">Seleccione Reporte 2</label>
						<select class="custom-select custom-select-sm">
							 <option selected>Seleccionar</option>
							 <option value="1">Listar Marcas</option>
							 <option value="2">Ventas por Marca</option>
							 <option value="3">Ventas por Marca y Categoria</option>
						</select>
						
					</div>

					<div class="col-md-4">
						<label for="validationTooltip01">Seleccione Marcas</label> 
						<select multiple class="form-control" size="4">
						  <option selected>Open this select menu</option>
						  <option value="1">One</option>
						  <option value="2">Two</option>
						  <option value="3">Three</option>
						</select>
					</div>

				</div>
				<button class="btn btn-dark btn-sm" type="submit"><font-awesome-icon icon="download" /> Descargar</button>
			    <button class="btn btn-primary btn-sm" type="submit">Generar</button>
			</div>
		</div>
	</transition>
		<!-- FIN DE VENTA POR MARCA -->
	<transition name="slide-fade">	
		<venta-marca-categoria v-if="reporte === '3'" id="reporte2"></venta-marca-categoria>
	</transition>
		
	</div>
</template>

<script >
	export default {
        data(){
            return {
               reporte: '0'
            }
        }, 
        methods: {
            cambioReporte(e){
                this.reporte = e.target.value;
            }
        },
        mounted() {
        	
        }
    }    
</script>
<style>
	.bounce-enter-active {
  animation: bounce-in .5s;
}
.bounce-leave-active {
  animation: bounce-in .5s reverse;
}
@keyframes bounce-in {
  0% {
    transform: scale(0);
  }
  50% {
    transform: scale(1.5);
  }
  100% {
    transform: scale(1);
  }
}

.fade-enter-active, .fade-leave-active {
  transition: opacity .5s;
}
.fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
  opacity: 0;
}

.slide-fade-enter-active {
  transition: all .3s ease;
}
.slide-fade-leave-active {
  transition: all .8s cubic-bezier(1.0, 0.5, 0.8, 1.0);
}
.slide-fade-enter, .slide-fade-leave-to
/* .slide-fade-leave-active below version 2.1.8 */ {
  transform: translateX(10px);
  opacity: 0;
}
</style>