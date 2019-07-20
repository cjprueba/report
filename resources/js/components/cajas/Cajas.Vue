<template>
	
        	<div class="row">

            <!-- PRIMERA CAJA -->

            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Dia {{caja1[0].sucursal}} </div>
                      <div v-if="cargado">
                        <div class="spinner-grow" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                      </div>
                      <div v-else v-bind:class="classObject(caja1[0].comportamientoDia)">
                        <div class="h5 mb-0 font-weight-bold">Gs. {{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(caja1[0].diaActual)}}</div>
                        <small>{{caja1[0].comportamientoDia}} % 
                          <font-awesome-icon v-if="caret(caja1[0].comportamientoDia)" icon="caret-up"/>
                          <font-awesome-icon v-else icon="caret-down"/> 
                        </small>
                      </div>
                    </div>
                    <div class="col-auto">
                       <!-- <font-awesome-icon icon="info-circle" class="fa-1x"  v-b-popover.hover="''+new Intl.NumberFormat('de-DE', {style: 'decimal', decimal: '0'}).format(caja1[0].mesActual)+''" title="Dia Anterior"/>   -->
                       <i class="fas fa-info-circle fa-1x text-gray-300" v-b-popover.hover="'Gs. '+new Intl.NumberFormat('de-DE', {style: 'decimal', decimal: '0'}).format(caja1[0].diaAnterior)+''" title="Dia Anterior"></i>  
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- SEGUNDA CAJA -->

            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Mes {{caja1[0].sucursal}} </div>
                      <div v-if="cargado" class="h5 mb-0 font-weight-bold text-primary-800">
                        <div class="spinner-grow" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                      </div>
                      <div v-else v-bind:class="classObject(caja1[0].comportamiento)">
                        <div class="h5 mb-0 font-weight-bold">Gs. {{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(caja1[0].mesActual)}}</div>
                        <small>{{caja1[0].comportamiento}} % 
                          <font-awesome-icon v-if="caret(caja1[0].comportamiento)" icon="caret-up"/>
                          <font-awesome-icon v-else icon="caret-down"/> 
                        </small>
                      </div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-info-circle fa-1x text-gray-300" v-b-popover.hover="'Gs. '+new Intl.NumberFormat('de-DE', {style: 'decimal', decimal: '0'}).format(caja1[0].mesAnterior)+''" title="Mes Anterior"></i>  
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- TERCERA CAJA -->

            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Año {{caja1[0].sucursal}} </div>
                      <div v-if="cargado" class="h5 mb-0 font-weight-bold text-primary-800">
                        <div class="spinner-grow" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                      </div>
                      <div v-else v-bind:class="classObject(caja1[0].comportamientoAnio)">
                        <div class="h5 mb-0 font-weight-bold">Gs. {{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(caja1[0].anioActual)}}</div>
                        <small>{{caja1[0].comportamientoAnio}} % 
                          <font-awesome-icon v-if="caret(caja1[0].comportamientoAnio)" icon="caret-up"/>
                          <font-awesome-icon v-else icon="caret-down"/> 
                        </small>
                      </div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-info-circle fa-1x text-gray-300" v-b-popover.hover="'Gs. '+new Intl.NumberFormat('de-DE', {style: 'decimal', decimal: '0'}).format(caja1[0].anioAnterior)+''" title="Año Anterior"></i>  
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- CUARTA CAJA -->

            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Anulados {{caja1[0].sucursal}} </div>
                      <div v-if="cargado" class="h5 mb-0 font-weight-bold text-primary-800">
                        <div class="spinner-grow" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                      </div>
                      <div v-else >
                        <div class="h5 mb-0 font-weight-bold">Gs. {{new Intl.NumberFormat("de-DE", {style: "decimal", decimal: "0"}).format(caja1[0].anuladoTotal)}}</div>
                        <small>Cant.: {{caja1[0].anulado}} </small>
                      </div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-comments fa-2x text-gray-300"></i>
                    </div>
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
          
        },
        classObject: function (dato) {
          return {
            'text-danger': dato < -60,
            'text-warning': (dato < -0 && dato > -60),
            'text-success': dato > 0
        }
      },
      caret: function (dato) {
          if (dato < 0) {
            return false;
          } else {
            return true;
          }  
      }
      },  
        mounted() {
            this.llamarVentas();
        }
    }
</script>
