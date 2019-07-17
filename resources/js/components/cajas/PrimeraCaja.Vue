<template>
	
        	
                 <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Día {{caja1[0].SUCURSAL}} </div>
                      <div v-if="cargado" class="h5 mb-0 font-weight-bold text-primary-800">
                        <div class="spinner-grow" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                      </div>
                      <div v-else class="h5 mb-0 font-weight-bold text-gray-800">Gs. {{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(caja1[0].TOTAL)}}</div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-calendar fa-2x text-gray-300"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>

               
              
</template>

<style >
  
</style>

<script>
    export default {
     
      data(){
        return {
          caja1: [{
            TOTAL: 'Cargan...',
            ID_SUCURSAL: 0
          }],
          cargado: true
        }
      }, 
      methods: {
        llamarVentas(){
          axios.get('ventas/').then((response) => {
           this.caja1 = response.data;
           this.cargado = false;
           
          });
          
        }
      },  
        mounted() {
            this.llamarVentas();
        }
    }
</script>
