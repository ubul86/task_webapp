<script setup>

import Home from "@/views/HomeComponent.vue";
</script>

<template>
    <v-container>
        <v-row>
            <v-col>
                <div>
                    <a href="https://vite.dev" target="_blank">
                        <img src="./assets/vite.svg" class="logo" alt="Vite logo" />
                    </a>
                    <a href="https://vuejs.org/" target="_blank">
                        <img src="./assets/vue.svg" class="logo vue" alt="Vue logo" />
                    </a>
                </div>
            </v-col>
        </v-row>
        <v-row>
            <v-col>
                <Home msg="Vite + Vue" />
                <v-btn color="primary">Button</v-btn>
            </v-col>
        </v-row>
    </v-container>



</template>

<style scoped>
.logo {
    height: 6em;
    padding: 1.5em;
    will-change: filter;
    transition: filter 300ms;
}
.logo:hover {
    filter: drop-shadow(0 0 2em #646cffaa);
}
.logo.vue:hover {
    filter: drop-shadow(0 0 2em #42b883aa);
}
</style>
