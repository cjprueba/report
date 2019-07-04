<template>
    <div class="container">
        <formv @new="agregarCategoria"></formv>
        <br>
        <categoria 
            v-for="(categoria, index) in categorias" 
            :key="categoria.id"
            :categoria="categoria"
            @delete="deleteCategoria(index)"
            @update="updateCategoria(index, ...arguments)">    
            </categoria>
    </div>
</template>

<script>
    export default {
        data(){
            return {
               categorias: []
            }
        }, 
        methods: {
            agregarCategoria(categoria){
                this.categorias.push(categoria);
            },
            updateCategoria(index, categoria){
                this.categorias[index] = categoria;
            },
            deleteCategoria(index){
                this.categorias.splice(index, 1);
            }
        },
        mounted() {
            axios.get('/categorias').then((response) => {
                this.categorias = response.data;
            });
        }
    }
</script>