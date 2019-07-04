<template>
    <div class="container">
        <div class="card">
                <div class="card-header">Dashboard - fecha : {{categoria.created_at}} </div>

                <div class="card-body">
                    <div class="panel-heading">Descripcion</div>
                   

                    <div class="panel-body">
                    	<input v-if="editMode" type="text" name="" class="form-control" v-model="categoria.description">
                        <p v-else>
                            {{categoria.description}}
                        </p>

                    </div>
                    <div class="panel-footer">
                    	<button v-if="editMode" class="btn btn-default" v-on:click="onClickUpdate()">
                            Guardar Cambios
                        </button>
                        <button v-else class="btn btn-default" v-on:click="onClickEdit()">
                            Editar
                        </button>
                        <button class="btn btn-danger" v-on:click="onClickDelete()">
                            Eliminar
                        </button>
                    </div>     
                </div>
            </div>
    </div>
</template>

<script>
    export default {
    	props: ['categoria'],
    	data(){
    		return {
    			editMode: false
    		}
    	}, 
    	methods: {
    		onClickDelete(){
    			axios.delete(`categorias/${this.categoria.id}`).then((response) => {
    				this.$emit('delete');
    			});
    			
    		},
    		onClickEdit(){
    			this.editMode = true;
    		}, 
    		onClickUpdate(){
    			const params = {
    				description: this.categoria.description
    			};
    			axios.put(`categorias/${this.categoria.id}`, params).then((response) => {
    				this.editMode = false;
    				const categoria = response.data;
					this.$emit('update', categoria); 
    			});
    			   			
    		}
    	},
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>
