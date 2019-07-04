<template>
    <div class="container">
        <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                  

                    <form action="" v-on:submit.prevent="nuevaCategoria()">
                        <div class="form-group">
                            <label for="thought">cual es el titulo?</label>
                            <input type="text" class="form-control" name="title" v-model="title">
                        </div>
                        <button type="submit" class="btn btn-primary">
                            Enviar Titulo
                        </button>
                    </form>

                </div>
            </div>
    </div>
</template>

<script>
    export default {
    	data(){
    		return {
    			title : ''
    		}
    	}, 
    	methods: {
    		listarCategoria(){
    			axios.get('/tasks')
			  	.then(function (response) {
			    // handle success
			  	  console.log(response);
			  	})
			  	.catch(function (error) {
			    // handle error
			    	console.log(error);
			  	});
    		},
            nuevaCategoria(){
                const params = {
                    description: this.title
                };
                axios.post('/categorias', params).then((response) => {
                    const categoria = response.data;
                     this.$emit('new', categoria);
                });
                
            }
    	},
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>
